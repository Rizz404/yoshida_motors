<?php
$secret = 'b670f960-4ff5-54b8-8c94-0f75297c86e6';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($expected, $signature)) {
  http_response_code(403);
  exit('Forbidden');
}

$plesk_webhook = 'https://localhost:8443/modules/git/public/web-hook.php?uuid=2a4606ee-d30e-9ae7-482c-ffd4c02adfe1';

$ch = curl_init($plesk_webhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$result = curl_exec($ch);
curl_close($ch);

http_response_code(200);
echo 'OK';
