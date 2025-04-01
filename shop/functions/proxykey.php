<?php
header('Content-Type: application/json');

// Verify the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get the request body
$requestData = json_decode(file_get_contents('php://input'), true);

$apiKey = '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';

// Create request to OpenRouter API
$ch = curl_init('https://openrouter.ai/api/v1/chat/completions');

// Build the current host dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
$host = $protocol . $_SERVER['HTTP_HOST'];

// Set CURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'HTTP-Referer: ' . $host,
    'X-Title: BYD Clothing Assistant',
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

// Forward the response to the client
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Add better error handling
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Forward status code
http_response_code($httpCode);

// Return the API error for debugging
if ($httpCode >= 400) {
    // Log the error for server-side debugging
    error_log('OpenRouter API Error: ' . $response);
}

echo $response;
?>