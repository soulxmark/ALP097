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
  <link rel="stylesheet" href="./styles/account/account.css">
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
        <!--<li><a href="account.php?logout=1" style="color:#d4af37;">Logout</a></li>-->
      </ul>
    </nav>
  </div>
  <br><br><br>
  <div class="account-page">
    <div class="account-wrapper">

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

      <div class="two-col a3">
        <div class="col-left">

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
                <?php if ($order['status'] === 'pending'): ?>
                <div style="text-align:right;margin-top:12px;">
                  <button class="pay-btn" onclick="openPayment(<?php echo $order['order_id']; ?>,'<?php echo number_format($order['total_amount'],2); ?>')">
                    💳 Pay Now
                  </button>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>

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
                <tr>
                  <th>Name</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Guests</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservations as $res): ?>
                <tr>
                  <td data-label="Name"><?php echo htmlspecialchars($res['full_name']); ?></td>
                  <td data-label="Date"><?php echo date('M j, Y', strtotime($res['reservation_date'])); ?></td>
                  <td data-label="Time"><?php echo date('g:i A', strtotime($res['reservation_time'])); ?></td>
                  <td data-label="Guests"><?php echo $res['party_size']; ?>paxs</td>
                  <td data-label="Status"><?php echo badge($res['status']); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php endif; ?>
          </div>

        </div>

        <div class="col-right a4">
          <div class="section-card">
            <h2>👤 My <span class="gold">Profile</span></h2>
            <div class="pi-row"><span class="pi-label">Username</span><span class="pi-value"><?php echo htmlspecialchars($user['username']); ?></span></div>
            <div class="pi-row"><span class="pi-label">Email</span><span class="pi-value"><?php echo htmlspecialchars($user['email']); ?></span></div>
            <div class="pi-row"><span class="pi-label">Account Role</span><span class="pi-value"><?php echo ucfirst($user['role']); ?></span></div>
            <div class="pi-row"><span class="pi-label">Member Since</span><span class="pi-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span></div>
          </div>
          <div class="section-card">
            <h2>⚡ Quick <span class="gold">Actions</span></h2>
            <div class="quick-actions">
              <a href="./menu.php" class="qa-btn"><span class="qa-icon">🍽️</span>Order Food</a>
              <a href="./reservation.php" class="qa-btn"><span class="qa-icon">📅</span>Reserve Table</a>
              <a href="./menu.php" class="qa-btn"><span class="qa-icon">📖</span>View Menu</a>
              <a href="./events.php" class="qa-btn"><span class="qa-icon">🎉</span>Events</a>
              <a href="./contact_us.php" class="qa-btn"><span class="qa-icon">💬</span>Contact Us</a>
              <a href="account.php?logout=1" class="qa-btn danger"><span class="qa-icon">🚪</span>Logout</a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ── Payment Modal ─────────────────────────────────────────── -->
  <div class="pm-overlay" id="pmOverlay">
    <div class="pm-box">

      <div class="pm-header">
        <div>
          <h3>Casa De Manila</h3>
          <p>Secure Payment</p>
        </div>
        <button class="pm-close" onclick="closePayment()">×</button>
      </div>

      <div class="pm-body">

        <div class="pm-amount">
          <div class="label">Amount Due</div>
          <div class="value" id="pmAmount">₱0.00</div>
          <div class="order-ref" id="pmRef">Order #0000</div>
        </div>

        <!-- Tabs -->
        <div class="pm-tabs">
          <button class="pm-tab active" onclick="switchTab('gcash')">
            <span class="tab-icon">💙</span>GCash
          </button>
          <button class="pm-tab" onclick="switchTab('maya')">
            <span class="tab-icon">💚</span>Maya
          </button>
          <button class="pm-tab" onclick="switchTab('card')">
            <span class="tab-icon">💳</span>Card
          </button>
        </div>

        <!-- GCash Panel -->
        <div class="pm-panel active" id="tab-gcash">
          <div class="pm-qr-wrap">
            <div class="qr-placeholder">📱</div>
            <div class="pm-number">0917 123 4567</div>
            <div class="pm-name">Casa De Manila Inc.</div>
            <div class="pm-instruction">
              1. Open your <strong>GCash</strong> app<br>
              2. Tap <strong>Send Money</strong> → enter the number above<br>
              3. Enter the exact amount and tap <strong>Send</strong><br>
              4. Screenshot your receipt and click confirm below
            </div>
          </div>
          <button class="pm-submit" onclick="confirmPayment('GCash')">✓ I've Paid via GCash</button>
        </div>

        <!-- Maya Panel -->
        <div class="pm-panel" id="tab-maya">
          <div class="pm-qr-wrap">
            <div class="qr-placeholder">📱</div>
            <div class="pm-number">0915 987 6543</div>
            <div class="pm-name">Casa De Manila Inc.</div>
            <div class="pm-instruction">
              1. Open your <strong>Maya</strong> app<br>
              2. Tap <strong>Send Money</strong> → enter the number above<br>
              3. Enter the exact amount and tap <strong>Send</strong><br>
              4. Screenshot your receipt and click confirm below
            </div>
          </div>
          <button class="pm-submit" onclick="confirmPayment('Maya')">✓ I've Paid via Maya</button>
        </div>

        <!-- Card Panel -->
        <div class="pm-panel" id="tab-card">
          <div class="card-icons">
            <span class="card-icon visa">VISA</span>
            <span class="card-icon mc">MC</span>
            <span class="card-icon amex">AMEX</span>
          </div>
          <div class="pm-card-form">
            <div class="pm-field">
              <label>Card Number</label>
              <input type="text" id="cardNum" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCard(this)">
            </div>
            <div class="pm-field">
              <label>Cardholder Name</label>
              <input type="text" id="cardName" placeholder="Juan dela Cruz">
            </div>
            <div class="form-row">
              <div class="pm-field">
                <label>Expiry</label>
                <input type="text" id="cardExp" placeholder="MM/YY" maxlength="5" oninput="formatExpiry(this)">
              </div>
              <div class="pm-field">
                <label>CVV</label>
                <input type="password" id="cardCvv" placeholder="•••" maxlength="4">
              </div>
            </div>
          </div>
          <button class="pm-submit" onclick="confirmPayment('Card')">🔒 Pay Securely</button>
        </div>

        <!-- Success state -->
        <div class="pm-success" id="pmSuccess">
          <div class="success-icon">✅</div>
          <h4>Payment Received!</h4>
          <p>Thank you! Your payment has been noted.<br>We are now preparing your order.</p>
        </div>

      </div>
    </div>
  </div>

  <footer class="footer reveal">
    <div class="footer-container">
      <p>&copy; 2026 Casa De Manila. All rights reserved.</p>
      <p>Email: reservations@casamanila.ph | Phone: +63 912 345 6789</p>
      <div class="social-links">
        <a href="https://facebook.com">Facebook</a>
        <a href="https://instagram.com">Instagram</a>
        <a href="https://twitter.com">X</a>
      </div>
    </div>
  </footer>

  <script src="./scripts/function.js"></script>
  <script>
    function toggleOrder(id) {
      document.getElementById('ord-' + id).classList.toggle('open');
    }

    /* ── Payment Modal ── */
    let currentOrderId = null;

    function openPayment(orderId, amount) {
      currentOrderId = orderId;
      document.getElementById('pmAmount').textContent = '₱' + amount;
      document.getElementById('pmRef').textContent = 'Order #' + String(orderId).padStart(4, '0');
      document.getElementById('pmSuccess').style.display = 'none';
      document.querySelectorAll('.pm-panel, .pm-tabs, .pm-amount').forEach(el => el.style.display = '');
      switchTab('gcash');
      document.getElementById('pmOverlay').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closePayment() {
      document.getElementById('pmOverlay').classList.remove('active');
      document.body.style.overflow = '';
    }

    document.getElementById('pmOverlay').addEventListener('click', e => {
      if (e.target === document.getElementById('pmOverlay')) closePayment();
    });

    function switchTab(tab) {
      document.querySelectorAll('.pm-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.pm-panel').forEach(p => p.classList.remove('active'));
      event.currentTarget.classList.add('active');
      document.getElementById('tab-' + tab).classList.add('active');
    }

    function confirmPayment(method) {
      // Show success, hide panels
      document.querySelectorAll('.pm-panel').forEach(p => p.style.display = 'none');
      document.querySelectorAll('.pm-tabs').forEach(t => t.style.display = 'none');
      document.getElementById('pmSuccess').style.display = 'block';
      setTimeout(closePayment, 3000);
    }

    /* ── Card formatting helpers ── */
    function formatCard(input) {
      let v = input.value.replace(/\D/g, '').substring(0, 16);
      input.value = v.replace(/(.{4})/g, '$1 ').trim();
    }

    function formatExpiry(input) {
      let v = input.value.replace(/\D/g, '').substring(0, 4);
      if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2);
      input.value = v;
    }
  </script>
</body>

</html>