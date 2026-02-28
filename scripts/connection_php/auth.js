/* ============================================================
   Casa De Manila — Auth JS
   File: scripts/auth.js
============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* -- Password show/hide toggle ----------------------------- */
  document.querySelectorAll('.toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = document.querySelector(this.dataset.target);
      if (!input) return;
      const hidden = input.type === 'password';
      input.type   = hidden ? 'text' : 'password';
      this.textContent = hidden ? '🙈' : '👁';
    });
  });

  /* -- Password strength meter (register page) --------------- */
  const pwInput  = document.getElementById('password');
  const bar      = document.getElementById('strength-bar');
  const barText  = document.getElementById('strength-text');

  if (pwInput && bar && barText) {
    pwInput.addEventListener('input', function () {
      if (!this.value.length) { bar.style.width = '0'; barText.textContent = ''; return; }
      const s = strength(this.value);
      bar.style.width      = s.pct + '%';
      bar.style.background = s.color;
      barText.textContent  = s.label;
      barText.style.color  = s.color;
    });
  }

  function strength(pw) {
    let score = 0;
    if (pw.length >= 6)           score++;
    if (pw.length >= 10)          score++;
    if (/[A-Z]/.test(pw))         score++;
    if (/[0-9]/.test(pw))         score++;
    if (/[^A-Za-z0-9]/.test(pw))  score++;
    const levels = [
      { pct:20,  color:'#e74c3c', label:'Very Weak'  },
      { pct:40,  color:'#e67e22', label:'Weak'       },
      { pct:60,  color:'#f1c40f', label:'Fair'       },
      { pct:80,  color:'#2ecc71', label:'Strong'     },
      { pct:100, color:'#27ae60', label:'Very Strong' },
    ];
    return levels[Math.min(score, levels.length) - 1] || levels[0];
  }

  /* -- Confirm password match indicator ---------------------- */
  const confirm = document.getElementById('confirm_password');
  const hint    = document.getElementById('match-hint');

  if (pwInput && confirm && hint) {
    function checkMatch() {
      if (!confirm.value.length) { hint.textContent = ''; return; }
      if (pwInput.value === confirm.value) {
        hint.textContent = '✓ Passwords match';
        hint.style.color = '#2ecc71';
      } else {
        hint.textContent = '✗ Passwords do not match';
        hint.style.color = '#e74c3c';
      }
    }
    confirm.addEventListener('input', checkMatch);
    pwInput.addEventListener('input', checkMatch);
  }

  /* -- Auto-dismiss flash messages after 4s ----------------- */
  document.querySelectorAll('.auth-error, .auth-success').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.6s';
      el.style.opacity    = '0';
      setTimeout(function () { el.remove(); }, 600);
    }, 4000);
  });

  /* -- Loading state on submit ------------------------------ */
  const form = document.querySelector('form');
  const btn  = document.querySelector('.auth-btn');
  if (form && btn) {
    form.addEventListener('submit', function () {
      btn.disabled     = true;
      btn.textContent  = 'Please wait...';
      btn.style.opacity = '0.7';
    });
  }

});