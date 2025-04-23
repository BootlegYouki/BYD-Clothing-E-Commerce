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
            // Live keys from environment variables
            $this->publicKey = $_ENV['PAYMONGO_PUBLIC_KEY_LIVE'];
            $this->secretKey = $_ENV['PAYMONGO_SECRET_KEY_LIVE'];
        } else {
            // Test keys from environment variables
            $this->publicKey = $_ENV['PAYMONGO_PUBLIC_KEY_TEST'];
            $this->secretKey = $_ENV['PAYMONGO_SECRET_KEY_TEST'];
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
            CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for testing
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
     * Create a checkout session (new PayMongo Checkout UI)
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
        
        // Prepare request data
        $requestData = [
            'data' => [
                'attributes' => [
                    'line_items' => $lineItems,
                    'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay'],
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'description' => $description,
                    'send_email_receipt' => false,
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
     * Create a payment link (legacy method - use createCheckoutSession instead)
     * 
     * @param float $amount Payment amount
     * @param string $description Payment description
     * @param array $metadata Additional metadata
     * @return array Payment link data
     * @deprecated Use createCheckoutSession instead
     */
    public function createPaymentLink($amount, $description, $metadata = []) {
        // Convert amount to cents (PayMongo requires amount in smallest currency unit)
        $amountInCents = round($amount * 100);
        
        // Create payment link request
        return $this->makeRequest('POST', 'links', [
            'data' => [
                'attributes' => [
                    'amount' => $amountInCents,
                    'description' => $description,
                    'remarks' => 'Order payment',
                    'currency' => 'PHP',
                    'metadata' => $metadata
                ]
            ]
        ]);
    }

    // REMOVE THIS DUPLICATE METHOD - It's already defined above
    // /**
    //  * Get payment intent details
    //  * 
    //  * @param string $id Payment intent ID
    //  * @return array Payment intent data
    //  */
    // public function getPaymentIntent($id) {
    //     return $this->makeRequest('GET', "payment_intents/$id");
    // }
    
    /**
     * Create a payment link with new tab flag
     * 
     * @param float $amount Payment amount
     * @param string $description Payment description
     * @param array $metadata Additional metadata
     * @return array Payment link data with new tab flag
     * @deprecated Use createCheckoutSession instead
     */
    public function createPaymentLinkNewTab($amount, $description, $metadata = []) {
        $paymentLink = $this->createPaymentLink($amount, $description, $metadata);
        $paymentLink['open_in_new_tab'] = true;
        return $paymentLink;
    }

    /**
     * Create a payment intent
     * 
     * @param float $amount Payment amount
     * @param array $metadata Additional metadata
     * @return array Payment intent data
     */
    public function createPaymentIntent($amount, $metadata = []) {
        // Convert amount to cents (PayMongo requires amount in smallest currency unit)
        $amountInCents = round($amount * 100);
        
        // Create payment intent request
        return $this->makeRequest('POST', 'payment_intents', [
            'data' => [
                'attributes' => [
                    'amount' => $amountInCents,
                    'payment_method_allowed' => ['card', 'paymaya', 'gcash'],
                    'payment_method_options' => [
                        'card' => ['request_three_d_secure' => 'any']
                    ],
                    'currency' => 'PHP',
                    'capture_type' => 'automatic',
                    'metadata' => $metadata
                ]
            ]
        ]);
    }

    /**
     * Create a webhook for payment events
     * 
     * @param string $url Webhook URL
     * @param array $events Events to listen for
     * @return array Webhook data
     */
    public function createWebhook($url, $events = ['payment.paid', 'payment.failed']) {
        return $this->makeRequest('POST', 'webhooks', [
            'data' => [
                'attributes' => [
                    'url' => $url,
                    'events' => $events
                ]
            ]
        ]);
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
            throw new Exception("Failed to create checkout URL: " . ($checkout['message'] ?? 'Unknown error'));
        }
    }
}