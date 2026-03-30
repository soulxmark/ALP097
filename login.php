<?php
session_start();
// If already logged in, jump to account
if (isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1) {
    header('Location: account.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Casa De Manila</title>
  <link rel="stylesheet" href="./styles/menu.css">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #111;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      font-family: 'Cormorant Garamond', serif;
      /* Required to contain the blurred background layer */
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      /* Your specific image path */
      background-image: url('./images/hero.webp');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;

      /* The Blur Effect */
      filter: blur(3px);

      /* Scale hides the 'white edges' caused by the blur */
      transform: scale(1.1);

      /* Sends it behind your text/content */
      z-index: -1;
    }

    .login-container {
      background: #1a1a1a;
      border: 1px solid rgba(212, 175, 55, 0.3);
      padding: 40px;
      border-radius: 20px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
      text-align: center;
    }

    .login-container h2 {
      font-family: 'Great Vibes', cursive;
      color: #d4af37;
      font-size: 3em;
      margin-bottom: 10px;
    }

    .login-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .login-group label {
      color: #d4af37;
      display: block;
      margin-bottom: 8px;
      font-size: 0.9em;
      letter-spacing: 1px;
    }

    .login-group input {
      width: 100%;
      padding: 12px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 8px;
      color: #fff;
      outline: none;
    }

    .login-btn {
      width: 100%;
      padding: 14px;
      background: #d4af37;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-btn:hover {
      background: #fff;
      color: #111;
    }

    .error-msg {
      color: #ff8a8a;
      margin-bottom: 15px;
      display: none;
    }
    .back-home {
      position: fixed;
      top: 22px;
      left: 28px;
      color: #d4af37;
      text-decoration: none;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1em;
      letter-spacing: 1px;
      z-index: 999;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: color .3s;
      box-shadow: 0 4px 5px rgba(212, 175, 55, 0.3);
    }

    .back-home:hover {
      color: #fff;
      box-shadow: 0 6px 10px rgba(212, 175, 55, 0.5);
    }

    .back-home svg {
      width: 18px;
      height: 18px;
      fill: currentColor;
    }
  </style>
</head>

<body>
   <a href="./index.php" class="back-home">
    <svg viewBox="0 0 24 24">
      <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
    </svg>
    Back to Home
  </a>

  <div class="login-container">
    <h2>Casa De Manila</h2>
    <p style="color:rgba(255,255,255,0.5); margin-bottom:30px;">Welcome back, please login.</p>

    <div id="errorBox" class="error-msg"></div>

    <div class="login-group">
      <label>Username</label>
      <input type="text" id="username" placeholder="Enter username">
    </div>
    <div class="login-group">
      <label>Password</label>
      <input type="password" id="password" placeholder="Enter password">
    </div>

    <button class="login-btn" onclick="performLogin()">Sign In</button>

    <p style="margin-top:20px; color:rgba(255,255,255,0.4);">
      Don't have an account? <a href="register.php" style="color:#d4af37; text-decoration:none;">Register here</a>
    </p>
  </div>

  <script src="./scripts/conection_string/api.js"></script>
  <script>
    async function performLogin() {
      const user = document.getElementById('username').value;
      const pass = document.getElementById('password').value;
      const errorBox = document.getElementById('errorBox');

      // Using your existing Auth object from api.js
      const res = await Auth.login({
        username: user,
        password: pass
      });

      if (res.success) {
        // Redirect to account page once session is set
        window.location.href = 'account.php';
      } else {
        errorBox.textContent = res.message || "Invalid credentials";
        errorBox.style.display = 'block';
      }
    }
  </script>

</body>

</html>