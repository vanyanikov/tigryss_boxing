<?php

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "tigryss_boxing";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    http_response_code(500);
    exit("Помилка підключення до бази: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
