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

      <div class="auth-error" id="loginError" style="display:none;"></div>

      <form id="loginForm">
        <div class="form-group">
          <label>Username</label>
          <input type="text" id="username" placeholder="Enter your username"
                 autocomplete="username" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" id="password" placeholder="Enter your password"
                 autocomplete="current-password" required>
          <button type="button" class="toggle-password" onclick="togglePw()">👁</button>
        </div>
        <button type="submit" class="auth-btn" id="loginBtn">Sign In</button>
      </form>

      <div class="auth-divider"><span>or</span></div>

      <p class="auth-switch">
        Don't have an account? <a href="./register.html">Create one</a>
      </p>

    </div>
  </main>

  <footer class="footer">
    <div class="footer-container">
      <p>&copy; 2026 Casa De Manila. All rights reserved.</p>
    </div>
  </footer>

  <script src="./scripts/api.js"></script>
  <script>
    // Redirect if already logged in
    checkAuth().then(user => {
      if (user) window.location.href = 'account.html';
    });

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn      = document.getElementById('loginBtn');
      const errorDiv = document.getElementById('loginError');
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();

      btn.disabled    = true;
      btn.textContent = 'Signing in...';
      errorDiv.style.display = 'none';

      const res = await Auth.login({ username, password });

      if (res.success) {
        showToast('✅ Welcome back, ' + res.user.username + '!', 'success');
        setTimeout(() => { window.location.href = 'account.html'; }, 800);
      } else {
        errorDiv.textContent   = res.message;
        errorDiv.style.display = 'block';
        btn.disabled    = false;
        btn.textContent = 'Sign In';
      }
    });

    function togglePw() {
      const pw = document.getElementById('password');
      const btn = document.querySelector('.toggle-password');
      pw.type  = pw.type === 'password' ? 'text' : 'password';
      btn.textContent = pw.type === 'password' ? '👁' : '🙈';
    }
  </script>
</body>
</html>