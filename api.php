<?php
session_start();
require_once './connection.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

// ── Google Apps Script URL (your existing one) ────────────────────────────
// After adding the OTP code to your Apps Script and redeploying,
// paste the NEW deployment URL here:
define('GAS_URL', 'https://script.google.com/macros/s/AKfycbwstUXUIOjo0ULyVO9auDJSAOpJcJokQGt9TudxmlS0GkJvSjRjLQw4KNbhBzZ5WatfaQ/exec');

// ── Send OTP via Google Apps Script (free, no SMTP needed) ───────────────
function sendOTPviaGAS($to, $otp) {
    $url      = GAS_URL . '?action=send_otp&email=' . urlencode($to) . '&otp=' . urlencode($otp);
    $response = @file_get_contents($url);
    if (!$response) return false;
    $result = json_decode($response, true);
    return isset($result['success']) && $result['success'] === true;
}

switch ($action) {

  // ── Step 1: Check credentials only — don't log in yet ────────────────────
  case 'check_credentials':
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (!$username || !$password) {
      echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
      break;
    }

    $stmt = $mysqli->prepare("SELECT uid, username, email, password_us, role FROM users_tbl1 WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || !password_verify($password, $row['password_us'])) {
      echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
      break;
    }

    // Store pending login — not fully logged in yet
    $_SESSION['pending_uid']      = $row['uid'];
    $_SESSION['pending_username'] = $row['username'];
    $_SESSION['pending_email']    = $row['email'];
    $_SESSION['pending_role']     = $row['role'];

    echo json_encode(['success' => true, 'email' => $row['email']]);
    break;

  // ── Step 2: Generate OTP and send via Google Apps Script ─────────────────
  case 'send_otp':
    $email = trim($data['email'] ?? '');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['success' => false, 'message' => 'Valid email is required.']);
      break;
    }

    $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Save OTP in session
    $_SESSION['otp_code']    = $otp;
    $_SESSION['otp_email']   = $email;
    $_SESSION['otp_expires'] = $expires;

    // Debug mode — shows exactly what Google Apps Script returns
    $gas_url      = GAS_URL . '?action=send_otp&email=' . urlencode($email) . '&otp=' . urlencode($otp);
    $gas_response = @file_get_contents($gas_url);
    $gas_result   = json_decode($gas_response, true);

    if ($gas_result && !empty($gas_result['success'])) {
      echo json_encode(['success' => true, 'message' => 'OTP sent to your email.']);
    } else {
      echo json_encode([
        'success'      => false,
        'message'      => 'Failed to send OTP.',
        'gas_raw'      => $gas_response,
        'gas_parsed'   => $gas_result,
        'gas_url'      => $gas_url,
      ]);
    }
    break;

  // ── Step 3: Verify OTP and complete login ─────────────────────────────────
  case 'verify_otp_login':
    $email   = trim($data['email'] ?? '');
    $entered = trim($data['otp']   ?? '');

    $stored_otp     = $_SESSION['otp_code']    ?? '';
    $stored_email   = $_SESSION['otp_email']   ?? '';
    $stored_expires = $_SESSION['otp_expires'] ?? '';
    $pending_uid    = $_SESSION['pending_uid'] ?? null;

    if (!$stored_otp || !$pending_uid) {
      echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
      break;
    }

    if (strtotime($stored_expires) < time()) {
      unset($_SESSION['otp_code'], $_SESSION['otp_email'], $_SESSION['otp_expires']);
      echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
      break;
    }

    if ($email !== $stored_email || $entered !== $stored_otp) {
      echo json_encode(['success' => false, 'message' => 'Incorrect OTP. Please try again.']);
      break;
    }

    // Save before unsetting
    $final_uid      = (int)$_SESSION['pending_uid'];
    $final_username = $_SESSION['pending_username'];
    $final_role     = $_SESSION['pending_role'];

    // Clear OTP + pending data
    unset(
      $_SESSION['otp_code'],      $_SESSION['otp_email'],    $_SESSION['otp_expires'],
      $_SESSION['pending_uid'],   $_SESSION['pending_username'],
      $_SESSION['pending_email'], $_SESSION['pending_role']
    );

    // Full login
    $_SESSION['uid']            = $final_uid;
    $_SESSION['username']       = $final_username;
    $_SESSION['role']           = $final_role;
    $_SESSION['session_status'] = 1;

    echo json_encode(['success' => true]);
    break;

  // ── Standalone OTP verify (e.g. reservation confirmation) ────────────────
  case 'verify_otp':
    $email   = trim($data['email'] ?? '');
    $entered = trim($data['otp']   ?? '');

    $stored_otp     = $_SESSION['otp_code']    ?? '';
    $stored_email   = $_SESSION['otp_email']   ?? '';
    $stored_expires = $_SESSION['otp_expires'] ?? '';

    if (!$stored_otp) {
      echo json_encode(['success' => false, 'message' => 'No OTP found. Please request a new one.']);
      break;
    }
    if (strtotime($stored_expires) < time()) {
      unset($_SESSION['otp_code'], $_SESSION['otp_email'], $_SESSION['otp_expires']);
      echo json_encode(['success' => false, 'message' => 'OTP expired.']);
      break;
    }
    if ($email !== $stored_email || $entered !== $stored_otp) {
      echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
      break;
    }

    unset($_SESSION['otp_code'], $_SESSION['otp_email'], $_SESSION['otp_expires']);
    $_SESSION['otp_verified'] = true;
    echo json_encode(['success' => true, 'message' => 'OTP verified.']);
    break;

  // ─────────────────────────────────────────────────────────────────────────

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

  case 'save_reservation':
    $uid = (isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1 && isset($_SESSION['uid']))
         ? (int)$_SESSION['uid']
         : null;

    $full_name   = trim($data['name']    ?? '');
    $email       = trim($data['email']   ?? '');
    $phone       = trim($data['phone']   ?? '');
    $party_size  = (int)($data['guests'] ?? 0);
    $res_date    = trim($data['date']    ?? '');
    $res_time    = trim($data['time']    ?? '');
    $special_req = trim($data['notes']   ?? '');

    if (!$full_name || !$email || !$phone || !$party_size || !$res_date || !$res_time) {
      echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
      break;
    }
    $today    = new DateTime('today');
    $selected = DateTime::createFromFormat('Y-m-d', $res_date);
    if (!$selected || $selected < $today) {
      echo json_encode(['success' => false, 'message' => 'Reservation date cannot be in the past.']);
      break;
    }
    if ($selected == $today) {
      $now          = new DateTime();
      $selectedTime = DateTime::createFromFormat('H:i', $res_time);
      if ($selectedTime && $selectedTime <= $now) {
        echo json_encode(['success' => false, 'message' => 'Reservation time cannot be in the past.']);
        break;
      }
    }
    if ($uid !== null) {
      $stmt = $mysqli->prepare(
        "INSERT INTO reservations_tbl (uid, full_name, email, phone, party_size, reservation_date, reservation_time, special_request, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
      );
      $stmt->bind_param("isssisss", $uid, $full_name, $email, $phone, $party_size, $res_date, $res_time, $special_req);
    } else {
      $stmt = $mysqli->prepare(
        "INSERT INTO reservations_tbl (uid, full_name, email, phone, party_size, reservation_date, reservation_time, special_request, status)
         VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, 'pending')"
      );
      $stmt->bind_param("sssisss", $full_name, $email, $phone, $party_size, $res_date, $res_time, $special_req);
    }
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => 'Reservation saved successfully!']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Database error: ' . $mysqli->error]);
    }
    $stmt->close();
    break;

  default:
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
}