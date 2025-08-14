<?php
// my_orders.php — вывод таблицы заказов
require_once __DIR__ . '/db.php';

// Пагинация
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

// Поиск по телефону/имени/товару (опц.)
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = '';
$params = [];
$types  = '';
if ($q !== '') {
    $where = " WHERE (name LIKE ? OR phone LIKE ? OR product LIKE ?)";
    $like  = '%' . $q . '%';
    $params = [$like, $like, $like];
    $types  = 'sss';
}

// Всего записей
if ($where) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders {$where}");
    $stmt->bind_param($types, ...$params);
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders");
}
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$total_pages = max(1, (int)ceil($total / $limit));

// Выборка текущей страницы
$sql = "SELECT id, created_at, name, phone, product, variant, size, quantity, price, notes
        FROM orders
        " . $where . "
        ORDER BY id DESC
        LIMIT ? OFFSET ?";
if ($where) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types . "ii", ...$params, $limit, $offset);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Мої замовлення</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background:#f7f7f7; }
    h1 { margin-bottom: 14px; }
    form { margin-bottom: 16px; display:flex; gap:8px; }
    input[type="text"] { flex:1; padding:8px 10px; }
    button, a.btn { padding:8px 12px; text-decoration:none; color:#fff; background:#333; border-radius:6px; border:0; cursor:pointer; }
    a.btn { display:inline-block; }
    table { width: 100%; border-collapse: collapse; background:#fff; }
    th, td { border-bottom: 1px solid #eee; padding: 8px 10px; text-align: left; }
    th { background:#fafafa; }
    .meta { color:#666; font-size:12px; }
    .pager { margin-top:16px; display:flex; gap:8px; align-items:center; }
  </style>
</head>
<body>
  <h1>Мої замовлення</h1>

  <div style="margin-bottom:10px">
    <a class="btn" href="index.html">← На головну</a>
  </div>

  <form method="get">
    <input type="text" name="q" placeholder="Пошук: імʼя, телефон або товар" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Шукати</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Дата</th>
        <th>Імʼя</th>
        <th>Телефон</th>
        <th>Товар</th>
        <th>Варіант</th>
        <th>Розмір</th>
        <th>К-сть</th>
        <th>Ціна</th>
        <th>Коментар</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td>#<?= (int)$row['id'] ?></td>
          <td class="meta"><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($row['product'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($row['variant'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($row['size'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= (int)$row['quantity'] ?></td>
          <td><?= number_format((float)$row['price'], 2, '.', ' ') ?></td>
          <td><?= nl2br(htmlspecialchars($row['notes'] ?? '', ENT_QUOTES, 'UTF-8')) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="pager">
    <?php if ($page > 1): ?>
      <a class="btn" href="?q=<?= urlencode($q) ?>&page=<?= $page-1 ?>">← Попередня</a>
    <?php endif; ?>
    <span>Сторінка <?= $page ?> з <?= $total_pages ?></span>
    <?php if ($page < $total_pages): ?>
      <a class="btn" href="?q=<?= urlencode($q) ?>&page=<?= $page+1 ?>">Наступна →</a>
    <?php endif; ?>
  </div>
</body>
</html>
<?php
$stmt->close();
