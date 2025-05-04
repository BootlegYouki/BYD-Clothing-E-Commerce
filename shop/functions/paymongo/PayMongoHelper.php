<?php
/**
 * PayMongo API Integration Helper
 * 
 * This class handles all interactions with the PayMongo payment gateway API.
 * It provides methods for creating payment links and retrieving payment information.
 */
class PayMongoHelper {
    private $secretKey;
    private $publicKey;
    
    /**
     * Constructor - initializes API keys
     * 
     * @param bool $isLive Whether to use live or test keys
     */
    public function __construct($isLive = false) {
        if ($isLive) {
            $this->publicKey = getEnvVar('PAYMONGO_PUBLIC_KEY_LIVE');
            $this->secretKey = getEnvVar('PAYMONGO_SECRET_KEY_LIVE');
        } else {
            $this->publicKey = getEnvVar('PAYMONGO_PUBLIC_KEY_TEST');
            $this->secretKey = getEnvVar('PAYMONGO_SECRET_KEY_TEST');
        }
    }

    /**
     * Make HTTP request to PayMongo API
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint API endpoint
     * @param array|null $data Request data
     * @return array Response data
     * @throws Exception If request fails
     */
    private function makeRequest($method, $endpoint, $data = null) {
        $url = "https://api.paymongo.com/v1/$endpoint";
        $ch = curl_init($url);
    
        // Set authorization headers
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($this->secretKey.':')
        ];
    
        // Configure cURL options
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true, // Disable SSL verification for testing
            CURLOPT_TIMEOUT => 30, // Add timeout to prevent hanging requests
        ]);
    
        // Add request body for POST/PUT requests
        if ($data) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            
            // Log request data for debugging
            error_log("PayMongo API Request to $url: $jsonData");
        }
    
        // Execute request and get response
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Log raw response for debugging
        error_log("PayMongo API Response ($httpCode): $response");
        
        // Close cURL session
        curl_close($ch);
        
        // Handle cURL errors
        if ($error) {
            error_log("PayMongo API cURL Error: $error");
            throw new Exception("API request failed: $error");
        }
        
        // Parse JSON response
        $decoded = json_decode($response, true);
        
        // Check for JSON parsing errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("PayMongo API JSON Error: " . json_last_error_msg() . " - Raw response: $response");
            throw new Exception("Invalid JSON response: " . json_last_error_msg());
        }
        
        // Check for API errors
        if ($httpCode >= 400) {
            $errorMsg = isset($decoded['errors']) && !empty($decoded['errors']) ? 
                $decoded['errors'][0]['detail'] : 'API request failed with status code: ' . $httpCode;
            error_log("PayMongo API Error: $errorMsg");
            throw new Exception($errorMsg);
        }
    
        return $decoded;
    }

    /**
     * Create a checkout session
     * 
     * @param float $amount Payment amount
     * @param string $description Payment description
     * @param array $metadata Additional data to store with payment
     * @param array $lineItems Line items for the checkout
     * @param array $customerInfo Customer information
     * @param string $successUrl URL to redirect after successful payment
     * @param string $cancelUrl URL to redirect after cancelled payment
     * @return array Checkout session data including checkout URL
     */
    public function createCheckoutSession($amount, $description, $metadata = [], $lineItems = [], $customerInfo = [], $successUrl = null, $cancelUrl = null) {
        // Convert amount to cents (PayMongo requires amount in smallest currency unit)
        $amountInCents = round($amount * 100);
        
        // If no line items provided, create a default one
        if (empty($lineItems)) {
            $lineItems = [
                [
                    'name' => 'Order Payment',
                    'quantity' => 1,
                    'amount' => $amountInCents,
                    'currency' => 'PHP'
                ]
            ];
        }
        
        // Set default success and cancel URLs if not provided
        if (!$successUrl) {
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $successUrl = $baseUrl . "/BYD-Clothing-E-Commerce-main/shop/payment-success.php";
        }
        
        if (!$cancelUrl) {
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $cancelUrl = $baseUrl . "/BYD-Clothing-E-Commerce-main/shop/payment-cancelled.php";
        }
        
        // Fix phone number format if it exists in customerInfo
        if (!empty($customerInfo) && isset($customerInfo['phone'])) {
            // Remove any leading 0 from the phone number
            $customerInfo['phone'] = preg_replace('/^0/', '', $customerInfo['phone']);
        }
        
        // Prepare request data
        $requestData = [
            'data' => [
                'attributes' => [
                    'line_items' => $lineItems,
                    'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay', 'qrph'],
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'description' => $description,
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'show_line_items' => true
                ]
            ]
        ];
        
        // Add metadata if provided
        if (!empty($metadata)) {
            $requestData['data']['attributes']['metadata'] = $metadata;
        }
        
        // Add customer info if provided
        if (!empty($customerInfo)) {
            $requestData['data']['attributes']['billing'] = $customerInfo;
        }
        
        // Make API request to create checkout session
        $response = $this->makeRequest('POST', 'checkout_sessions', $requestData);
        
        // Extract checkout URL and session ID from response
        $checkoutUrl = $response['data']['attributes']['checkout_url'] ?? null;
        $sessionId = $response['data']['id'] ?? null;
        
        if (!$checkoutUrl || !$sessionId) {
            error_log("Invalid checkout response: " . json_encode($response));
            throw new Exception("Failed to create checkout session: Invalid response");
        }
        
        return [
            'checkout_url' => $checkoutUrl,
            'session_id' => $sessionId
        ];
    }

    /**
     * Get checkout session details
     * 
     * @param string $sessionId Checkout session ID
     * @return array Checkout session data
     */
    public function getCheckoutSession($sessionId) {
        // Make API request to get checkout session
        return $this->makeRequest('GET', 'checkout_sessions/' . $sessionId);
    }
    
    /**
     * Get payment intent details
     * 
     * @param string $paymentId Payment intent ID
     * @return array Payment intent data
     */
    public function getPaymentIntent($paymentId) {
        // Check if this is a checkout session ID (starts with 'cs_')
        if (strpos($paymentId, 'cs_') === 0) {
            // This is a checkout session ID, not a payment intent ID
            $checkoutData = $this->getCheckoutSession($paymentId);
            
            // Log the checkout data for debugging
            error_log("Retrieved checkout session: " . json_encode($checkoutData));
            
            // Return the checkout data in a format similar to payment intent
            return [
                'data' => [
                    'attributes' => [
                        'status' => $checkoutData['data']['attributes']['payment_intent']['status'] ?? 'unknown',
                        'amount' => $checkoutData['data']['attributes']['payment_intent']['amount'] ?? 0,
                        'payment_method_used' => $checkoutData['data']['attributes']['payment_method_used'] ?? 'unknown',
                        'payment_intent_id' => $checkoutData['data']['attributes']['payment_intent']['id'] ?? null
                    ]
                ]
            ];
        }
        
        // This is a regular payment intent ID
        return $this->makeRequest('GET', "payment_intents/$paymentId");
    }
    
    /**
     * Expire a checkout session
     * 
     * @param string $id Checkout session ID
     * @return array Response data
     */
    public function expireCheckoutSession($id) {
        $response = $this->makeRequest('POST', "checkout_sessions/$id/expire");
        
        return [
            'success' => isset($response['data']['attributes']['status']) && $response['data']['attributes']['status'] === 'expired',
            'data' => $response
        ];
    }

    /**
     * Get checkout URL from a checkout session
     * 
     * @param float $amount Payment amount
     * @param string $description Payment description
     * @param array $metadata Additional metadata
     * @return string Checkout URL
     */
    public function getCheckoutUrl($amount, $description, $metadata = []) {
        $checkout = $this->createCheckoutSession($amount, $description, $metadata);
        
        if ($checkout['success'] && isset($checkout['checkout_url'])) {
            return $checkout['checkout_url'];
        } else {
        }
    }
}