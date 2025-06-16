<?php

date_default_timezone_set('UTC'); // optional: set your timezone

$botToken = '6627263483:AAG5WQX0ha9hsx740CwSUtkMjwDONp0Eh_w';
$chatId   = '5248818941';

// Log file path (auto-creates if not exists)
$logFile = __DIR__ . '/relay-log.txt';

function log_debug($entry) {
    global $logFile;
    file_put_contents($logFile, "[" . date("Y-m-d H:i:s") . "] $entry\n", FILE_APPEND);
}

// Accept POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    log_debug("Denied: Non-POST request");
    exit("Method Not Allowed");
}

$text   = trim($_POST['text'] ?? '');
$source = trim($_POST['source'] ?? 'unknown');

if (empty($text)) {
    http_response_code(400);
    log_debug("Missing text from source: $source");
    exit("Missing message text.");
}

$finalMessage = $text . "\n\n📡 *Relayed From:* `{$source}`";

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$payload = [
    'chat_id' => $chatId,
    'text'    => $finalMessage,
    'parse_mode' => 'Markdown'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error    = curl_error($ch);
curl_close($ch);

// Log outcome
if ($response) {
    log_debug("✅ Relayed from {$source} | Response: $response");
    echo "OK";
} else {
    log_debug("❌ Failed from {$source} | cURL Error: $error");
    echo "Fail";
}
?>
