<?php
session_start();
require_once './connection.php';

if (!isset($_SESSION['session_status']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

$today = date('Y-m-d');

// 1. Daily Totals
$summary = $mysqli->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue,
        SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as collected_cash
    FROM orders_tbl 
    WHERE DATE(order_date) = '$today'
")->fetch_assoc();

// 2. Top 5 Best Sellers for Today
$top_items = $mysqli->query("
    SELECT item_name, SUM(quantity) as qty, SUM(subtotal) as sales
    FROM order_items_tbl oi
    JOIN orders_tbl o ON oi.order_id = o.order_id
    WHERE DATE(o.order_date) = '$today'
    GROUP BY item_name ORDER BY qty DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Daily Report - <?php echo $today; ?></title>
  <style>
    body {
      font-family: 'Times New Roman', serif;
      color: #333;
      padding: 40px;
    }

    .report-header {
      text-align: center;
      border-bottom: 2px solid #d4af37;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .report-header h1 {
      font-size: 2.5em;
      margin: 0;
      color: #111;
    }

    .report-header p {
      margin: 5px 0;
      color: #666;
      font-style: italic;
    }

    .stats-grid {
      display: flex;
      justify-content: space-between;
      margin-bottom: 40px;
    }

    .stat-item {
      text-align: center;
      flex: 1;
      border: 1px solid #eee;
      padding: 15px;
    }

    .stat-item span {
      display: block;
      font-size: 0.8em;
      text-transform: uppercase;
      color: #999;
    }

    .stat-item strong {
      font-size: 1.8em;
      color: #111;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th {
      text-align: left;
      border-bottom: 2px solid #111;
      padding: 10px;
      font-size: 0.9em;
    }

    td {
      padding: 10px;
      border-bottom: 1px solid #eee;
    }

    .no-print-btn {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #d4af37;
      color: #fff;
      border: none;
      padding: 10px 20px;
      cursor: pointer;
      border-radius: 5px;
    }

    /* PDF/Print Specifics */
    @media print {
      .no-print-btn {
        display: none;
      }

      body {
        padding: 0;
      }

      @page {
        margin: 1cm;
      }
    }
  </style>
</head>

<body>

  <button class="no-print-btn" onclick="window.print()">📥 Export as PDF</button>

  <div class="report-header">
    <h1>Casa De Manila</h1>
    <p>Daily Sales Summary: <?php echo date('F j, Y'); ?></p>
  </div>

  <div class="stats-grid">
    <div class="stat-item">
      <span>Total Orders</span>
      <strong><?php echo $summary['total_orders'] ?? 0; ?></strong>
    </div>
    <div class="stat-item">
      <span>Gross Revenue</span>
      <strong>₱<?php echo number_format($summary['total_revenue'] ?? 0, 2); ?></strong>
    </div>
    <div class="stat-item">
      <span>Collected (Paid)</span>
      <strong style="color: #27ae60;">₱<?php echo number_format($summary['collected_cash'] ?? 0, 2); ?></strong>
    </div>
  </div>

  <h3>Top Selling Items</h3>
  <table>
    <thead>
      <tr>
        <th>Item Name</th>
        <th>Quantity Sold</th>
        <th>Total Sales</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($top_items as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
        <td><?php echo $item['qty']; ?></td>
        <td>₱<?php echo number_format($item['sales'], 2); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; font-size: 0.8em; color: #999; text-align: center;">
    Report generated on <?php echo date('Y-m-d H:i:s'); ?> by Admin System
  </div>

</body>

</html>