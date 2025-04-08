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
        // Set your test keys here
        $this->publicKey = 'pk_test_WuLdYroE1TcYB1y49qVXnuQm';
        $this->secretKey = 'sk_test_WuLdYroE1TcYB1y49qVXnuQm';
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
            CURLOPT_SSL_VERIFYPEER => false, // Added for testing
        ]);

        // Add request body for POST/PUT requests
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // Execute request and get response
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        // Handle cURL errors
        if ($error) {
            throw new Exception("cURL Error: $error");
        }

        // Parse JSON response
        $decoded = json_decode($response, true);
        
        // Handle API errors
        if ($httpCode >= 400) {
            $errorMsg = isset($decoded['errors']) && !empty($decoded['errors']) ? 
                $decoded['errors'][0]['detail'] : 'API request failed: ' . $response;
            throw new Exception($errorMsg);
        }

        return $decoded;
    }

    /**
     * Create a payment link
     * 
     * @param float $amount Payment amount
     * @param string $description Payment description
     * @param array $metadata Additional metadata
     * @return array Payment link data
     */
    public function createPaymentLink($amount, $description, $metadata = []) {
        // Convert amount to cents (PayMongo requires amount in smallest currency unit)
        $amountInCents = round($amount * 100); // Convert to cents and ensure it's an integer
        
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

    /**
     * Get payment intent details
     * 
     * @param string $id Payment intent ID
     * @return array Payment intent data
     */
    public function getPaymentIntent($id) {
        return $this->makeRequest('GET', "payment_intents/$id");
    }
}