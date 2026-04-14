case 'save_reservation':
// ── Pull uid from session (set by login action) ──
$uid = (isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1 && isset($_SESSION['uid']))
? (int)$_SESSION['uid']
: null;

$full_name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$party_size = (int)($data['guests'] ?? 0);
$res_date = trim($data['date'] ?? '');
$res_time = trim($data['time'] ?? '');
$special_req = trim($data['notes'] ?? '');

if (!$full_name || !$email || !$phone || !$party_size || !$res_date || !$res_time) {
echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
break;
}

// Block past date
$today = new DateTime('today');
$selected = DateTime::createFromFormat('Y-m-d', $res_date);
if (!$selected || $selected

< $today) { echo json_encode(['success'=> false, 'message' => 'Reservation date cannot be in the past.']);
  break;
  }

  // Block past time if today
  if ($selected == $today) {
  $now = new DateTime();
  $selectedTime = DateTime::createFromFormat('H:i', $res_time);
  if ($selectedTime && $selectedTime <= $now) { echo json_encode(['success'=> false, 'message' => 'Reservation time cannot be in the past.']);
    break;
    }
    }

    if ($uid !== null) {
    // Logged-in: i=uid, s=name, s=email, s=phone, i=party_size, s=date, s=time, s=notes
    $stmt = $mysqli->prepare(
    "INSERT INTO reservations_tbl (uid, full_name, email, phone, party_size, reservation_date, reservation_time, special_request, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->bind_param("isssisss", $uid, $full_name, $email, $phone, $party_size, $res_date, $res_time, $special_req);
    } else {
    // Guest: s=name, s=email, s=phone, i=party_size, s=date, s=time, s=notes
    $stmt = $mysqli->prepare(
    "INSERT INTO reservations_tbl (uid, full_name, email, phone, party_size, reservation_date, reservation_time, special_request, status)
    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->bind_param("sssisss", $full_name, $email, $phone, $party_size, $res_date, $res_time, $special_req);
    }

    if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Reservation saved.']);
    } else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $mysqli->error]);
    }
    $stmt->close();
    break;