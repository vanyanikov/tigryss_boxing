<?php
// save_order.php
header('Content-Type: application/json');

// Подключение к БД
require_once 'db.php';

// Читаем входящие данные
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['first_name']) || empty($data['last_name']) || empty($data['phone']) || empty($data['city']) || empty($data['department']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
    exit;
}

$firstName = $data['first_name'];
$lastName = $data['last_name'];
$patronymic = $data['patronymic'] ?? '';
$phone = $data['phone'];
$city = $data['city'];
$department = $data['department'];
$userId = $data['user_id'] ?? null; // Если пользователь авторизован
$items = $data['items'];

try {
    // Отключаем автокоммит для транзакции
    $conn->begin_transaction();

    // Сохраняем заказ
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, first_name, last_name, patronymic, phone, city, department, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issssss", $userId, $firstName, $lastName, $patronymic, $phone, $city, $department);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();

    // Сохраняем товары заказа
    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($items as $item) {
        $productId = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $itemStmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
        $itemStmt->execute();
    }
    $itemStmt->close();

    // Завершаем транзакцию
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Заказ успешно оформлен!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Ошибка сохранения заказа: ' . $e->getMessage()]);
}

$conn->close();
?>
