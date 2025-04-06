<?php
// Load environment variables
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
        }
    }
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Prevents buffering for Nginx

// Get API key from environment variable
$api_key = getenv('OPENROUTER_API_KEY');

if (!$api_key) {
    echo "data: {\"error\":\"API key not configured on server\"}\n\n";
    exit;
}

// Rest of your existing code
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

curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
    echo $data;
    flush();
    return strlen($data);
});

curl_exec($ch);
curl_close($ch);
?>