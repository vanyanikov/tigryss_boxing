<?php
// === КОНФІГУРАЦІЯ ===
$token = '7567416481:AAF48p4yWJPPbRNOal-x3_a_QkY68sOr-84';
$chat_id = '1942917071';

// === ДАНІ З ФОРМИ ===
$name = $_POST['name'];
$surname = $_POST['surname'];
$patronymic = $_POST['patronymic'];
$phone = $_POST['phone'];
$city = $_POST['city'];
$department = $_POST['department'];
$items = $_POST['items'];
$no_call = isset($_POST['no_call']) ? 'Так' : 'Ні';

// === ФОРМУЄМО ПОВІДОМЛЕННЯ ===
$message = "🛒 НОВЕ ЗАМОВЛЕННЯ\n"
  . "Ім'я: $name\n"
  . "Прізвище: $surname\n"
  . "По батькові: $patronymic\n"
  . "Телефон: $phone\n"
  . "Місто: $city\n"
  . "Відділення: $department\n"
  . "Замовлено: $items\n"
  . "Без дзвінка: $no_call";

// === ВІДПРАВКА В TELEGRAM ===
$url = "https://api.telegram.org/bot$token/sendMessage";
$data = [
  'chat_id' => $chat_id,
  'text' => $message,
  'parse_mode' => 'HTML'
];

// CURL-запит
$options = [
  'http' => [
    'method' => 'POST',
    'header' => 'Content-Type: application/x-www-form-urlencoded',
    'content' => http_build_query($data)
  ]
];
$context = stream_context_create($options);
file_get_contents($url, false, $context);

// === ВІДПРАВКА ВІДПОВІДІ САЙТУ ===
header('Location: index.html'); // або дякуємо.html
?>
