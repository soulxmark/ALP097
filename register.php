<?php
/* ============================================================
   Casa De Manila — Register Page
   File: register.php
============================================================ */
session_start();
require_once 'connection.php';

if (isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1) {
    header('Location: account.php');
    exit;
}

$error   = '';
$success = '';

if (isset($_POST['register'])) {
    $username         = trim($_POST['username']);
    $email            = trim($_POST['email']);
    $password         = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt_check = $mysqli->prepare(
            "SELECT uid FROM users_tbl1 WHERE username = ? OR email = ?"
        );
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Username or email is already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = $mysqli->prepare(
                "INSERT INTO users_tbl1 (username, email, password_us) VALUES (?, ?, ?)"
            );
            $stmt_insert->bind_param("sss", $username, $email, $hashed);

            if ($stmt_insert->execute()) {
                $success = "Account created! You can now <a href='./login.php'>sign in</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register – Casa De Manila</title>
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

      <p class="auth-title">Create <span>Account</span></p>

      <?php if (!empty($error)): ?>
        <div class="auth-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <div class="auth-success"><?php echo $success; ?></div>
      <?php endif; ?>

      <?php if (empty($success)): ?>
      <form method="POST" action="">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username"
                 placeholder="Choose a username"
                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                 autocomplete="username" required>
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email"
                 placeholder="your@email.com"
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                 autocomplete="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
                 placeholder="Create a password"
                 autocomplete="new-password" required>
          <button type="button" class="toggle-password" data-target="#password">👁</button>
          <p class="field-hint">Minimum 6 characters</p>
          <div style="margin-top:8px;background:rgba(255,255,255,0.1);border-radius:6px;height:6px;overflow:hidden;">
            <div id="strength-bar" style="height:100%;width:0;border-radius:6px;transition:all 0.4s;"></div>
          </div>
          <small id="strength-text" style="font-size:0.78em;display:block;margin-top:4px;"></small>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password"
                 placeholder="Repeat your password"
                 autocomplete="new-password" required>
          <small id="match-hint" style="font-size:0.78em;display:block;margin-top:4px;"></small>
        </div>
        <button type="submit" name="register" class="auth-btn">Create Account</button>
      </form>
      <?php endif; ?>

      <div class="auth-divider"><span>or</span></div>

      <p class="auth-switch">
        Already have an account? <a href="./login.php">Sign in</a>
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