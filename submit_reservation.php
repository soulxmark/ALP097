<?php
// submit_reservation.php
// Receives JSON POST from email.js and saves to reservations_tbl
session_start();
require_once './connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Decode JSON body from email.js fetch
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data)) {
    $data = $_POST;
}

// ── Pull uid from SESSION only — never trust client input ──
$is_logged_in = isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1 && isset($_SESSION['uid']);
$uid          = $is_logged_in ? (int)$_SESSION['uid'] : null;

// ── Sanitize ──
$full_name   = trim($data['name']    ?? '');
$email       = trim($data['email']   ?? '');
$phone       = trim($data['phone']   ?? '');
$party_size  = (int)($data['guests'] ?? 0);
$res_date    = trim($data['date']    ?? '');
$res_time    = trim($data['time']    ?? '');
$special_req = trim($data['notes']   ?? '');

// ── Required field check ──
if (!$full_name || !$email || !$phone || !$party_size || !$res_date || !$res_time) {
    echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled.']);
    exit;
}

// ── Block past date ──
$today    = new DateTime('today');
$selected = DateTime::createFromFormat('Y-m-d', $res_date);
if (!$selected || $selected < $today) {
    echo json_encode(['status' => 'error', 'message' => 'Reservation date cannot be in the past.']);
    exit;
}

// ── Block past time if today ──
if ($selected == $today) {
    $now          = new DateTime();
    $selectedTime = DateTime::createFromFormat('H:i', $res_time);
    if ($selectedTime && $selectedTime <= $now) {
        echo json_encode(['status' => 'error', 'message' => 'Reservation time cannot be in the past.']);
        exit;
    }
}

// ── Insert — handle uid=null separately to avoid MySQLi null int bug ──
if ($uid !== null) {
    // Logged-in user: bind uid as integer
    // Type string: i=uid, s=full_name, s=email, s=phone, i=party_size, s=res_date, s=res_time, s=special_req
    $stmt = $mysqli->prepare(
        "INSERT INTO reservations_tbl
            (uid, full_name, email, phone, party_size, reservation_date, reservation_time, special_request, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->bind_param("isssisss", $uid, $full_name, $email, $phone, $party_size, $res_date, $res_time, $special_req);
} else {
    // Guest (not logged in): uid = NULL in SQL
    // Type string: s=full_name, s=email, s=phone, i=party_size, s=res_date, s=res_time, s=special_req
    $stmt = $mysqli->prepare(
        "INSERT INTO reservations_tbl
            (uid, full_name, email, phone, party_size, reservation_date, reservation_time, special_request, status)
         VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->bind_param("sssisss", $full_name, $email, $phone, $party_size, $res_date, $res_time, $special_req);
}

if ($stmt && $stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Reservation saved.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $mysqli->error]);
}

$stmt->close();
$mysqli->close();
exit;
?>