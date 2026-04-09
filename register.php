<?php
session_start();
require_once './connection.php';

// Already logged in → go to account
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
  <title>Create Account – Casa De Manila</title>
  <link rel="stylesheet" href="./styles/about.css">
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Cormorant Garamond', serif;
      background: #0d0d0d;
      min-height: 100vh;
    }

    .auth-page {
      min-height: 100vh;
      background: url('./images/hero.webp') no-repeat center center / cover;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 110px 20px 60px;
      position: relative;
    }

    .auth-page::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.65);
    }

    .register-card {
      position: relative;
      z-index: 1;
      background: rgba(10, 10, 10, 0.85);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 24px;
      padding: 50px 48px;
      width: 100%;
      max-width: 640px;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
      animation: cardUp .6s ease both;
    }

    @keyframes cardUp {
      from {
        opacity: 0;
        transform: translateY(32px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .auth-logo {
      text-align: center;
      margin-bottom: 28px;
    }

    .auth-logo h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 3em;
      color: #d4af37;
      line-height: 1;
      text-shadow: 0 4px 20px rgba(212, 175, 55, .3);
    }

    .auth-logo p {
      color: rgba(255, 255, 255, .45);
      font-size: .78em;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      margin-top: 5px;
    }

    .step-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 28px;
    }

    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
    }

    .step-circle {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .07);
      border: 2px solid rgba(212, 175, 55, .22);
      color: rgba(255, 255, 255, .35);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .9em;
      font-weight: 700;
      transition: all .4s;
    }

    .step.active .step-circle {
      background: #d4af37;
      border-color: #d4af37;
      color: #111;
      box-shadow: 0 0 16px rgba(212, 175, 55, .4);
    }

    .step.done .step-circle {
      background: rgba(212, 175, 55, .18);
      border-color: #d4af37;
      color: #d4af37;
    }

    .step-label {
      font-size: .68em;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, .28);
      transition: color .4s;
    }

    .step.active .step-label {
      color: #d4af37;
    }

    .step.done .step-label {
      color: rgba(212, 175, 55, .55);
    }

    .step-line {
      flex: 1;
      height: 2px;
      background: rgba(212, 175, 55, .13);
      margin: 0 8px 22px;
      max-width: 70px;
      transition: background .4s;
    }

    .step-line.done {
      background: rgba(212, 175, 55, .5);
    }

    .progress-bar-wrap {
      height: 3px;
      background: rgba(255, 255, 255, .06);
      border-radius: 2px;
      margin-bottom: 30px;
      overflow: hidden;
    }

    .progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #d4af37, #e8c84a);
      border-radius: 2px;
      transition: width .5s ease;
    }

    .section-title {
      font-size: 1em;
      color: rgba(255, 255, 255, .38);
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-title::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(212, 175, 55, .13);
    }

    .form-panel {
      display: none;
    }

    .form-panel.active {
      display: block;
      animation: panelIn .4s ease;
    }

    @keyframes panelIn {
      from {
        opacity: 0;
        transform: translateX(16px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-row.full {
      grid-template-columns: 1fr;
    }

    .form-group {
      margin-bottom: 18px;
      position: relative;
    }

    .form-group label {
      display: flex;
      align-items: center;
      gap: 6px;
      color: rgba(255, 255, 255, .52);
      font-size: .78em;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .form-group label .req {
      color: #d4af37;
      font-size: 1.1em;
      line-height: 1;
    }

    .form-group label .opt {
      color: rgba(255, 255, 255, .22);
      font-size: .85em;
      font-style: italic;
      text-transform: none;
      letter-spacing: 0;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 15px;
      background: rgba(255, 255, 255, .06);
      border: 1px solid rgba(212, 175, 55, .2);
      border-radius: 11px;
      color: #fff;
      font-size: 1em;
      font-family: 'Cormorant Garamond', serif;
      outline: none;
      transition: border-color .3s, background .3s, box-shadow .3s;
    }

    .form-group select option {
      background: #111;
      color: #fff;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: rgba(255, 255, 255, .2);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: #d4af37;
      background: rgba(255, 255, 255, .1);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, .08);
    }

    .input-wrap {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(212, 175, 55, .45);
      font-size: .95em;
      pointer-events: none;
    }

    .input-wrap input,
    .input-wrap select {
      padding-left: 40px;
    }

    .toggle-pw {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: rgba(255, 255, 255, .3);
      font-size: 1em;
      cursor: pointer;
      transition: color .2s;
      padding: 0;
    }

    .toggle-pw:hover {
      color: #d4af37;
    }

    .field-hint {
      font-size: .75em;
      color: rgba(255, 255, 255, .28);
      margin-top: 5px;
    }

    .field-error {
      font-size: .75em;
      color: #e74c3c;
      margin-top: 5px;
      display: none;
    }

    .strength-bar-wrap {
      height: 5px;
      background: rgba(255, 255, 255, .07);
      border-radius: 4px;
      overflow: hidden;
      margin-top: 8px;
    }

    #strengthBar {
      height: 100%;
      width: 0;
      border-radius: 4px;
      transition: width .4s, background .4s;
    }

    .gender-group {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .gender-option {
      flex: 1;
      min-width: 80px;
    }

    .gender-option input[type="radio"] {
      display: none;
    }

    .gender-option label {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      padding: 10px 12px;
      background: rgba(255, 255, 255, .06);
      border: 1px solid rgba(212, 175, 55, .18);
      border-radius: 10px;
      color: rgba(255, 255, 255, .45);
      cursor: pointer;
      transition: all .3s;
      font-size: .95em;
      text-transform: none;
      letter-spacing: 0;
    }

    .gender-option input[type="radio"]:checked+label {
      background: rgba(212, 175, 55, .14);
      border-color: #d4af37;
      color: #d4af37;
    }

    .terms-row {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 14px 16px;
      background: rgba(212, 175, 55, .05);
      border: 1px solid rgba(212, 175, 55, .14);
      border-radius: 12px;
      margin-bottom: 20px;
    }

    .terms-row input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: #d4af37;
      margin-top: 2px;
      flex-shrink: 0;
      cursor: pointer;
    }

    .terms-row label {
      color: rgba(255, 255, 255, .52);
      font-size: .9em;
      line-height: 1.6;
      cursor: pointer;
    }

    .terms-row label a {
      color: #d4af37;
      text-decoration: none;
    }

    .summary-box {
      background: rgba(255, 255, 255, .04);
      border: 1px solid rgba(212, 175, 55, .14);
      border-radius: 14px;
      padding: 18px 20px;
      margin-bottom: 20px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255, 255, 255, .04);
      font-size: .95em;
      gap: 12px;
    }

    .summary-row:last-child {
      border-bottom: none;
    }

    .s-label {
      color: rgba(255, 255, 255, .38);
      font-size: .75em;
      letter-spacing: 1px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .s-value {
      color: #fff;
      font-weight: 600;
      text-align: right;
      word-break: break-word;
    }

    .btn-row {
      display: flex;
      gap: 12px;
      margin-top: 8px;
    }

    .btn-back {
      flex: 1;
      padding: 13px;
      background: transparent;
      border: 1px solid rgba(212, 175, 55, .28);
      border-radius: 11px;
      color: rgba(255, 255, 255, .55);
      font-family: 'Cormorant Garamond', serif;
      font-size: 1em;
      cursor: pointer;
      transition: all .3s;
    }

    .btn-back:hover {
      border-color: #d4af37;
      color: #d4af37;
    }

    .btn-next,
    .btn-submit {
      flex: 2;
      padding: 13px;
      background: #d4af37;
      border: none;
      border-radius: 11px;
      color: #111;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.05em;
      font-weight: 700;
      letter-spacing: 1px;
      cursor: pointer;
      transition: background .3s, transform .2s;
    }

    .btn-next:hover,
    .btn-submit:hover {
      background: #e8c84a;
      transform: translateY(-2px);
    }

    .btn-submit:disabled {
      opacity: .6;
      cursor: not-allowed;
      transform: none;
    }

    .flash-error {
      background: rgba(220, 53, 69, .14);
      border: 1px solid rgba(220, 53, 69, .4);
      color: #ff8a8a;
      padding: 11px 15px;
      border-radius: 10px;
      font-size: .9em;
      margin-bottom: 18px;
      display: none;
    }

    .flash-success {
      background: rgba(40, 167, 69, .14);
      border: 1px solid rgba(40, 167, 69, .4);
      color: #90ee90;
      padding: 18px 20px;
      border-radius: 14px;
      font-size: 1em;
      margin-bottom: 20px;
      text-align: center;
      line-height: 1.8;
      display: none;
    }

    .flash-success a {
      color: #d4af37;
      font-weight: 700;
      text-decoration: none;
    }

    .signin-link {
      text-align: center;
      color: rgba(255, 255, 255, .38);
      font-size: .95em;
      margin-top: 22px;
    }

    .signin-link a {
      color: #d4af37;
      font-weight: 600;
      text-decoration: none;
    }

    .signin-link a:hover {
      color: #fff;
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

    .navbar.scrolled {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 100;
      background: rgba(0, 0, 0, .9);
      padding: 14px 40px;
      display: flex;
      align-items: center;
    }

    .navbar .logo a {
      font-family: 'Cormorant Garamond', serif;
      font-size: 40px;
      color: #d4af37;
      text-decoration: none;
    }

    .navbar .logo p {
      font-size: 12px;
      color: rgba(255, 255, 255, .38);
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    @media (max-width: 600px) {
      .register-card {
        padding: 36px 20px;
      }

      .form-row {
        grid-template-columns: 1fr;
      }
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

  <div class="navbar scrolled">
    <div class="logo">
      <a href="./index.php">Casa De Manila</a>
      <p>Authenticity You Can Taste</p>
    </div>
  </div>

  <main class="auth-page">
    <div class="register-card">

      <div class="auth-logo">
        <h1>Sign Up</h1>
        <p>Create Your Account</p>
      </div>

      <div class="step-indicator">
        <div class="step active" id="stepDot1">
          <div class="step-circle">1</div>
          <div class="step-label">Account</div>
        </div>
        <div class="step-line" id="stepLine1"></div>
        <div class="step" id="stepDot2">
          <div class="step-circle">2</div>
          <div class="step-label">Personal</div>
        </div>
        <div class="step-line" id="stepLine2"></div>
        <div class="step" id="stepDot3">
          <div class="step-circle">3</div>
          <div class="step-label">Confirm</div>
        </div>
      </div>

      <div class="progress-bar-wrap">
        <div class="progress-bar" id="progressBar" style="width:33%"></div>
      </div>

      <div class="flash-error" id="flashError"></div>
      <div class="flash-success" id="flashSuccess"></div>

      <!-- STEP 1 -->
      <div class="form-panel active" id="panel1">
        <p class="section-title">Account Info</p>
        <div class="form-row">
          <div class="form-group">
            <label>Username <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="text" id="username" placeholder="e.g. juandelacruz" autocomplete="username">
            </div>
            <p class="field-hint">At least 3 characters, no spaces</p>
            <div class="field-error" id="errUsername"></div>
          </div>
          <div class="form-group">
            <label>Email Address <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="email" id="email" placeholder="you@email.com" autocomplete="email">
            </div>
            <div class="field-error" id="errEmail"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Password <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="password" id="password" placeholder="Min. 6 characters" autocomplete="new-password" oninput="checkStrength()">
              <button type="button" class="toggle-pw" onclick="togglePw('password',this)">👁</button>
            </div>
            <div class="strength-bar-wrap">
              <div id="strengthBar"></div>
            </div>
            <small id="strengthText" style="font-size:.75em;display:block;margin-top:4px;min-height:16px;"></small>
            <div class="field-error" id="errPassword"></div>
          </div>
          <div class="form-group">
            <label>Confirm Password <span class="req">*</span></label>
            <div class="input-wrap">
                <input type="password" id="confirmPassword" placeholder="Repeat password" autocomplete="new-password" oninput="checkMatch()">
              <button type="button" class="toggle-pw" onclick="togglePw('confirmPassword',this)">👁</button>
            </div>
            <small id="matchHint" style="font-size:.75em;display:block;margin-top:4px;min-height:16px;"></small>
            <div class="field-error" id="errConfirm"></div>
          </div>
        </div>
        <div class="btn-row">
          <button class="btn-next" onclick="nextStep(1)">Next: Personal Info →</button>
        </div>
      </div>

      <!-- STEP 2 -->
      <div class="form-panel" id="panel2">
        <p class="section-title">Personal Info</p>
        <div class="form-row">
          <div class="form-group">
            <label>First Name <span class="req">*</span></label>
            <div class="input-wrap">
             
              <input type="text" id="firstName" placeholder="Juan" autocomplete="given-name">
            </div>
            <div class="field-error" id="errFirstName"></div>
          </div>
          <div class="form-group">
            <label>Last Name <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="text" id="lastName" placeholder="dela Cruz" autocomplete="family-name">
            </div>
            <div class="field-error" id="errLastName"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Phone Number <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="tel" id="phone" placeholder="09XX XXX XXXX" autocomplete="tel">
            </div>
            <p class="field-hint">Philippine number format</p>
            <div class="field-error" id="errPhone"></div>
          </div>
          <div class="form-group">
            <label>Birthday <span class="opt">Required</span></label>
            <div class="input-wrap">
              <input type="date" id="birthday" autocomplete="bday">
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Gender <span class="opt">Required</span></label>
          <div class="gender-group">
            <div class="gender-option"><input type="radio" name="gender" id="gMale" value="Male"><label for="gMale">♂ Male</label></div>
            <div class="gender-option"><input type="radio" name="gender" id="gFemale" value="Female"><label for="gFemale">♀ Female</label></div>
            <div class="gender-option"><input type="radio" name="gender" id="gOther" value="Prefer not to say"><label for="gOther">○ Prefer not to say</label></div>
          </div>
        </div>
        <div class="form-group">
          <label>Address <span class="opt">Required</span></label>
          <div class="input-wrap">
            <span class="input-icon" style="top:14px;transform:none;">📍</span>
            <textarea id="address" placeholder="Street, Barangay, City, Province" autocomplete="street-address" style="padding-left:40px;"></textarea>
          </div>
        </div>
        <div class="btn-row">
          <button class="btn-back" onclick="prevStep(2)">← Back</button>
          <button class="btn-next" onclick="nextStep(2)">Review & Confirm →</button>
        </div>
      </div>

      <!-- STEP 3 -->
      <div class="form-panel" id="panel3">
        <p class="section-title">✅ Review & Confirm</p>
        <div class="summary-box" id="summaryBox"></div>
        <div class="terms-row">
          <input type="checkbox" id="terms">
          <label for="terms">
            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> of Casa De Manila.
            I consent to my personal data being used to manage my account and orders.
          </label>
        </div>
        <div class="field-error" id="errTerms" style="margin-bottom:12px;"></div>
        <div class="btn-row">
          <button class="btn-back" onclick="prevStep(3)">← Back</button>
          <button class="btn-submit" id="submitBtn" onclick="submitForm()">Create My Account</button>
        </div>
      </div>

      <p class="signin-link">Already have an account? <a href="./login.php">Sign in</a></p>

    </div>
  </main>

  <script src="./scripts/conection_string/api.js"></script>
  <script>
    let currentStep = 1;

    function nextStep(from) {
      if (!validateStep(from)) return;
      currentStep = from + 1;
      updateUI();
      if (currentStep === 3) buildSummary();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    function prevStep(from) {
      currentStep = from - 1;
      updateUI();
    }

    function updateUI() {
      document.querySelectorAll('.form-panel').forEach((p, i) => {
        p.classList.toggle('active', i + 1 === currentStep);
      });
      for (let i = 1; i <= 3; i++) {
        const d = document.getElementById('stepDot' + i);
        d.classList.remove('active', 'done');
        if (i === currentStep) d.classList.add('active');
        if (i < currentStep) d.classList.add('done');
      }
      for (let i = 1; i <= 2; i++) {
        document.getElementById('stepLine' + i).classList.toggle('done', i < currentStep);
      }
      document.getElementById('progressBar').style.width = (currentStep / 3 * 100) + '%';
      document.getElementById('flashError').style.display = 'none';
    }

    function showErr(id, msg) {
      const e = document.getElementById(id);
      e.textContent = msg;
      e.style.display = 'block';
    }

    function clearErr(id) {
      const e = document.getElementById(id);
      e.textContent = '';
      e.style.display = 'none';
    }

    function validateStep(step) {
      let ok = true;
      if (step === 1) {
        ['errUsername', 'errEmail', 'errPassword', 'errConfirm'].forEach(clearErr);
        const u = document.getElementById('username').value.trim();
        const e = document.getElementById('email').value.trim();
        const p = document.getElementById('password').value;
        const c = document.getElementById('confirmPassword').value;
        if (!u || u.length < 3) {
          showErr('errUsername', 'At least 3 characters required.');
          ok = false;
        }
        if (/\s/.test(u)) {
          showErr('errUsername', 'No spaces allowed.');
          ok = false;
        }
        if (!e || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e)) {
          showErr('errEmail', 'Enter a valid email address.');
          ok = false;
        }
        if (!p || p.length < 6) {
          showErr('errPassword', 'At least 6 characters required.');
          ok = false;
        }
        if (p !== c) {
          showErr('errConfirm', 'Passwords do not match.');
          ok = false;
        }
      }
      if (step === 2) {
        ['errFirstName', 'errLastName', 'errPhone'].forEach(clearErr);
        const fn = document.getElementById('firstName').value.trim();
        const ln = document.getElementById('lastName').value.trim();
        const ph = document.getElementById('phone').value.trim().replace(/\s/g, '');
        if (!fn) {
          showErr('errFirstName', 'First name is required.');
          ok = false;
        }
        if (!ln) {
          showErr('errLastName', 'Last name is required.');
          ok = false;
        }
        if (!ph) {
          showErr('errPhone', 'Phone number is required.');
          ok = false;
        } else if (!/^(09|\+639)\d{9}$/.test(ph)) {
          showErr('errPhone', 'Use format: 09XX-XXX-XXXX');
          ok = false;
        }
      }
      return ok;
    }

    function buildSummary() {
      const gender = document.querySelector('input[name="gender"]:checked');
      const data = [
        ['Username', document.getElementById('username').value],
        ['Email', document.getElementById('email').value],
        ['Full Name', document.getElementById('firstName').value + ' ' + document.getElementById('lastName').value],
        ['Phone', document.getElementById('phone').value],
        ['Birthday', document.getElementById('birthday').value || '—'],
        ['Gender', gender ? gender.value : '—'],
        ['Address', document.getElementById('address').value.trim() || '—'],
        ['Password', '••••••••'],
      ];
      document.getElementById('summaryBox').innerHTML = data.map(([l, v]) =>
        `<div class="summary-row"><span class="s-label">${l}</span><span class="s-value">${v}</span></div>`
      ).join('');
    }

    async function submitForm() {
      clearErr('errTerms');
      if (!document.getElementById('terms').checked) {
        showErr('errTerms', 'Please agree to the Terms of Service to continue.');
        return;
      }
      const btn = document.getElementById('submitBtn');
      btn.disabled = true;
      btn.textContent = '⏳ Creating your account...';

      const gender = document.querySelector('input[name="gender"]:checked');
      const payload = {
        username: document.getElementById('username').value.trim(),
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('password').value,
        first_name: document.getElementById('firstName').value.trim(),
        last_name: document.getElementById('lastName').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        birthday: document.getElementById('birthday').value || null,
        gender: gender ? gender.value : null,
        address: document.getElementById('address').value.trim() || null,
      };

      const res = await Auth.register(payload);

      if (res.success) {
        document.querySelectorAll('.form-panel, .step-indicator, .progress-bar-wrap, .signin-link')
          .forEach(el => el.style.display = 'none');
        const ok = document.getElementById('flashSuccess');
        ok.innerHTML = `🎉 <strong>Welcome to Casa De Manila, ${payload.first_name}!</strong><br>
        Your account has been created successfully.<br><br>
        <a href="./login.php">→ Sign in to your account</a>`;
        ok.style.display = 'block';
      } else {
        const err = document.getElementById('flashError');
        err.textContent = res.message;
        err.style.display = 'block';
        btn.disabled = false;
        btn.textContent = '🎉 Create My Account';
        if (res.message.toLowerCase().includes('taken') || res.message.toLowerCase().includes('already')) {
          currentStep = 1;
          updateUI();
        }
      }
    }

    function checkStrength() {
      const pw = document.getElementById('password').value;
      const bar = document.getElementById('strengthBar');
      const txt = document.getElementById('strengthText');
      if (!pw) {
        bar.style.width = '0';
        txt.textContent = '';
        return;
      }
      let score = 0;
      if (pw.length >= 6) score++;
      if (pw.length >= 10) score++;
      if (/[A-Z]/.test(pw)) score++;
      if (/[0-9]/.test(pw)) score++;
      if (/[^A-Za-z0-9]/.test(pw)) score++;
      const levels = [{
          pct: 20,
          color: '#e74c3c',
          label: 'Very Weak'
        },
        {
          pct: 40,
          color: '#e67e22',
          label: 'Weak'
        },
        {
          pct: 60,
          color: '#f1c40f',
          label: 'Fair'
        },
        {
          pct: 80,
          color: '#2ecc71',
          label: 'Strong'
        },
        {
          pct: 100,
          color: '#27ae60',
          label: 'Very Strong'
        },
      ];
      const lvl = levels[Math.min(score, levels.length) - 1];
      bar.style.width = lvl.pct + '%';
      bar.style.background = lvl.color;
      txt.textContent = lvl.label;
      txt.style.color = lvl.color;
    }

    function checkMatch() {
      const pw = document.getElementById('password').value;
      const c = document.getElementById('confirmPassword').value;
      const el = document.getElementById('matchHint');
      if (!c) {
        el.textContent = '';
        return;
      }
      el.textContent = pw === c ? '✓ Passwords match' : '✗ Passwords do not match';
      el.style.color = pw === c ? '#2ecc71' : '#e74c3c';
    }

    function togglePw(id, btn) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.textContent = input.type === 'password' ? '👁' : '🙈';
    }

    // Redirect if already logged in
    checkAuth().then(user => {
      if (user) window.location.href = './account.php';
    });
  </script>
</body>

</html>