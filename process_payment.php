<?php
session_start();
require_once './connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1 || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true);
$id     = (int)($data['order_id'] ?? 0);
$status = trim($data['status']   ?? 'completed');

$allowed = ['completed', 'cancelled', 'preparing', 'ready', 'confirmed', 'pending'];
if (!$id || !in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

$stmt = $mysqli->prepare("UPDATE orders_tbl SET status = ? WHERE order_id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $mysqli->error]);
}
$stmt->close();
?>