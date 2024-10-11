<?php
header('Content-Type: application/json');

$results = []; // آرایه‌ای برای نگه‌داری نتایج
$hosts = isset($_POST['names']) ? explode("\n", trim($_POST['names'])) : [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($hosts as $host) {
        if (!preg_match('/^https?:\/\//', $host)) {
            $domain = 'http://' . trim($host);
        } else {
            $domain = trim($host);
        }

        $startTime = microtime(true);

        $ch = curl_init($domain);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseTime = microtime(true) - $startTime;

        $results[] = [
            'domain' => $domain,
            'status' => ($httpCode >= 200 && $httpCode < 400) ? 'up' : 'down',
            'responseTime' => round($responseTime * 1000, 2) // زمان پاسخ به میلی‌ثانیه
        ];

        curl_close($ch);
    }
    
    echo json_encode($results);
}
?>
