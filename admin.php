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
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
  <link rel="stylesheet" href="./styles/admin/admin.css">
</head>

<body>

  <div class="admin-header">
    <h2>Casa De Manila | Admin Dashboard</h2>

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
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Notes</th>
            <th>Action</th>
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
          <tr>
            <td colspan="7" style="text-align:center;opacity:0.4;padding:30px;">No orders yet.</td>
          </tr>
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
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Date</th>
            <th>Time</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
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
          <tr>
            <td colspan="9" style="text-align:center;opacity:0.4;padding:30px;">No reservations yet.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- REPORTS TAB -->
  <div id="tab-reports" class="tab-content">

    <!-- Filter Bar -->
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
      <div style="display:flex;gap:6px;flex-wrap:wrap;">
        <button class="period-btn active" onclick="setPeriod('daily',this)">Daily</button>
        <button class="period-btn" onclick="setPeriod('weekly',this)">Weekly</button>
        <button class="period-btn" onclick="setPeriod('monthly',this)">Monthly</button>
        <button class="period-btn" onclick="setPeriod('annually',this)">Annual</button>
        <button class="period-btn" onclick="setPeriod('custom',this)">📅 Custom</button>
      </div>
      <span id="period-label" style="color:rgba(255,255,255,0.4);font-size:0.9em;"></span>
      <button onclick="exportPDF()" style="margin-left:auto;background:var(--gold);color:#111;border:none;padding:9px 20px;border-radius:8px;font-family:'Cormorant Garamond',serif;font-size:1em;font-weight:bold;cursor:pointer;transition:0.2s;">
        📥 Export as PDF
      </button>
    </div>

    <!-- Custom Date Range (shown only when Custom is selected) -->
    <div id="custom-date-row" style="display:none;align-items:center;gap:12px;margin-bottom:20px;background:var(--card);padding:16px 20px;border-radius:12px;border:1px solid rgba(212,175,55,0.2);flex-wrap:wrap;">
      <label style="color:rgba(255,255,255,0.5);font-size:0.82em;letter-spacing:1px;text-transform:uppercase;">From</label>
      <input type="date" id="custom-from" style="background:rgba(255,255,255,0.07);border:1px solid rgba(212,175,55,0.3);color:#fff;padding:8px 12px;border-radius:8px;font-family:'Cormorant Garamond',serif;font-size:0.95em;outline:none;">
      <label style="color:rgba(255,255,255,0.5);font-size:0.82em;letter-spacing:1px;text-transform:uppercase;">To</label>
      <input type="date" id="custom-to" style="background:rgba(255,255,255,0.07);border:1px solid rgba(212,175,55,0.3);color:#fff;padding:8px 12px;border-radius:8px;font-family:'Cormorant Garamond',serif;font-size:0.95em;outline:none;">
      <button onclick="loadReport('custom')" style="background:var(--gold);color:#111;border:none;padding:8px 18px;border-radius:8px;font-family:'Cormorant Garamond',serif;font-size:0.95em;font-weight:bold;cursor:pointer;">
        Generate
      </button>
    </div>

    <!-- Stats for selected period -->
    <div class="stats-grid" id="report-stats" style="max-width:750px;margin-bottom:28px;"></div>

    <!-- Top Selling Items -->
    <p class="section-title">Top Selling Items</p>
    <div id="report-items" style="background:var(--card);border-radius:14px;padding:24px;max-width:600px;min-height:100px;">
      <p style="opacity:0.4;text-align:center;">Loading...</p>
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
      const res = await fetch('process_payment.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          order_id: id,
          status: status
        })
      });
      const data = await res.json();
      if (data.success) location.reload();
      else alert('Update failed: ' + (data.message || 'Unknown error'));
    }

    async function updateRes(id, status) {
      const label = status === 'confirmed' ? 'confirm' : 'cancel';
      if (!confirm('Are you sure you want to ' + label + ' Reservation #' + id + '?')) return;
      const res = await fetch('update_reservation.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          reservation_id: id,
          status: status
        })
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

    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(evt => {
      document.addEventListener(evt, resetTimers, {
        passive: true
      });
    });
    resetTimers();

    // ── Reports Filter ──
    let currentPeriod = 'daily';

    const today = new Date();
    const todayStr = today.toLocaleDateString('en-PH', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    });

    const PERIOD_LABELS = {
      daily: 'Today — ' + todayStr,
      weekly: 'This Week (Last 7 Days)',
      monthly: today.toLocaleDateString('en-PH', {
        month: 'long',
        year: 'numeric'
      }),
      annually: 'Year ' + today.getFullYear(),
      custom: 'Custom Range'
    };

    // Set default max date on custom inputs to today
    document.addEventListener('DOMContentLoaded', () => {
      const todayISO = today.toISOString().split('T')[0];
      document.getElementById('custom-to').value = todayISO;
      document.getElementById('custom-to').max = todayISO;
      document.getElementById('custom-from').max = todayISO;
      // Set from to start of current month by default
      const firstOfMonth = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
      document.getElementById('custom-from').value = firstOfMonth;
    });

    function setPeriod(period, btn) {
      currentPeriod = period;
      document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const customRow = document.getElementById('custom-date-row');
      if (period === 'custom') {
        customRow.style.display = 'flex';
        document.getElementById('period-label').textContent = 'Select date range below';
        return; // don't auto-load — wait for Generate button
      } else {
        customRow.style.display = 'none';
        document.getElementById('period-label').textContent = PERIOD_LABELS[period];
        loadReport(period);
      }
    }

    async function loadReport(period) {
      document.getElementById('report-items').innerHTML = '<p style="opacity:0.4;text-align:center;padding:20px;">Loading...</p>';
      document.getElementById('report-stats').innerHTML = '';

      let url = 'report_data.php?period=' + period;

      if (period === 'custom') {
        const from = document.getElementById('custom-from').value;
        const to = document.getElementById('custom-to').value;
        if (!from || !to) {
          document.getElementById('report-items').innerHTML = '<p style="color:#e67e22;text-align:center;padding:20px;">Please select both From and To dates.</p>';
          return;
        }
        if (from > to) {
          document.getElementById('report-items').innerHTML = '<p style="color:#e74c3c;text-align:center;padding:20px;">From date cannot be after To date.</p>';
          return;
        }
        url += '&from=' + from + '&to=' + to;
        document.getElementById('period-label').textContent = from + ' → ' + to;
      }

      try {
        const res = await fetch(url);
        const data = await res.json();

        // Stats
        document.getElementById('report-stats').innerHTML = `
          <div class="stat-box"><h3>Orders</h3><p>${data.total_orders}</p></div>
          <div class="stat-box"><h3>Gross Revenue</h3><p>₱${numFmt(data.gross_revenue)}</p></div>
          <div class="stat-box"><h3>Collected</h3><p style="color:#27ae60;">₱${numFmt(data.collected)}</p></div>
          <div class="stat-box"><h3>Pending</h3><p style="color:#e67e22;">${data.pending_orders}</p></div>
        `;

        // Top Items
        if (!data.items || data.items.length === 0) {
          document.getElementById('report-items').innerHTML = '<p style="opacity:0.4;text-align:center;padding:20px;">No data for this period.</p>';
          return;
        }

        const maxQty = data.items[0].qty;
        let html = '';
        data.items.forEach(item => {
          const pct = Math.round((item.qty / maxQty) * 100);
          html += `
            <div class="top-item-row">
              <span style="min-width:160px;">${item.item_name}</span>
              <div class="item-bar-wrap"><div class="item-bar" style="width:${pct}%;"></div></div>
              <span style="min-width:40px;text-align:right;opacity:0.7;">${item.qty}x</span>
              <span class="revenue-cell" style="min-width:90px;text-align:right;">₱${numFmt(item.sales)}</span>
            </div>`;
        });
        document.getElementById('report-items').innerHTML = html;

      } catch (e) {
        document.getElementById('report-items').innerHTML = '<p style="color:#e74c3c;text-align:center;">Failed to load report data.</p>';
      }
    }

    function numFmt(n) {
      return Number(n || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    }

    function exportPDF() {
      let url = 'admin_dashboard.php?period=' + currentPeriod;
      if (currentPeriod === 'custom') {
        const from = document.getElementById('custom-from').value;
        const to = document.getElementById('custom-to').value;
        if (!from || !to) {
          alert('Please select a custom date range first.');
          return;
        }
        url += '&from=' + from + '&to=' + to;
      }
      window.open(url, '_blank');
    }

    // Load daily report when reports tab is clicked
    document.querySelector('[onclick*="reports"]').addEventListener('click', () => {
      if (!document.getElementById('report-stats').innerHTML.trim()) {
        document.getElementById('period-label').textContent = PERIOD_LABELS['daily'];
        loadReport('daily');
      }
    });
  </script>

</body>

</html>