<?php
/* ============================================================
   Casa De Manila — Login Page
   File: login.php
============================================================ */
session_start();
require_once './php/connection.php';

if (isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1) {
    header('Location: account.php');
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $mysqli->prepare(
            "SELECT uid, username, password_us FROM users_tbl1 WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $error = "Invalid username or password.";
        } else {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_us'])) {
                $_SESSION['uid']            = $row['uid'];
                $_SESSION['username']       = $row['username'];
                $_SESSION['session_status'] = 1;
                header('Location: account.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – Casa De Manila</title>
  <link rel="stylesheet" href="./styles/about.css">
  <link rel="stylesheet" href="./styles/auth.css">
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
</head>
<body>

  <a href="./index.html" class="back-home">
    <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
    Back to Home
  </a>

  <div class="navbar scrolled">
    <div class="logo">
      <a href="./index.html">Casa De Manila</a>
      <p>Authenticity You Can Taste</p>
    </div>
  </div>

  <main class="auth-page">
    <div class="auth-card">

      <div class="auth-logo">
        <h1>Casa De Manila</h1>
        <p>Member Portal</p>
      </div>

      <p class="auth-title">Welcome <span>Back</span></p>

      <?php if (!empty($error)): ?>
        <div class="auth-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username"
                 placeholder="Enter your username"
                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                 autocomplete="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
                 placeholder="Enter your password"
                 autocomplete="current-password" required>
          <button type="button" class="toggle-password" data-target="#password">👁</button>
        </div>
        <button type="submit" name="login" class="auth-btn">Sign In</button>
      </form>

      <div class="auth-divider"><span>or</span></div>

      <p class="auth-switch">
        Don't have an account? <a href="./register.php">Create one</a>
      </p>

    </div>
  </main>

  <footer class="footer">
    <div class="footer-container">
      <p>&copy; <?php echo date('Y'); ?> Casa De Manila. All rights reserved.</p>
      <div class="social-links">
        <a href="#">Facebook</a><a href="#">Instagram</a><a href="#">Twitter</a>
      </div>
    </div>
  </footer>

  <script src="./scripts/auth.js"></script>
</body>
</html>