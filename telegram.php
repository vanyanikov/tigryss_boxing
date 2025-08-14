<?php
require_once __DIR__ . '/telegram_config.php';

function sendTelegramMessage(string $text): array {
    global $TELEGRAM_BOT_TOKEN, $TELEGRAM_CHAT_ID;

    if (!$TELEGRAM_BOT_TOKEN || !$TELEGRAM_CHAT_ID || $TELEGRAM_BOT_TOKEN === 'YOUR_TELEGRAM_BOT_TOKEN') {
        return ['ok' => false, 'error' => 'Не задан TELEGRAM_BOT_TOKEN/CHAT_ID'];
    }

    $url  = "https://api.telegram.org/bot{$TELEGRAM_BOT_TOKEN}/sendMessage";
    $data = [
        'chat_id' => $TELEGRAM_CHAT_ID,
        'text'    => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query($data)
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['ok' => false, 'error' => $err];
    }
    $json = json_decode($resp, true);
    return is_array($json) ? $json : ['ok' => false, 'error' => 'Bad JSON'];
}
