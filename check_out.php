<?php
session_start();
require_once './connection.php';

// Protection: Must be logged in
if (!isset($_SESSION['session_status']) || $_SESSION['session_status'] != 1) {
    header('Location: login.php');
    exit;
}

$uid = (int)$_SESSION['uid'];
// For this example, let's assume you have a 'cart' in your session.
// If you don't have a cart system yet, we should build that next!
$cart = $_SESSION['cart'] ?? []; 
$total = 0;
foreach($cart as $item) { $total += $item['price'] * $item['quantity']; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    $method = $_POST['payment_method']; // 'Cash' or 'QR'
    $notes = $_POST['notes'] ?? '';

    $stmt = $mysqli->prepare("INSERT INTO orders_tbl (uid, total_amount, status, payment_method, notes, order_date) VALUES (?, ?, 'pending', ?, ?, NOW())");
    $stmt->bind_param("idss", $uid, $total, $method, $notes);
    $stmt->execute();
    
    // Clear cart and redirect
    unset($_SESSION['cart']);
    header('Location: account.php?order_success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Secure Checkout | Casa De Manila</title>
  <link rel="stylesheet" href="./styles/menu.css">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #111;
      color: #fff;
      font-family: 'Cormorant Garamond', serif;
      padding: 50px 20px;
    }

    .checkout-box {
      max-width: 600px;
      margin: 0 auto;
      background: #1a1a1a;
      padding: 40px;
      border: 1px solid #d4af37;
      border-radius: 15px;
    }

    h2 {
      font-family: 'Great Vibes', cursive;
      color: #d4af37;
      font-size: 3em;
      text-align: center;
    }

    .payment-options {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin: 25px 0;
    }

    .pay-card {
      border: 1px solid rgba(212, 175, 55, 0.3);
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      cursor: pointer;
      transition: 0.3s;
    }

    .pay-card.active {
      background: #d4af37;
      color: #111;
      border-color: #fff;
    }

    #qr-section {
      display: none;
      text-align: center;
      margin-top: 20px;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      color: #111;
    }

    #qr-section img {
      width: 200px;
      height: 200px;
    }

    .confirm-btn {
      width: 100%;
      padding: 15px;
      background: #d4af37;
      border: none;
      color: #111;
      font-weight: bold;
      font-size: 1.1em;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 20px;
    }

    input[type="radio"] {
      display: none;
    }
  </style>
</head>

<body>

  <div class="checkout-box">
    <h2>Checkout</h2>
    <p style="text-align:center;">Total Amount: <span style="color:#d4af37; font-size:1.5em;">₱<?php echo number_format($total, 2); ?></span></p>

    <form method="POST" id="checkoutForm">
      <p>Select Payment Method:</p>
      <div class="payment-options">
        <label class="pay-card active">
          <input type="radio" name="payment_method" value="Cash" checked onclick="toggleQR(false)">
          💵 Cash on Pickup
        </label>
        <label class="pay-card">
          <input type="radio" name="payment_method" value="QR" onclick="toggleQR(true)">
          📱 QR Code / E-Wallet
        </label>
      </div>

      <div id="qr-section">
        <p><strong>Scan to Pay via GCash/Maya</strong></p>
        <img src="./images/payments/your_gcash_qr.png" alt="Payment QR Code">
        <p style="font-size:0.8em; margin-top:10px;">Please save your screenshot for verification.</p>
      </div>

      <textarea name="notes" placeholder="Special instructions..." style="width:100%; background:rgba(255,255,255,0.05); color:#fff; border:1px solid #333; padding:10px; margin-top:15px; border-radius:8px;"></textarea>

      <button type="submit" name="confirm_order" class="confirm-btn">Place Order Now</button>
    </form>
  </div>

  <script>
    function toggleQR(show) {
      const qrSection = document.getElementById('qr-section');
      qrSection.style.display = show ? 'block' : 'none';

      // UI Visual toggle
      document.querySelectorAll('.pay-card').forEach(card => {
        card.classList.remove('active');
      });
      event.currentTarget.parentElement.classList.add('active');
    }
  </script>

</body>

</html>