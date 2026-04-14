<?php
// report_data.php — Returns filtered report data as JSON
session_start();
require_once './connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1 || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$period = $_GET['period'] ?? 'daily';

// Build date filter based on period
switch ($period) {
    case 'weekly':
        $where = "DATE(order_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'monthly':
        $where = "MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
        break;
    case 'annually':
        $where = "YEAR(order_date) = YEAR(CURDATE())";
        break;
    default: // daily
        $where = "DATE(order_date) = CURDATE()";
        break;
}

// Summary stats
$stats = $mysqli->query("
    SELECT 
        COUNT(*) as total_orders,
        COALESCE(SUM(total_amount), 0) as gross_revenue,
        COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as collected
    FROM orders_tbl
    WHERE $where
")->fetch_assoc();

// Top 5 items
$items_result = $mysqli->query("
    SELECT oi.item_name, SUM(oi.quantity) as qty, SUM(oi.subtotal) as sales
    FROM order_items_tbl oi
    JOIN orders_tbl o ON oi.order_id = o.order_id
    WHERE $where
    GROUP BY oi.item_name
    ORDER BY qty DESC
    LIMIT 5
");

$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode([
    'period'       => $period,
    'total_orders' => (int)$stats['total_orders'],
    'gross_revenue' => (float)$stats['gross_revenue'],
    'collected'    => (float)$stats['collected'],
    'items'        => $items,
]);
?>