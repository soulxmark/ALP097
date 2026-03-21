<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../connection.php';

if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1) {
  echo json_encode(['success' => false, 'message' => 'Not logged in.']);
  exit;
}

$uid  = (int)$_SESSION['uid'];
$stmt = $mysqli->prepare(
  "SELECT o.order_id, o.order_date, o.total_amount, o.status, o.notes,
          oi.item_name, oi.price, oi.quantity, oi.subtotal
   FROM orders_tbl o
   LEFT JOIN order_items_tbl oi ON o.order_id = oi.order_id
   WHERE o.uid = ? ORDER BY o.order_date DESC"
);
$stmt->bind_param("i", $uid);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$orders = [];
foreach ($rows as $row) {
  $oid = $row['order_id'];
  if (!isset($orders[$oid])) {
    $orders[$oid] = [
      'order_id'     => $oid,
      'order_date'   => $row['order_date'],
      'total_amount' => $row['total_amount'],
      'status'       => $row['status'],
      'notes'        => $row['notes'],
      'items'        => [],
    ];
  }
  if ($row['item_name']) {
    $orders[$oid]['items'][] = [
      'name'     => $row['item_name'],
      'price'    => $row['price'],
      'quantity' => $row['quantity'],
      'subtotal' => $row['subtotal'],
    ];
  }
}

echo json_encode(['success' => true, 'orders' => array_values($orders)]);