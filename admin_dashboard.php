<?php
// admin_dashboard.php — Printable Sales Report
session_start();
require_once './connection.php';
require_once './session_check.php';

if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1 || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$period = $_GET['period'] ?? 'daily';
$from   = preg_replace('/[^0-9\-]/', '', $_GET['from'] ?? '');
$to     = preg_replace('/[^0-9\-]/', '', $_GET['to']   ?? '');

// Build date filter and label
switch ($period) {
    case 'weekly':
        $where       = "DATE(order_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $where_items = "DATE(o.order_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $label       = 'Weekly Report — Last 7 Days';
        break;
    case 'monthly':
        $where       = "MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
        $where_items = "MONTH(o.order_date) = MONTH(CURDATE()) AND YEAR(o.order_date) = YEAR(CURDATE())";
        $label       = 'Monthly Report — ' . date('F Y');
        break;
    case 'annually':
        $where       = "YEAR(order_date) = YEAR(CURDATE())";
        $where_items = "YEAR(o.order_date) = YEAR(CURDATE())";
        $label       = 'Annual Report — ' . date('Y');
        break;
    case 'custom':
        if ($from && $to) {
            $where       = "DATE(order_date) BETWEEN '$from' AND '$to'";
            $where_items = "DATE(o.order_date) BETWEEN '$from' AND '$to'";
            $label       = 'Report Date | ' . date('M j, Y', strtotime($from)) . '  to  ' . date('M j, Y', strtotime($to));
        } else {
            $where       = "DATE(order_date) = CURDATE()";
            $where_items = "DATE(o.order_date) = CURDATE()";
            $label       = 'Daily Report — ' . date('F j, Y');
        }
        break;
    default: // daily
        $where       = "DATE(order_date) = CURDATE()";
        $where_items = "DATE(o.order_date) = CURDATE()";
        $label       = 'Daily Report — ' . date('F j, Y');
        break;
}

// Summary
$summary = $mysqli->query("
    SELECT 
        COUNT(*) as total_orders,
        COALESCE(SUM(total_amount), 0) as total_revenue,
        COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as collected_cash,
        COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_orders,
        COALESCE(SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END), 0) as cancelled_orders
    FROM orders_tbl
    WHERE $where
")->fetch_assoc();

// Top items
$top_items = $mysqli->query("
    SELECT oi.item_name, SUM(oi.quantity) as qty, SUM(oi.subtotal) as sales
    FROM order_items_tbl oi
    JOIN orders_tbl o ON oi.order_id = o.order_id
    WHERE $where_items
    GROUP BY oi.item_name
    ORDER BY qty DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Orders breakdown
$orders = $mysqli->query("
    SELECT o.order_id, o.order_date, o.total_amount, o.status, u.username
    FROM orders_tbl o
    JOIN users_tbl1 u ON o.uid = u.uid
    WHERE $where
    ORDER BY o.order_date DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php echo $label; ?> | Casa De Manila</title>
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
  <link rel="stylesheet" href="./styles/admin/admin_dashboard.css">
</head>

<body>

  <!-- Buttons (hidden on print) -->
  <div class="no-print">
    <a href="admin.php" class="btn-back">← Dashboard</a>
    <button class="btn-print" onclick="window.print()">📥 Print / PDF</button>
  </div>

  <!-- Report Header -->
  <div class="report-header">
    <h1>Casa De Manila</h1>
    <h2><?php echo $label; ?></h2>
    <p>SM City Manila, Ermita, Manila &nbsp;|&nbsp; reservations@casamanila.ph</p>
  </div>
 <!-- Period switcher (hidden on print) -->
  <div class="period-bar no-print">
    <?php if ($period === 'custom'): ?>
    <span class="period-link active">Custom: <?php echo htmlspecialchars($label); ?></span>
    <?php endif; ?>
  </div>
  <bR>
  <!-- Summary Stats -->
  <div class="stats-grid">

    <div class="stat-item">
      <span>Total Orders</span>
      <strong><?php echo $summary['total_orders']; ?></strong>
    </div>
    <div class="stat-item">
      <span>Gross Revenue</span>
      <strong>₱<?php echo number_format($summary['total_revenue'], 2); ?></strong>
    </div>
    <div class="stat-item">
      <span>Collected (Paid)</span>
      <strong style="color:#27ae60;">₱<?php echo number_format($summary['collected_cash'], 2); ?></strong>
    </div>
    <div class="stat-item">
      <span>Pending</span>
      <strong style="color:#e67e22;"><?php echo $summary['pending_orders']; ?></strong>
    </div>
    <div class="stat-item">
      <span>Cancelled</span>
      <strong style="color:#c0392b;"><?php echo $summary['cancelled_orders']; ?></strong>
    </div>
  </div>

  <!-- Top Selling Items -->
  <h3>Top Selling Items</h3>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Item Name</th>
        <th>Qty Sold</th>
        <th>Total Sales</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($top_items)): ?>
      <tr>
        <td colspan="4" style="text-align:center;color:#aaa;padding:16px;">No items sold in this period.</td>
      </tr>
      <?php else: ?>
      <?php foreach ($top_items as $i => $item): ?>
      <tr>
        <td style="color:#aaa;"><?php echo $i + 1; ?></td>
        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
        <td><?php echo $item['qty']; ?></td>
        <td class="gold">₱<?php echo number_format($item['sales'], 2); ?></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Orders Breakdown -->
  <h3>Orders Breakdown</h3>
  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Date & Time</th>
        <th>Amount</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($orders)): ?>
      <tr>
        <td colspan="5" style="text-align:center;color:#aaa;padding:16px;">No orders in this period.</td>
      </tr>
      <?php else: ?>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td>#<?php echo str_pad($o['order_id'], 4, '0', STR_PAD_LEFT); ?></td>
        <td><?php echo htmlspecialchars($o['username']); ?></td>
        <td><?php echo date('M d, Y g:i A', strtotime($o['order_date'])); ?></td>
        <td class="gold">₱<?php echo number_format($o['total_amount'], 2); ?></td>
        <td>
          <span class="status-badge s-<?php echo $o['status']; ?>">
            <?php echo ucfirst($o['status']); ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer-note">
    Report generated on <?php echo date('F j, Y \a\t g:i A'); ?> &nbsp;·&nbsp;
    Period: <?php echo htmlspecialchars($label); ?> &nbsp;·&nbsp;
    Casa De Manila Admin System
  </div>

</body>

</html>