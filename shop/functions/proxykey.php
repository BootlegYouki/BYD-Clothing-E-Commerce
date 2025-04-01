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

// API key is stored on the server, not exposed to client
$apiKey = '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';

// Create request to OpenRouter API
$ch = curl_init('https://openrouter.ai/api/v1/chat/completions');

// Set CURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'HTTP-Referer: http://localhost',
    'X-Title: Beyond Doubt Clothing',
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

// Forward the response to the client
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Forward status code
http_response_code($httpCode);
echo $response;
?>