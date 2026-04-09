<?php
session_start();
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
      background-image: url('./images/hero.webp');
      background-size: cover;
      background-position: center;
      filter: blur(3px);
      transform: scale(1.1);
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
      font-family: 'Cormorant Garamond', serif;
      font-size: 3em;
      margin-bottom: 10px;
      color: #d4af37;
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
      box-sizing: border-box;
    }

    .login-group input:focus {
      border-color: rgba(212, 175, 55, 0.6);
    }

    .login-btn {
      width: 100%;
      padding: 14px;
      background: #d4af37;
      border: none;
      border-radius: 8px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1em;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-btn:hover {
      background: #fff;
      color: #111;
    }

    .login-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .error-msg {
      color: #ff8a8a;
      margin-bottom: 15px;
      font-size: 0.9em;
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
    }

    .back-home:hover {
      color: #fff;
    }

    .back-home svg {
      width: 18px;
      height: 18px;
      fill: currentColor;
    }

    /* OTP Modal */
    .otp-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.85);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .otp-overlay.active {
      display: flex;
    }

    .otp-box {
      background: #1a1a1a;
      border: 1px solid rgba(212, 175, 55, 0.35);
      border-radius: 20px;
      padding: 40px 36px;
      width: 100%;
      max-width: 380px;
      text-align: center;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
      animation: popUp .35s ease;
    }

    @keyframes popUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .otp-box h3 {
      font-family: 'Great Vibes', cursive;
      color: #d4af37;
      font-size: 2.2em;
      margin: 0 0 6px;
    }

    .otp-box p {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.88em;
      margin: 0 0 24px;
      line-height: 1.5;
    }

    .otp-box p strong {
      color: #d4af37;
    }

    .otp-inputs {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-bottom: 24px;
    }

    .otp-inputs input {
      width: 44px;
      height: 54px;
      text-align: center;
      font-size: 1.4em;
      font-family: 'Cormorant Garamond', serif;
      font-weight: 700;
      background: rgba(255, 255, 255, 0.05);
      border: 1.5px solid rgba(212, 175, 55, 0.25);
      border-radius: 10px;
      color: #fff;
      outline: none;
      transition: border-color .2s;
    }

    .otp-inputs input:focus {
      border-color: #d4af37;
    }

    .otp-error {
      color: #ff8a8a;
      font-size: 0.85em;
      margin-bottom: 16px;
      min-height: 20px;
    }

    .otp-btn {
      width: 100%;
      padding: 13px;
      background: #d4af37;
      border: none;
      border-radius: 8px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1em;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
      margin-bottom: 14px;
    }

    .otp-btn:hover {
      background: #fff;
      color: #111;
    }

    .otp-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .otp-resend {
      color: rgba(255, 255, 255, 0.4);
      font-size: 0.85em;
    }

    .otp-resend button {
      background: none;
      border: none;
      color: #d4af37;
      cursor: pointer;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1em;
      text-decoration: underline;
      padding: 0;
    }

    .otp-resend button:disabled {
      color: rgba(212, 175, 55, 0.4);
      cursor: not-allowed;
      text-decoration: none;
    }

    .otp-timer {
      color: rgba(212, 175, 55, 0.7);
      font-size: 0.85em;
      margin-top: 8px;
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
    <p style="color:rgba(255,255,255,0.5);margin-bottom:30px;">Welcome back, please login.</p>
    <div id="errorBox" class="error-msg"></div>
    <div class="login-group">
      <label>Username</label>
      <input type="text" id="username" placeholder="Enter username" onkeydown="if(event.key==='Enter') performLogin()">
    </div>
    <div class="login-group">
      <label>Password</label>
      <input type="password" id="password" placeholder="Enter password" onkeydown="if(event.key==='Enter') performLogin()">
    </div>
    <button class="login-btn" id="loginBtn" onclick="performLogin()">Sign In</button>
    <p style="margin-top:20px;color:rgba(255,255,255,0.4);">
      Don't have an account? <a href="register.php" style="color:#d4af37;text-decoration:none;">Register here</a>
    </p>
  </div>

  <!-- OTP Modal -->
  <div class="otp-overlay" id="otpOverlay">
    <div class="otp-box">
      <h3>Verify It's You</h3>
      <p>We sent a 6-digit code to<br><strong id="otpEmailDisplay"></strong></p>
      <div class="otp-inputs">
        <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" id="d0" oninput="otpInput(this,0)" onkeydown="otpBack(this,0,event)">
        <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" id="d1" oninput="otpInput(this,1)" onkeydown="otpBack(this,1,event)">
        <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" id="d2" oninput="otpInput(this,2)" onkeydown="otpBack(this,2,event)">
        <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" id="d3" oninput="otpInput(this,3)" onkeydown="otpBack(this,3,event)">
        <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" id="d4" oninput="otpInput(this,4)" onkeydown="otpBack(this,4,event)">
        <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" id="d5" oninput="otpInput(this,5)" onkeydown="otpBack(this,5,event)">
      </div>
      <div class="otp-error" id="otpError"></div>
      <button class="otp-btn" id="verifyBtn" onclick="verifyOTP()">Verify & Sign In</button>
      <div class="otp-resend">
        Didn't receive it? <button id="resendBtn" onclick="resendOTP()">Resend OTP</button>
      </div>
      <div class="otp-timer" id="otpTimer"></div>
    </div>
  </div>

  <script src="./scripts/conection_string/api.js"></script>
  <script>
    let otpEmail = '';
    let timerInterval = null;

    async function performLogin() {
      const user = document.getElementById('username').value.trim();
      const pass = document.getElementById('password').value.trim();
      const errorBox = document.getElementById('errorBox');
      const btn = document.getElementById('loginBtn');

      errorBox.style.display = 'none';

      if (!user || !pass) {
        errorBox.textContent = 'Please fill in all fields.';
        errorBox.style.display = 'block';
        return;
      }

      btn.textContent = 'Checking...';
      btn.disabled = true;

      console.log('[LOGIN] Step 1 — checking credentials for:', user);

      let res;
      try {
        res = await apiFetch('check_credentials', {
          method: 'POST',
          body: JSON.stringify({
            username: user,
            password: pass
          })
        });
        console.log('[LOGIN] check_credentials response:', res);
      } catch (e) {
        console.error('[LOGIN] check_credentials fetch error:', e);
        errorBox.textContent = 'Network error. Is XAMPP running?';
        errorBox.style.display = 'block';
        btn.textContent = 'Sign In';
        btn.disabled = false;
        return;
      }

      if (!res.success) {
        errorBox.textContent = res.message || 'Invalid credentials.';
        errorBox.style.display = 'block';
        btn.textContent = 'Sign In';
        btn.disabled = false;
        return;
      }

      // Credentials OK — send OTP
      otpEmail = res.email;
      console.log('[LOGIN] Credentials OK. Sending OTP to:', otpEmail);

      btn.textContent = 'Sending OTP...';

      let otpRes;
      try {
        otpRes = await apiFetch('send_otp', {
          method: 'POST',
          body: JSON.stringify({
            email: otpEmail
          })
        });
        console.log('[LOGIN] send_otp response:', otpRes);
      } catch (e) {
        console.error('[LOGIN] send_otp fetch error:', e);
        errorBox.textContent = 'Failed to send OTP. Check your internet.';
        errorBox.style.display = 'block';
        btn.textContent = 'Sign In';
        btn.disabled = false;
        return;
      }

      btn.textContent = 'Sign In';
      btn.disabled = false;

      if (!otpRes.success) {
        errorBox.textContent = otpRes.message || 'Failed to send OTP. Check SMTP settings in api.php.';
        errorBox.style.display = 'block';
        return;
      }

      // Show OTP modal
      console.log('[LOGIN] OTP sent. Opening modal.');
      openOTPModal();
    }

    function openOTPModal() {
      document.getElementById('otpEmailDisplay').textContent = otpEmail;
      document.getElementById('otpOverlay').classList.add('active');
      document.getElementById('otpError').textContent = '';
      clearOTPInputs();
      document.getElementById('d0').focus();
      startTimer(300);
    }

    function clearOTPInputs() {
      document.querySelectorAll('.otp-digit').forEach(i => i.value = '');
    }

    function otpInput(el, idx) {
      el.value = el.value.replace(/\D/g, '');
      if (el.value && idx < 5) document.getElementById('d' + (idx + 1)).focus();
      const code = [...document.querySelectorAll('.otp-digit')].map(i => i.value).join('');
      if (code.length === 6) verifyOTP();
    }

    function otpBack(el, idx, e) {
      if (e.key === 'Backspace' && !el.value && idx > 0) {
        document.getElementById('d' + (idx - 1)).focus();
      }
    }

    async function verifyOTP() {
      const code = [...document.querySelectorAll('.otp-digit')].map(i => i.value).join('');
      const errEl = document.getElementById('otpError');
      const btn = document.getElementById('verifyBtn');

      if (code.length < 6) {
        errEl.textContent = 'Please enter the full 6-digit code.';
        return;
      }

      btn.textContent = 'Verifying...';
      btn.disabled = true;
      errEl.textContent = '';

      console.log('[OTP] Verifying code:', code, 'for email:', otpEmail);

      let res;
      try {
        res = await apiFetch('verify_otp_login', {
          method: 'POST',
          body: JSON.stringify({
            email: otpEmail,
            otp: code
          })
        });
        console.log('[OTP] verify_otp_login response:', res);
      } catch (e) {
        console.error('[OTP] verify fetch error:', e);
        errEl.textContent = 'Network error during verification.';
        btn.textContent = 'Verify & Sign In';
        btn.disabled = false;
        return;
      }

      btn.textContent = 'Verify & Sign In';
      btn.disabled = false;

      if (res.success) {
        clearInterval(timerInterval);
        console.log('[OTP] Success! Redirecting to account.php');
        window.location.href = 'account.php';
      } else {
        errEl.textContent = res.message || 'Invalid OTP.';
        clearOTPInputs();
        document.getElementById('d0').focus();
      }
    }

    async function resendOTP() {
      const btn = document.getElementById('resendBtn');
      const errEl = document.getElementById('otpError');
      btn.disabled = true;

      console.log('[OTP] Resending OTP to:', otpEmail);

      const res = await apiFetch('send_otp', {
        method: 'POST',
        body: JSON.stringify({
          email: otpEmail
        })
      });

      console.log('[OTP] resend response:', res);

      if (res.success) {
        errEl.textContent = '';
        clearOTPInputs();
        document.getElementById('d0').focus();
        startTimer(300);
      } else {
        errEl.textContent = res.message || 'Failed to resend.';
      }

      setTimeout(() => btn.disabled = false, 30000);
    }

    function startTimer(seconds) {
      clearInterval(timerInterval);
      const el = document.getElementById('otpTimer');
      el.style.color = 'rgba(212,175,55,0.7)';
      let remaining = seconds;
      timerInterval = setInterval(() => {
        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        el.textContent = `Code expires in ${m}:${s}`;
        if (--remaining < 0) {
          clearInterval(timerInterval);
          el.textContent = 'Code expired. Please request a new one.';
          el.style.color = '#ff8a8a';
        }
      }, 1000);
    }
  </script>

</body>

</html>