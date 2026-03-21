<?php
session_start();
header('Content-Type: application/json');

if (
  isset($_SESSION['session_status']) &&
  $_SESSION['session_status'] == 1 &&
  isset($_SESSION['uid'])
) {
  echo json_encode([
    'success' => true,
    'user'    => [
      'uid'      => $_SESSION['uid'],
      'username' => $_SESSION['username'],
      'role'     => $_SESSION['role'] ?? 'user',
    ]
  ]);
} else {
  echo json_encode(['success' => false]);
}