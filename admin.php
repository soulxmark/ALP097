<?php
session_start();
require_once './connection.php';

// SECURITY: Only allow Admins
if (!isset($_SESSION['session_status']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// 1. Fetch Summary Stats
$stats = $mysqli->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_revenue
    FROM orders_tbl
")->fetch_assoc();

// 2. Fetch Recent Orders with User Names
$orders_query = "
    SELECT o.*, u.username, u.email 
    FROM orders_tbl o 
    JOIN users_tbl1 u ON o.uid = u.uid 
    ORDER BY o.order_date DESC 
    LIMIT 50";
$orders = $mysqli->query($orders_query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Casa De Manila</title>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --gold: #d4af37;
      --dark: #111;
      --card: #1a1a1a;
    }

    body {
      background: var(--dark);
      color: #fff;
      font-family: 'Cormorant Garamond', serif;
      margin: 0;
      padding: 20px;
    }

    .admin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid rgba(212, 175, 55, 0.3);
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .admin-header h1 {
      font-family: 'Great Vibes', cursive;
      color: var(--gold);
      font-size: 3em;
      margin: 0;
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 40px;
    }

    .stat-box {
      background: var(--card);
      border: 1px solid rgba(212, 175, 55, 0.2);
      padding: 20px;
      border-radius: 15px;
      text-align: center;
    }

    .stat-box h3 {
      color: var(--gold);
      text-transform: uppercase;
      font-size: 0.8em;
      letter-spacing: 2px;
      margin-bottom: 10px;
    }

    .stat-box p {
      font-size: 2.5em;
      font-weight: bold;
      margin: 0;
    }

    /* Table Styles */
    .order-table {
      width: 100%;
      border-collapse: collapse;
      background: var(--card);
      border-radius: 15px;
      overflow: hidden;
    }

    .order-table th {
      background: rgba(212, 175, 55, 0.1);
      color: var(--gold);
      padding: 15px;
      text-align: left;
      text-transform: uppercase;
      font-size: 0.8em;
    }

    .order-table td {
      padding: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .status-pill {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8em;
      font-weight: bold;
    }

    .status-pending {
      background: #f39c12;
      color: #fff;
    }

    .status-completed {
      background: #27ae60;
      color: #fff;
    }

    .revenue-cell {
      color: var(--gold);
      font-weight: bold;
    }

    .btn-action {
      background: transparent;
      border: 1px solid var(--gold);
      color: var(--gold);
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn-action:hover {
      background: var(--gold);
      color: var(--dark);
    }
  </style>
</head>

<body>

  <div class="admin-header">
    <h1>Casa De Manila Admin</h1>
    <div>
      <span>Welcome, Admin | </span>
      <a href="account.php?logout=1" style="color: #ff4d4d; text-decoration: none;">Logout</a>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-box">
      <h3>Total Revenue</h3>
      <p>₱<?php echo number_format($stats['total_revenue'], 2); ?></p>
    </div>
    <div class="stat-box">
      <h3>Pending Orders</h3>
      <p><?php echo $stats['pending_count']; ?></p>
    </div>
    <div class="stat-box">
      <h3>Total Orders</h3>
      <p><?php echo $stats['total_orders']; ?></p>
    </div>
  </div>

  <h2>Recent Activity</h2>
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
          <small style="opacity: 0.5;"><?php echo htmlspecialchars($o['email']); ?></small>
        </td>
        <td><?php echo date('M d, Y H:i', strtotime($o['order_date'])); ?></td>
        <td class="revenue-cell">₱<?php echo number_format($o['total_amount'], 2); ?></td>
        <td>
          <span class="status-pill status-<?php echo $o['status']; ?>">
            <?php echo strtoupper($o['status']); ?>
          </span>
        </td>
        <td style="font-size: 0.9em; max-width: 200px;"><?php echo htmlspecialchars($o['notes']); ?></td>
        <td>
          <?php if ($o['status'] === 'pending'): ?>
          <button class="btn-action" onclick="markAsComplete(<?php echo $o['order_id']; ?>)">Mark Paid</button>
          <?php else: ?>
          <span style="color: #27ae60;">✓ Verified</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
    async function markAsComplete(id) {
      if (!confirm("Are you sure you want to mark Order #" + id + " as completed?")) return;

      const response = await fetch('process_payment.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          order_id: id,
          method: 'Admin Manual Override'
        })
      });

      const res = await response.json();
      if (res.success) location.reload();
      else alert("Update failed: " + res.message);
    }
  </script>

</body>

</html>