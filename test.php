<?php
require_once __DIR__ . '/db.php';
header('Content-Type: text/plain; charset=utf-8');
echo "DB OK\n";

$ping = $conn->query("SELECT NOW() AS now_time")->fetch_assoc();
echo "NOW(): " . $ping['now_time'] . "\n";
