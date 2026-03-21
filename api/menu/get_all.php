<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../connection.php';

$category = trim($_GET['category'] ?? '');

if ($category) {
  $stmt = $mysqli->prepare(
    "SELECT menu_id AS _id, name, description, price, category, image
     FROM menu_tbl WHERE is_available = 1 AND category = ? ORDER BY menu_id"
  );
  $stmt->bind_param("s", $category);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $mysqli->query(
    "SELECT menu_id AS _id, name, description, price, category, image
     FROM menu_tbl WHERE is_available = 1 ORDER BY menu_id"
  );
}

$items = [];
while ($row = $result->fetch_assoc()) {
  $items[] = $row;
}

echo json_encode(['success' => true, 'items' => $items]);