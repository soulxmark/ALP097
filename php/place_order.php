<?php
/* ============================================================
   Casa De Manila — Place Order API
   File: place_order.php
   Called via fetch() POST from menu.js
   Expects JSON: { items: [{name, price, quantity, menu_id}], total }
============================================================ */
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$data  = json_decode(file_get_contents('php://input'), true);
$items = $data['items'] ?? [];
$total = (float)($data['total'] ?? 0);
$notes = trim($data['notes'] ?? '');
$uid   = (int)$_SESSION['uid'];

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

$mysqli->begin_transaction();

try {
    // Insert order
    $stmt_o = $mysqli->prepare(
        "INSERT INTO orders_tbl (uid, total_amount, notes) VALUES (?, ?, ?)"
    );
    $stmt_o->bind_param("ids", $uid, $total, $notes);
    $stmt_o->execute();
    $order_id = $mysqli->insert_id;
    $stmt_o->close();

    // Insert items
    $stmt_i = $mysqli->prepare(
        "INSERT INTO order_items_tbl (order_id, menu_id, item_name, price, quantity, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    foreach ($items as $item) {
        $menu_id  = !empty($item['menu_id']) ? (int)$item['menu_id'] : null;
        $name     = substr(trim($item['name']), 0, 100);
        $price    = (float)$item['price'];
        $qty      = (int)$item['quantity'];
        $subtotal = $price * $qty;
        $stmt_i->bind_param("iisdid", $order_id, $menu_id, $name, $price, $qty, $subtotal);
        $stmt_i->execute();
    }
    $stmt_i->close();

    $mysqli->commit();

    echo json_encode([
        'success'  => true,
        'order_id' => $order_id,
        'message'  => 'Order placed successfully!'
    ]);

} catch (Exception $e) {
    $mysqli->rollback();
    error_log('Order error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to place order. Try again.']);
}