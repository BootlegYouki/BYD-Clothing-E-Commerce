<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Prevents buffering for Nginx

// Store API key securely on the server
$api_key = "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";

// Get the incoming request
$input = json_decode(file_get_contents('php://input'), true);
$model = $input['model'] ?? '';
$messages = $input['messages'] ?? [];

// Initialize cURL
$ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => $model,
    'messages' => $messages,
    'stream' => true
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key,
    'HTTP-Referer: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
    'X-Title: BYD Clothing Assistant'
]);

// Stream the response directly
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
    echo $data;
    flush();
    return strlen($data);
});

curl_exec($ch);
curl_close($ch);
?>