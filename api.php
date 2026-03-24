<?php
session_start();
require_once './connection.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($action) {

  case 'me':
    if (isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1) {
      echo json_encode(['success' => true, 'user' => [
        'uid'      => $_SESSION['uid'],
        'username' => $_SESSION['username'],
        'role'     => $_SESSION['role'] ?? 'user',
      ]]);
    } else {
      echo json_encode(['success' => false]);
    } 
    break;

  case 'login':
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');
    if (!$username || !$password) {
      echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
      break;
    }
    $stmt = $mysqli->prepare("SELECT uid, username, password_us, role FROM users_tbl1 WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row || !password_verify($password, $row['password_us'])) {
      echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
      break;
    }
    $_SESSION['uid']            = $row['uid'];
    $_SESSION['username']       = $row['username'];
    $_SESSION['role']           = $row['role'];
    $_SESSION['session_status'] = 1;
    echo json_encode(['success' => true, 'user' => [
      'uid' => $row['uid'], 'username' => $row['username'], 'role' => $row['role']
    ]]);
    break;

  case 'logout':
    session_unset();
    session_destroy();
    echo json_encode(['success' => true]);
    break;

  case 'register':
    $username = trim($data['username'] ?? '');
    $email    = trim($data['email']    ?? '');
    $password = trim($data['password'] ?? '');
    if (!$username || !$email || !$password) {
      echo json_encode(['success' => false, 'message' => 'All fields are required.']);
      break;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
      break;
    }
    if (strlen($password) < 6) {
      echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
      break;
    }
    $stmt = $mysqli->prepare("SELECT uid FROM users_tbl1 WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      echo json_encode(['success' => false, 'message' => 'Username or email already taken.']);
      $stmt->close();
      break;
    }
    $stmt->close();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO users_tbl1 (username, email, password_us) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hash);
    echo $stmt->execute()
      ? json_encode(['success' => true,  'message' => 'Account created! You can now log in.'])
      : json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    $stmt->close();
    break;

  case 'menu':
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
    while ($row = $result->fetch_assoc()) $items[] = $row;
    echo json_encode(['success' => true, 'items' => $items]);
    break;

  case 'place_order':
    if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => 'Not logged in.']);
      break;
    }
    $items = $data['items'] ?? [];
    $total = (float)($data['total'] ?? 0);
    $notes = trim($data['notes'] ?? '');
    $uid   = (int)$_SESSION['uid'];
    if (empty($items) || $total <= 0) {
      echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
      break;
    }
    $mysqli->begin_transaction();
    try {
      $stmt = $mysqli->prepare("INSERT INTO orders_tbl (uid, total_amount, notes) VALUES (?, ?, ?)");
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
    break;

  case 'my_orders':
    if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1) {
      echo json_encode(['success' => false, 'message' => 'Not logged in.']);
      break;
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
    break;

  default:
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
}