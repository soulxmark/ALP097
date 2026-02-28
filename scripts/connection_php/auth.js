/* ============================================================
   Casa De Manila — Auth Pages JS
   File: scripts/auth.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ----------------------------------------------------------
     1. PASSWORD TOGGLE (show/hide password)
  ---------------------------------------------------------- */
  document.querySelectorAll('.toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = document.querySelector(this.dataset.target);
      if (!input) return;

      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      this.textContent = isHidden ? '🙈' : '👁';
    });
  });

  /* ----------------------------------------------------------
     2. PASSWORD STRENGTH METER (register page only)
  ---------------------------------------------------------- */
  const passwordInput = document.getElementById('password');
  const strengthBar   = document.getElementById('strength-bar');
  const strengthText  = document.getElementById('strength-text');

  if (passwordInput && strengthBar && strengthText) {
    passwordInput.addEventListener('input', function () {
      const val      = this.value;
      const strength = getPasswordStrength(val);

      strengthBar.style.width      = strength.percent + '%';
      strengthBar.style.background = strength.color;
      strengthText.textContent     = val.length === 0 ? '' : strength.label;
      strengthText.style.color     = strength.color;
    });
  }

  function getPasswordStrength(password) {
    let score = 0;
    if (password.length >= 6)  score++;
    if (password.length >= 10) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    const levels = [
      { percent: 20,  color: '#e74c3c', label: 'Very Weak'  },
      { percent: 40,  color: '#e67e22', label: 'Weak'       },
      { percent: 60,  color: '#f1c40f', label: 'Fair'       },
      { percent: 80,  color: '#2ecc71', label: 'Strong'     },
      { percent: 100, color: '#27ae60', label: 'Very Strong' },
    ];

    return levels[Math.min(score, levels.length) - 1] || levels[0];
  }

  /* ----------------------------------------------------------
     3. CONFIRM PASSWORD MATCH INDICATOR (register page only)
  ---------------------------------------------------------- */
  const confirmInput = document.getElementById('confirm_password');

  if (passwordInput && confirmInput) {
    function checkMatch() {
      const matchHint = document.getElementById('match-hint');
      if (!matchHint) return;

      if (confirmInput.value.length === 0) {
        matchHint.textContent = '';
        return;
      }

      if (passwordInput.value === confirmInput.value) {
        matchHint.textContent = '✓ Passwords match';
        matchHint.style.color = '#2ecc71';
      } else {
        matchHint.textContent = '✗ Passwords do not match';
        matchHint.style.color = '#e74c3c';
      }
    }

    confirmInput.addEventListener('input', checkMatch);
    passwordInput.addEventListener('input', checkMatch);
  }

  /* ----------------------------------------------------------
     4. AUTO-DISMISS FLASH MESSAGES
  ---------------------------------------------------------- */
  const messages = document.querySelectorAll('.auth-error, .auth-success');
  messages.forEach(function (msg) {
    setTimeout(function () {
      msg.style.transition = 'opacity 0.6s ease';
      msg.style.opacity    = '0';
      setTimeout(function () { msg.remove(); }, 600);
    }, 4000);
  });

  /* ----------------------------------------------------------
     5. FORM SUBMIT LOADING STATE
  ---------------------------------------------------------- */
  const form   = document.querySelector('form');
  const authBtn = document.querySelector('.auth-btn');

  if (form && authBtn) {
    form.addEventListener('submit', function () {
      authBtn.disabled     = true;
      authBtn.textContent  = 'Please wait...';
      authBtn.style.opacity = '0.7';
    });
  }

});