<?php
// =============================================
// admin.php — Main Admin Dashboard
// Casa De Manila
// =============================================
session_start();
require_once './connection.php';
require_once './session_check.php';

// SECURITY: Admins only
if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1 || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$today = date('Y-m-d');

// Overall Stats
$stats = $mysqli->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_revenue,
        SUM(total_amount) as gross_revenue
    FROM orders_tbl
")->fetch_assoc();

// Today's Stats
$today_stats = $mysqli->query("
    SELECT 
        COUNT(*) as today_orders,
        SUM(total_amount) as today_revenue
    FROM orders_tbl
    WHERE DATE(order_date) = '$today'
")->fetch_assoc();

// Reservation Stats
$res_stats = $mysqli->query("
    SELECT 
        COUNT(*) as total_res,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_res
    FROM reservations_tbl
")->fetch_assoc();

// Recent Orders
$orders = $mysqli->query("
    SELECT o.*, u.username, u.email 
    FROM orders_tbl o 
    JOIN users_tbl1 u ON o.uid = u.uid 
    ORDER BY o.order_date DESC 
    LIMIT 50
")->fetch_all(MYSQLI_ASSOC);

// Recent Reservations
$reservations = $mysqli->query("
    SELECT * FROM reservations_tbl 
    ORDER BY created_at DESC 
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

// Top Selling Items
$top_items = $mysqli->query("
    SELECT item_name, SUM(quantity) as qty, SUM(subtotal) as sales
    FROM order_items_tbl
    GROUP BY item_name 
    ORDER BY qty DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Casa De Manila</title>
  < <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root { --gold: #d4af37; --dark: #111; --card: #1a1a1a; }
    * { box-sizing: border-box; }
    body { background: var(--dark); color: #fff; font-family: 'Cormorant Garamond', serif; margin: 0; padding: 24px; }

    .admin-header {
      display: flex; justify-content: space-between; align-items: center;
      border-bottom: 1px solid rgba(212,175,55,0.3);
      padding-bottom: 20px; margin-bottom: 30px; flex-wrap: wrap; gap: 12px;
    }
    .admin-header h1 { font-family: "Cormorant Garamond", serif; color: var(--gold); font-size: 3em; margin: 0; }
    .header-right { display: flex; align-items: center; gap: 12px; font-size: 0.95em; flex-wrap: wrap; }
    .header-right a {
      text-decoration: none; border: 1px solid rgba(255,107,107,0.4);
      color: #ff6b6b; padding: 6px 14px; border-radius: 8px; transition: 0.3s;
    }
    .header-right a:hover { background: rgba(255,107,107,0.15); }
    .report-link {
      color: var(--gold) !important;
      border-color: rgba(212,175,55,0.4) !important;
    }
    .report-link:hover { background: rgba(212,175,55,0.1) !important; }

    .tab-nav {
      display: flex; gap: 8px; margin-bottom: 28px;
      border-bottom: 1px solid rgba(212,175,55,0.15);
    }
    .tab-btn {
      background: none; border: none; color: rgba(255,255,255,0.4);
      font-family: 'Cormorant Garamond', serif; font-size: 1em;
      padding: 10px 20px; cursor: pointer;
      border-bottom: 2px solid transparent; transition: 0.2s; letter-spacing: 1px;
    }
    .tab-btn:hover { color: var(--gold); }
    .tab-btn.active { color: var(--gold); border-bottom: 2px solid var(--gold); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px; margin-bottom: 32px;
    }
    .stat-box {
      background: var(--card); border: 1px solid rgba(212,175,55,0.2);
      padding: 20px; border-radius: 14px; text-align: center;
    }
    .stat-box h3 { color: var(--gold); text-transform: uppercase; font-size: 0.72em; letter-spacing: 2px; margin: 0 0 10px; }
    .stat-box p { font-size: 2em; font-weight: bold; margin: 0; }
    .stat-box small { color: rgba(255,255,255,0.35); font-size: 0.75em; }

    .section-title {
      color: var(--gold); font-size: 1.1em; letter-spacing: 2px;
      text-transform: uppercase; margin: 0 0 16px;
      border-left: 3px solid var(--gold); padding-left: 12px;
    }
    .table-wrap { overflow-x: auto; border-radius: 14px; }
    .order-table { width: 100%; border-collapse: collapse; background: var(--card); min-width: 700px; }
    .order-table th {
      background: rgba(212,175,55,0.08); color: var(--gold);
      padding: 13px 15px; text-align: left;
      text-transform: uppercase; font-size: 0.75em; letter-spacing: 1px; white-space: nowrap;
    }
    .order-table td { padding: 13px 15px; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.95em; }
    .order-table tr:hover td { background: rgba(255,255,255,0.02); }
    .revenue-cell { color: var(--gold); font-weight: bold; }

    .status-pill { padding: 3px 11px; border-radius: 20px; font-size: 0.75em; font-weight: bold; white-space: nowrap; }
    .status-pending   { background: #e67e22; color: #fff; }
    .status-confirmed { background: #2980b9; color: #fff; }
    .status-preparing { background: #8e44ad; color: #fff; }
    .status-ready     { background: #16a085; color: #fff; }
    .status-completed { background: #27ae60; color: #fff; }
    .status-cancelled { background: #c0392b; color: #fff; }

    .btn-action {
      background: transparent; border: 1px solid var(--gold); color: var(--gold);
      padding: 5px 12px; border-radius: 6px; cursor: pointer;
      font-family: 'Cormorant Garamond', serif; font-size: 0.88em; transition: 0.2s;
    }
    .btn-action:hover { background: var(--gold); color: var(--dark); }
    .btn-action.danger { border-color: #e74c3c; color: #e74c3c; }
    .btn-action.danger:hover { background: #e74c3c; color: #fff; }

    .top-item-row {
      display: flex; justify-content: space-between; align-items: center;
      padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .top-item-row:last-child { border-bottom: none; }
    .item-bar-wrap { flex: 1; margin: 0 16px; }
    .item-bar { height: 6px; background: var(--gold); border-radius: 3px; opacity: 0.7; }

    @media (max-width: 600px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      body { padding: 14px; }
    }
  </style>
</head>
<body>

  <div class="admin-header">
    <h1>Casa De Manila (Dashboard)</h1>
    <div class="header-right">
      <span style="color:rgba(255,255,255,0.5);">
        Account: <strong style="color:#fff;"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
      </span>
      <a href="admin_dashboard.php" class="report-link" target="_blank">📊 Daily Report</a>
      <a href="admin.php?logout=1">🚪 Logout</a>
    </div>
  </div>

  <div class="tab-nav">
    <button class="tab-btn active" onclick="switchTab('orders', this)">📦 Orders</button>
    <button class="tab-btn" onclick="switchTab('reservations', this)">📅 Reservations</button>
    <button class="tab-btn" onclick="switchTab('reports', this)">📊 Reports</button>
  </div>

  <!-- ORDERS TAB -->
  <div id="tab-orders" class="tab-content active">
    <div class="stats-grid">
      <div class="stat-box">
        <h3>Total Revenue</h3>
        <p>₱<?php echo number_format($stats['total_revenue'] ?? 0, 0); ?></p>
        <small>completed orders</small>
      </div>
      <div class="stat-box">
        <h3>Gross Revenue</h3>
        <p>₱<?php echo number_format($stats['gross_revenue'] ?? 0, 0); ?></p>
        <small>all orders</small>
      </div>
      <div class="stat-box">
        <h3>Pending Orders</h3>
        <p style="color:#e67e22;"><?php echo $stats['pending_count'] ?? 0; ?></p>
      </div>
      <div class="stat-box">
        <h3>Total Orders</h3>
        <p><?php echo $stats['total_orders'] ?? 0; ?></p>
      </div>
      <div class="stat-box">
        <h3>Today's Orders</h3>
        <p><?php echo $today_stats['today_orders'] ?? 0; ?></p>
        <small>₱<?php echo number_format($today_stats['today_revenue'] ?? 0, 0); ?></small>
      </div>
    </div>

    <p class="section-title">Recent Orders</p>
    <div class="table-wrap">
      <table class="order-table">
        <thead>
          <tr>
            <th>Order ID</th><th>Customer</th><th>Date</th>
            <th>Amount</th><th>Status</th><th>Notes</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?php echo str_pad($o['order_id'], 4, '0', STR_PAD_LEFT); ?></td>
            <td>
              <strong><?php echo htmlspecialchars($o['username']); ?></strong><br>
              <small style="opacity:0.45;"><?php echo htmlspecialchars($o['email']); ?></small>
            </td>
            <td><?php echo date('M d, Y H:i', strtotime($o['order_date'])); ?></td>
            <td class="revenue-cell">₱<?php echo number_format($o['total_amount'], 2); ?></td>
            <td>
              <span class="status-pill status-<?php echo $o['status']; ?>">
                <?php echo strtoupper($o['status']); ?>
              </span>
            </td>
            <td style="font-size:0.88em;max-width:180px;opacity:0.7;"><?php echo htmlspecialchars($o['notes'] ?? ''); ?></td>
            <td>
              <?php if ($o['status'] === 'pending'): ?>
                <button class="btn-action" onclick="markOrder(<?php echo $o['order_id']; ?>,'completed')">✓ Mark Paid</button>
                <button class="btn-action danger" style="margin-top:4px;" onclick="markOrder(<?php echo $o['order_id']; ?>,'cancelled')">✕ Cancel</button>
              <?php elseif ($o['status'] === 'completed'): ?>
                <span style="color:#27ae60;font-size:0.85em;">✓ Verified</span>
              <?php elseif ($o['status'] === 'cancelled'): ?>
                <span style="color:#c0392b;font-size:0.85em;">Cancelled</span>
              <?php else: ?>
                <button class="btn-action" onclick="markOrder(<?php echo $o['order_id']; ?>,'completed')">✓ Complete</button>
                <button class="btn-action danger" style="margin-top:4px;" onclick="markOrder(<?php echo $o['order_id']; ?>,'cancelled')">✕ Cancel</button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($orders)): ?>
          <tr><td colspan="7" style="text-align:center;opacity:0.4;padding:30px;">No orders yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- RESERVATIONS TAB -->
  <div id="tab-reservations" class="tab-content">
    <div class="stats-grid">
      <div class="stat-box">
        <h3>Total Reservations</h3>
        <p><?php echo $res_stats['total_res'] ?? 0; ?></p>
      </div>
      <div class="stat-box">
        <h3>Pending</h3>
        <p style="color:#e67e22;"><?php echo $res_stats['pending_res'] ?? 0; ?></p>
      </div>
    </div>

    <p class="section-title">Recent Reservations</p>
    <div class="table-wrap">
      <table class="order-table">
        <thead>
          <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
            <th>Date</th><th>Time</th><th>Guests</th><th>Status</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations as $r): ?>
          <tr>
            <td>#<?php echo str_pad($r['reservation_id'], 4, '0', STR_PAD_LEFT); ?></td>
            <td><?php echo htmlspecialchars($r['full_name']); ?></td>
            <td style="font-size:0.85em;opacity:0.7;"><?php echo htmlspecialchars($r['email']); ?></td>
            <td><?php echo htmlspecialchars($r['phone']); ?></td>
            <td><?php echo date('M d, Y', strtotime($r['reservation_date'])); ?></td>
            <td><?php echo date('g:i A', strtotime($r['reservation_time'])); ?></td>
            <td style="text-align:center;"><?php echo $r['party_size']; ?></td>
            <td>
              <span class="status-pill status-<?php echo $r['status']; ?>">
                <?php echo strtoupper($r['status']); ?>
              </span>
            </td>
            <td>
              <?php if ($r['status'] === 'pending'): ?>
                <button class="btn-action" onclick="updateRes(<?php echo $r['reservation_id']; ?>,'confirmed')">✓ Confirm</button>
                <button class="btn-action danger" style="margin-top:4px;" onclick="updateRes(<?php echo $r['reservation_id']; ?>,'cancelled')">✕ Cancel</button>
              <?php elseif ($r['status'] === 'confirmed'): ?>
                <span style="color:#2980b9;font-size:0.85em;">✓ Confirmed</span>
              <?php else: ?>
                <span style="color:#c0392b;font-size:0.85em;">Cancelled</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($reservations)): ?>
          <tr><td colspan="9" style="text-align:center;opacity:0.4;padding:30px;">No reservations yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- REPORTS TAB -->
  <div id="tab-reports" class="tab-content">
    <a href="admin_dashboard.php" target="_blank" class="btn-action" style="display:inline-block;margin-bottom:24px;padding:10px 20px;font-size:1em;">
      📥 Open Daily Report (PDF)
    </a>

    <p class="section-title">Top Selling Items (All Time)</p>
    <div style="background:var(--card);border-radius:14px;padding:24px;max-width:600px;">
      <?php
      $maxQty = !empty($top_items) ? $top_items[0]['qty'] : 1;
      foreach ($top_items as $item):
        $pct = round(($item['qty'] / $maxQty) * 100);
      ?>
      <div class="top-item-row">
        <span style="min-width:160px;"><?php echo htmlspecialchars($item['item_name']); ?></span>
        <div class="item-bar-wrap">
          <div class="item-bar" style="width:<?php echo $pct; ?>%;"></div>
        </div>
        <span style="min-width:40px;text-align:right;opacity:0.7;"><?php echo $item['qty']; ?>x</span>
        <span class="revenue-cell" style="min-width:90px;text-align:right;">₱<?php echo number_format($item['sales'], 0); ?></span>
      </div>
      <?php endforeach; ?>
      <?php if (empty($top_items)): ?>
      <p style="opacity:0.4;text-align:center;">No sales data yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function switchTab(name, btn) {
      document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById('tab-' + name).classList.add('active');
      btn.classList.add('active');
    }

    async function markOrder(id, status) {
      const label = status === 'completed' ? 'completed (paid)' : 'cancelled';
      if (!confirm('Mark Order #' + id + ' as ' + label + '?')) return;
      const res  = await fetch('process_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: id, status: status })
      });
      const data = await res.json();
      if (data.success) location.reload();
      else alert('Update failed: ' + (data.message || 'Unknown error'));
    }

    async function updateRes(id, status) {
      const label = status === 'confirmed' ? 'confirm' : 'cancel';
      if (!confirm('Are you sure you want to ' + label + ' Reservation #' + id + '?')) return;
      const res  = await fetch('update_reservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reservation_id: id, status: status })
      });
      const data = await res.json();
      if (data.success) location.reload();
      else alert('Update failed: ' + (data.message || 'Unknown error'));
    }

    // Auto-logout after 15 min inactivity
    const TIMEOUT_MS = 15 * 60 * 1000;
    const WARNING_MS = 14 * 60 * 1000;
    let logoutTimer, warnTimer;

    function resetTimers() {
      clearTimeout(logoutTimer);
      clearTimeout(warnTimer);
      warnTimer = setTimeout(() => {
        if (confirm('⚠️ You will be logged out in 1 minute due to inactivity. Click OK to stay logged in.')) {
          resetTimers();
        }
      }, WARNING_MS);
      logoutTimer = setTimeout(() => {
        window.location.href = 'admin.php?logout=1';
      }, TIMEOUT_MS);
    }

    ['mousemove','keydown','click','scroll','touchstart'].forEach(evt => {
      document.addEventListener(evt, resetTimers, { passive: true });
    });
    resetTimers();
  </script>

</body>
</html>