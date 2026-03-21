<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../connection.php';

if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Not logged in.']);
  exit;
}

$data  = json_decode(file_get_contents('php://input'), true);
$items = $data['items'] ?? [];
$total = (float)($data['total'] ?? 0);
$notes = trim($data['notes'] ?? '');
$uid   = (int)$_SESSION['uid'];

if (empty($items) || $total <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
  exit;
}

$mysqli->begin_transaction();
try {
  $stmt = $mysqli->prepare(
    "INSERT INTO orders_tbl (uid, total_amount, notes) VALUES (?, ?, ?)"
  );
  $stmt->bind_param("ids", $uid, $total, $notes);
  $stmt->execute();
  $order_id = $mysqli->insert_id;
  $stmt->close();

  $stmt = $mysqli->prepare(
    "INSERT INTO order_items_tbl (order_id, menu_id, item_name, price, quantity, subtotal)
     VALUES (?, ?, ?, ?, ?, ?)"
  );
  foreach ($items as $item) {
    $menu_id  = !empty($item['menu_id']) ? (int)$item['menu_id'] : null;
    $name     = substr(trim($item['name']), 0, 100);
    $price    = (float)$item['price'];
    $qty      = (int)$item['quantity'];
    $subtotal = $price * $qty;
    $stmt->bind_param("iisdid", $order_id, $menu_id, $name, $price, $qty, $subtotal);
    $stmt->execute();
  }
  $stmt->close();

  $mysqli->commit();
  echo json_encode(['success' => true, 'order_id' => $order_id, 'message' => 'Order placed!']);
} catch (Exception $e) {
  $mysqli->rollback();
  echo json_encode(['success' => false, 'message' => 'Failed to place order. Try again.']);
}