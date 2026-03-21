<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../connection.php';

$data     = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (!$username || !$password) {
  echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
  exit;
}

$stmt = $mysqli->prepare(
  "SELECT uid, username, password_us, role FROM users_tbl1 WHERE username = ? LIMIT 1"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row || !password_verify($password, $row['password_us'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
  exit;
}

$_SESSION['uid']            = $row['uid'];
$_SESSION['username']       = $row['username'];
$_SESSION['role']           = $row['role'];
$_SESSION['session_status'] = 1;

echo json_encode([
  'success' => true,
  'user'    => [
    'uid'      => $row['uid'],
    'username' => $row['username'],
    'role'     => $row['role'],
  ]
]);