<?php
$secret = 'b670f960-4ff5-54b8-8c94-0f75297c86e6';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($expected, $signature)) {
  http_response_code(403);
  exit('Forbidden');
}

// Menggunakan 127.0.0.1 lebih aman daripada localhost untuk bypass IPv6
$plesk_webhook = 'https://127.0.0.1:8443/modules/git/public/web-hook.php?uuid=2a4606ee-d30e-9ae7-482c-ffd4c02adfe1';

$ch = curl_init($plesk_webhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Matikan validasi SSL secara total (Peer & Host)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$result = curl_exec($ch);

// Catat error ke file log PHP jika curl gagal tembus ke Plesk
if ($result === false) {
  error_log('Curl error webhook Plesk: ' . curl_error($ch));
}

// Cukup hapus curl_close($ch) karena CurlHandle akan otomatis tertutup

http_response_code(200);
echo 'OK';
