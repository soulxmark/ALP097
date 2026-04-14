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
  <style>
    body { font-family: 'Times New Roman', serif; color: #333; padding: 40px; background: #fff; }

    .no-print { position: fixed; top: 20px; right: 20px; display: flex; gap: 10px; z-index: 99; }
    .btn-print { background: #d4af37; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-size: 0.95em; }
    .btn-back  { background: #111; color: #d4af37; border: 1px solid #d4af37; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-size: 0.95em; text-decoration: none; }

    /* Period switcher (no-print) */
    .period-bar { display: flex; gap: 8px; margin-bottom: 20px; }
    .period-link {
      padding: 6px 16px; border: 1px solid #d4af37; border-radius: 6px;
      text-decoration: none; color: #d4af37; font-size: 0.85em; transition: 0.2s;
    }
    .period-link:hover, .period-link.active { background: #d4af37; color: #fff; }

    .report-header { text-align: center; border-bottom: 2px solid #d4af37; padding-bottom: 20px; margin-bottom: 30px; }
    .report-header h1 { font-size: 2.5em; margin: 0; color: #111; }
    .report-header h2 { font-size: 1.2em; color: #d4af37; margin: 8px 0 4px; font-weight: normal; letter-spacing: 2px; text-transform: uppercase; }
    .report-header p { margin: 4px 0; color: #666; font-style: italic; font-size: 0.9em; }

    .stats-grid { display: flex; justify-content: space-between; gap: 10px; margin-bottom: 32px; }
    .stat-item { text-align: center; flex: 1; border: 1px solid #eee; padding: 16px; border-radius: 6px; }
    .stat-item span { display: block; font-size: 0.75em; text-transform: uppercase; color: #999; letter-spacing: 1px; margin-bottom: 6px; }
    .stat-item strong { font-size: 1.6em; color: #111; }

    h3 { font-size: 1em; letter-spacing: 2px; text-transform: uppercase; color: #333; border-left: 3px solid #d4af37; padding-left: 10px; margin: 28px 0 12px; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 0.92em; }
    th { text-align: left; border-bottom: 2px solid #111; padding: 9px 10px; font-size: 0.85em; }
    td { padding: 9px 10px; border-bottom: 1px solid #eee; }
    .gold { color: #d4af37; font-weight: bold; }

    .status-badge { padding: 2px 9px; border-radius: 12px; font-size: 0.78em; font-weight: bold; }
    .s-completed { background: #d4edda; color: #155724; }
    .s-pending   { background: #fff3cd; color: #856404; }
    .s-cancelled { background: #f8d7da; color: #721c24; }
    .s-preparing { background: #e2d9f3; color: #4a235a; }

    .footer-note { margin-top: 40px; border-top: 1px solid #eee; padding-top: 16px; font-size: 0.78em; color: #aaa; text-align: center; }

    @media print {
      .no-print, .period-bar { display: none !important; }
      body { padding: 0; }
      @page { margin: 1.2cm; }
    }
  </style>
</head>
<body>

  <!-- Buttons (hidden on print) -->
  <div class="no-print">
    <a href="admin.php" class="btn-back">← Dashboard</a>
    <button class="btn-print" onclick="window.print()">📥 Print / PDF</button>
  </div>

  <!-- Period switcher (hidden on print) -->
  <div class="period-bar no-print">
    <a href="?period=daily"    class="period-link <?php echo $period==='daily'    ?'active':''; ?>">Daily</a>
    <a href="?period=weekly"   class="period-link <?php echo $period==='weekly'   ?'active':''; ?>">Weekly</a>
    <a href="?period=monthly"  class="period-link <?php echo $period==='monthly'  ?'active':''; ?>">Monthly</a>
    <a href="?period=annually" class="period-link <?php echo $period==='annually' ?'active':''; ?>">Annual</a>
  </div>

  <!-- Report Header -->
  <div class="report-header">
    <h1>Casa De Manila</h1>
    <h2><?php echo $label; ?></h2>
    <p>SM City Manila, Ermita, Manila &nbsp;|&nbsp; reservations@casamanila.ph</p>
  </div>

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
      <tr><td colspan="4" style="text-align:center;color:#aaa;padding:16px;">No items sold in this period.</td></tr>
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
      <tr><td colspan="5" style="text-align:center;color:#aaa;padding:16px;">No orders in this period.</td></tr>
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