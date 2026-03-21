<?php
session_start();
require_once './connection.php';

if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$uid = (int)$_SESSION['uid'];

$stmt = $mysqli->prepare("SELECT uid, username, email, role, created_at FROM users_tbl1 WHERE uid = ? LIMIT 1");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) { session_destroy(); header('Location: login.php'); exit; }

$stmt = $mysqli->prepare(
    "SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount),0) AS total_spent,
            SUM(status='pending') AS pending, SUM(status='completed') AS completed
     FROM orders_tbl WHERE uid = ?"
);
$stmt->bind_param("i", $uid);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $mysqli->prepare("SELECT order_id, order_date, total_amount, status, notes FROM orders_tbl WHERE uid = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$order_items = [];
if (!empty($orders)) {
    $ids = implode(',', array_column($orders, 'order_id'));
    $res = $mysqli->query("SELECT order_id, item_name, price, quantity, subtotal FROM order_items_tbl WHERE order_id IN ($ids) ORDER BY item_id");
    while ($row = $res->fetch_assoc()) { $order_items[$row['order_id']][] = $row; }
}

$stmt = $mysqli->prepare("SELECT reservation_id, full_name, reservation_date, reservation_time, party_size, status FROM reservations_tbl WHERE uid = ? ORDER BY reservation_date DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function badge($status) {
    $map = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','preparing'=>'badge-preparing','ready'=>'badge-ready','completed'=>'badge-completed','cancelled'=>'badge-cancelled'];
    $cls = $map[$status] ?? 'badge-pending';
    return "<span class='badge {$cls}'>" . ucfirst(htmlspecialchars($status)) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account – Casa De Manila</title>
  <link rel="stylesheet" href="./styles/about.css">
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
  <style>
    body { background: #f5f0e8; font-family: 'Cormorant Garamond', serif; }
    .account-page { min-height: 100vh; padding: 100px 20px 60px; }
    .account-wrapper { max-width: 1060px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }
    .a1 { animation: fadeUp 0.5s ease both; }
    .a2 { animation: fadeUp 0.5s ease 0.08s both; }
    .a3 { animation: fadeUp 0.5s ease 0.16s both; }
    .a4 { animation: fadeUp 0.5s ease 0.24s both; }

    .welcome-banner { background: linear-gradient(130deg, #111 55%, #1c1508); border: 1px solid rgba(212,175,55,0.35); border-radius: 20px; padding: 36px 44px; display: flex; align-items: center; justify-content: space-between; gap: 20px; box-shadow: 0 12px 40px rgba(0,0,0,0.22); }
    .wb-left h1 { font-family: 'Great Vibes', cursive; font-size: 3em; color: #d4af37; line-height: 1; }
    .wb-left p { color: rgba(255,255,255,0.5); font-size: 1em; margin-top: 6px; }
    .wb-left .role-badge { display: inline-block; background: rgba(212,175,55,0.12); border: 1px solid rgba(212,175,55,0.35); color: #d4af37; font-size: 0.72em; padding: 3px 12px; border-radius: 20px; letter-spacing: 2px; text-transform: uppercase; margin-top: 10px; }
    .wb-right { display: flex; align-items: center; gap: 16px; flex-shrink: 0; }
    .wb-avatar { width: 72px; height: 72px; border-radius: 50%; background: rgba(212,175,55,0.15); border: 2px solid #d4af37; display: flex; align-items: center; justify-content: center; font-size: 2em; color: #d4af37; font-weight: 700; }
    .wb-logout { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.6); padding: 8px 18px; border-radius: 10px; font-family: 'Cormorant Garamond', serif; font-size: 0.9em; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .wb-logout:hover { background: rgba(220,53,69,0.2); border-color: rgba(220,53,69,0.4); color: #ff8a8a; }

    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .stat-card { background: #fff; border: 1px solid rgba(212,175,55,0.15); border-radius: 16px; padding: 24px 20px; text-align: center; box-shadow: 0 3px 14px rgba(0,0,0,0.06); transition: transform 0.3s, box-shadow 0.3s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    .stat-icon { font-size: 1.8em; margin-bottom: 8px; }
    .stat-value { font-size: 2em; font-weight: 700; color: #111; line-height: 1; }
    .stat-label { font-size: 0.78em; color: #aaa; letter-spacing: 1px; text-transform: uppercase; margin-top: 6px; }

    .two-col { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }
    .col-left  { display: flex; flex-direction: column; gap: 24px; }
    .col-right { display: flex; flex-direction: column; gap: 24px; }

    .section-card { background: #fff; border: 1px solid rgba(212,175,55,0.15); border-radius: 18px; padding: 30px; box-shadow: 0 3px 14px rgba(0,0,0,0.06); }
    .section-card h2 { font-size: 1.3em; color: #111; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f0e8d0; display: flex; align-items: center; gap: 8px; }
    .section-card h2 .gold { color: #d4af37; }

    .order-card { border: 1px solid #f0e8d0; border-radius: 14px; margin-bottom: 12px; overflow: hidden; transition: box-shadow 0.3s; }
    .order-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.08); }
    .order-card:last-child { margin-bottom: 0; }
    .order-header { background: #fdf9f0; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; cursor: pointer; user-select: none; border-bottom: 1px solid #f0e8d0; transition: background 0.2s; }
    .order-header:hover { background: #faf4e4; }
    .order-meta { display: flex; flex-direction: column; gap: 2px; }
    .order-id   { font-weight: 700; color: #111; font-size: 1em; }
    .order-date { font-size: 0.85em; color: #999; }
    .order-right { display: flex; align-items: center; gap: 12px; }
    .order-total { font-size: 1.1em; font-weight: 700; color: #d4af37; }
    .order-toggle { color: #bbb; font-size: 1.1em; transition: transform 0.3s; }
    .order-card.open .order-toggle { transform: rotate(180deg); }
    .order-body { display: none; padding: 14px 18px; background: #fff; }
    .order-card.open .order-body { display: block; }
    .order-item-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #f9f5ec; font-size: 0.97em; color: #333; }
    .order-item-row:last-child { border-bottom: none; }
    .oi-name { flex: 1; }
    .oi-qty  { color: #aaa; font-size: 0.9em; margin: 0 14px; }
    .oi-sub  { font-weight: 600; color: #111; }
    .order-notes { margin-top: 10px; padding: 9px 12px; background: #fdf9f0; border-radius: 8px; font-size: 0.88em; color: #888; border-left: 3px solid #d4af37; }
    .order-footer-row { margin-top: 12px; padding-top: 10px; border-top: 2px solid #f0e8d0; display: flex; justify-content: space-between; font-weight: 700; color: #111; }
    .order-footer-row span:last-child { color: #d4af37; }

    .empty-state { text-align: center; padding: 40px 20px; color: #bbb; }
    .empty-state .empty-icon { font-size: 2.5em; margin-bottom: 10px; }
    .empty-state a { color: #d4af37; text-decoration: none; font-weight: 600; }

    .badge { display: inline-block; padding: 3px 11px; border-radius: 20px; font-size: 0.75em; font-weight: 600; letter-spacing: 0.5px; white-space: nowrap; }
    .badge-pending   { background: #fff3cd; color: #856404; }
    .badge-confirmed { background: #cfe2ff; color: #084298; }
    .badge-preparing { background: #fff0d4; color: #9a5a00; }
    .badge-ready     { background: #d1f4e0; color: #0a5235; }
    .badge-completed { background: #d1e7dd; color: #0a5235; }
    .badge-cancelled { background: #f8d7da; color: #842029; }

    .pi-row { display: flex; flex-direction: column; gap: 2px; padding: 12px 0; border-bottom: 1px solid #f5f0e8; }
    .pi-row:last-child { border-bottom: none; }
    .pi-label { font-size: 0.75em; letter-spacing: 1.5px; text-transform: uppercase; color: #bbb; }
    .pi-value { font-size: 1em; color: #111; font-weight: 600; word-break: break-all; }

    .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 4px; }
    .qa-btn { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 16px 10px; background: #fdf9f0; border: 1px solid rgba(212,175,55,0.2); border-radius: 12px; text-decoration: none; color: #444; font-family: 'Cormorant Garamond', serif; font-size: 0.92em; transition: all 0.3s; text-align: center; cursor: pointer; }
    .qa-btn:hover { background: #111; color: #d4af37; border-color: #d4af37; transform: translateY(-2px); }
    .qa-btn .qa-icon { font-size: 1.5em; }
    .qa-btn.danger:hover { background: #c0392b; color: #fff; border-color: #c0392b; }

    .res-table { width: 100%; border-collapse: collapse; font-size: 0.93em; }
    .res-table th { text-align: left; padding: 9px 13px; background: #f9f5ec; color: #aaa; font-size: 0.75em; letter-spacing: 1.5px; text-transform: uppercase; }
    .res-table td { padding: 11px 13px; border-bottom: 1px solid #f5f0e8; color: #333; }
    .res-table tr:last-child td { border-bottom: none; }
    .res-table tr:hover td { background: #fdfaf3; }

    @media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 560px) {
      .welcome-banner { flex-direction: column; text-align: center; padding: 28px 22px; }
      .wb-right { flex-direction: column; }
      .stats-row { grid-template-columns: repeat(2, 1fr); }
      .res-table thead { display: none; }
      .res-table td { display: block; padding: 5px 12px; }
      .res-table td::before { content: attr(data-label)": "; font-weight: 700; color: #aaa; font-size: 0.82em; }
      .res-table tr { display: block; border: 1px solid #f0e8d0; border-radius: 10px; margin-bottom: 10px; }
    }
  </style>
</head>
<body>

<div class="navbar scrolled" id="navbar">
  <div class="logo">
    <a href="./index.php">Casa De Manila</a>
    <p>Authenticity You Can Taste</p>
  </div>
  <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
  <nav class="nav-links" id="navLinks">
    <ul>
      <li><a href="./index.php">Home</a></li>
      <li><a href="./reservation.php">Reservation</a></li>
      <li><a href="./menu.php">Menu</a></li>
      <li><a href="./events.php">Events</a></li>
      <li><a href="./contact_us.php">Contact</a></li>
      <li><a href="./about.php">About</a></li>
      <li><a href="account.php?logout=1" style="color:#d4af37;">Logout</a></li>
    </ul>
  </nav>
</div>

<div class="account-page">
  <div class="account-wrapper">

    <!-- WELCOME BANNER -->
    <div class="welcome-banner a1">
      <div class="wb-left">
        <h1>Hello, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <p>Welcome back to your Casa De Manila dashboard</p>
        <span class="role-badge"><?php echo htmlspecialchars($user['role']); ?></span>
      </div>
      <div class="wb-right">
        <div class="wb-avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
        <a href="account.php?logout=1" class="wb-logout">🚪 Logout</a>
      </div>
    </div>

    <!-- STATS -->
    <div class="stats-row a2">
      <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-value"><?php echo (int)$stats['total_orders']; ?></div>
        <div class="stat-label">Total Orders</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value">₱<?php echo number_format((float)$stats['total_spent'], 0); ?></div>
        <div class="stat-label">Total Spent</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-value"><?php echo (int)$stats['pending']; ?></div>
        <div class="stat-label">Pending</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value"><?php echo (int)$stats['completed']; ?></div>
        <div class="stat-label">Completed</div>
      </div>
    </div>

    <!-- TWO COLUMN -->
    <div class="two-col a3">
      <div class="col-left">

        <!-- ORDER HISTORY -->
        <div class="section-card">
          <h2>🧾 Order <span class="gold">History</span></h2>

          <?php if (empty($orders)): ?>
            <div class="empty-state">
              <div class="empty-icon">🍽️</div>
              <p>No orders yet.</p>
              <p style="margin-top:8px;"><a href="./menu.php">Browse the menu →</a></p>
            </div>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <div class="order-card" id="ord-<?php echo $order['order_id']; ?>">

                <div class="order-header" onclick="toggleOrder(<?php echo $order['order_id']; ?>)">
                  <div class="order-meta">
                    <span class="order-id">Order #<?php echo str_pad($order['order_id'], 4, '0', STR_PAD_LEFT); ?></span>
                    <span class="order-date"><?php echo date('F j, Y — g:i A', strtotime($order['order_date'])); ?></span>
                  </div>
                  <div class="order-right">
                    <?php echo badge($order['status']); ?>
                    <span class="order-total">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                    <span class="order-toggle">▼</span>
                  </div>
                </div>

                <div class="order-body">
                  <?php if (!empty($order_items[$order['order_id']])): ?>
                    <?php foreach ($order_items[$order['order_id']] as $it): ?>
                      <div class="order-item-row">
                        <span class="oi-name"><?php echo htmlspecialchars($it['item_name']); ?></span>
                        <span class="oi-qty">× <?php echo (int)$it['quantity']; ?></span>
                        <span class="oi-sub">₱<?php echo number_format($it['subtotal'], 2); ?></span>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p style="color:#bbb;font-size:0.9em;padding:8px 0;">No item details recorded.</p>
                  <?php endif; ?>
                  <?php if (!empty($order['notes'])): ?>
                    <div class="order-notes">📝 <?php echo htmlspecialchars($order['notes']); ?></div>
                  <?php endif; ?>
                  <div class="order-footer-row">
                    <span>Order Total</span>
                    <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
                  </div>
                </div>

              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- RESERVATIONS -->
        <div class="section-card">
          <h2>🗓️ My <span class="gold">Reservations</span></h2>
          <?php if (empty($reservations)): ?>
            <div class="empty-state">
              <div class="empty-icon">📅</div>
              <p>No reservations yet.</p>
              <p style="margin-top:8px;"><a href="./reservation.php">Book a table →</a></p>
            </div>
          <?php else: ?>
            <table class="res-table">
              <thead>
                <tr><th>Name</th><th>Date</th><th>Time</th><th>Guests</th><th>Status</th></tr>
              </thead>
              <tbody>
                <?php foreach ($reservations as $res): ?>
                  <tr>
                    <td data-label="Name"><?php echo htmlspecialchars($res['full_name']); ?></td>
                    <td data-label="Date"><?php echo date('M j, Y', strtotime($res['reservation_date'])); ?></td>
                    <td data-label="Time"><?php echo date('g:i A', strtotime($res['reservation_time'])); ?></td>
                    <td data-label="Guests"><?php echo $res['party_size']; ?> pax</td>
                    <td data-label="Status"><?php echo badge($res['status']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>

      </div><!-- col-left -->

      <div class="col-right a4">

        <!-- PROFILE -->
        <div class="section-card">
          <h2>👤 My <span class="gold">Profile</span></h2>
          <div class="pi-row">
            <span class="pi-label">Username</span>
            <span class="pi-value"><?php echo htmlspecialchars($user['username']); ?></span>
          </div>
          <div class="pi-row">
            <span class="pi-label">Email</span>
            <span class="pi-value"><?php echo htmlspecialchars($user['email']); ?></span>
          </div>
          <div class="pi-row">
            <span class="pi-label">Account Role</span>
            <span class="pi-value"><?php echo ucfirst($user['role']); ?></span>
          </div>
          <div class="pi-row">
            <span class="pi-label">Member Since</span>
            <span class="pi-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
          </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="section-card">
          <h2>⚡ Quick <span class="gold">Actions</span></h2>
          <div class="quick-actions">
            <a href="./menu.php"           class="qa-btn"><span class="qa-icon">🍽️</span>Order Food</a>
            <a href="./reservation.php"    class="qa-btn"><span class="qa-icon">📅</span>Reserve Table</a>
            <a href="./menu.php"           class="qa-btn"><span class="qa-icon">📖</span>View Menu</a>
            <a href="./events.php"         class="qa-btn"><span class="qa-icon">🎉</span>Events</a>
            <a href="./contact_us.php"     class="qa-btn"><span class="qa-icon">💬</span>Contact Us</a>
            <a href="account.php?logout=1" class="qa-btn danger"><span class="qa-icon">🚪</span>Logout</a>
          </div>
        </div>

      </div><!-- col-right -->
    </div><!-- two-col -->

  </div>
</div>

<footer class="footer">
  <div class="footer-container">
    <p>&copy; <?php echo date('Y'); ?> Casa De Manila. All rights reserved.</p>
    <p>Email: reservations@casamanila.ph | Phone: +63 912 345 6789</p>
    <div class="social-links">
      <a href="#">Facebook</a><a href="#">Instagram</a><a href="#">Twitter</a>
    </div>
  </div>
</footer>

<script src="./scripts/function.js"></script>
<script>
  function toggleOrder(id) {
    const card = document.getElementById('ord-' + id);
    card.classList.toggle('open');
  }
</script>
</body>
</html>