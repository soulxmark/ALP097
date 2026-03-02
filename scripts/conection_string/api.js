/* ============================================================
   Casa De Manila — API Helper
   File: scripts/api.js
   All fetch calls go through here
============================================================ */

const API = 'http://localhost:5000/api';

/* ── Generic fetch wrapper ─────────────────────────────────── */
async function apiFetch(endpoint, options = {}) {
  try {
    const res = await fetch(API + endpoint, {
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      ...options
    });
    return await res.json();
  } catch (err) {
    console.error('API error:', err);
    return { success: false, message: 'Network error. Is the server running?' };
  }
}

/* ── Auth ───────────────────────────────────────────────────── */
const Auth = {
  me:       ()       => apiFetch('/auth/me'),
  login:    (data)   => apiFetch('/auth/login',    { method: 'POST', body: JSON.stringify(data) }),
  register: (data)   => apiFetch('/auth/register', { method: 'POST', body: JSON.stringify(data) }),
  logout:   ()       => apiFetch('/auth/logout',   { method: 'POST' })
};

/* ── Menu ───────────────────────────────────────────────────── */
const Menu = {
  getAll:   (cat)    => apiFetch('/menu' + (cat && cat !== 'all' ? `?category=${cat}` : '')),
  seed:     ()       => apiFetch('/menu/seed', { method: 'POST' })
};

/* ── Orders ─────────────────────────────────────────────────── */
const Orders = {
  place:    (data)   => apiFetch('/orders',    { method: 'POST', body: JSON.stringify(data) }),
  mine:     ()       => apiFetch('/orders/my')
};

/* ── Account ────────────────────────────────────────────────── */
const Account = {
  stats:    ()       => apiFetch('/account/stats')
};

/* ── Session helpers ────────────────────────────────────────── */
async function checkAuth() {
  const res = await Auth.me();
  return res.logged_in ? res.user : null;
}

async function requireAuth(redirectTo = 'login.html') {
  const user = await checkAuth();
  if (!user) {
    window.location.href = redirectTo;
    return null;
  }
  return user;
}

/* ── Navbar: update login/user pill based on session ────────── */
async function initNavbar() {
  const user = await checkAuth();
  const li   = document.getElementById('nav-auth');
  if (!li) return;

  if (user) {
    li.innerHTML = `
      <a href="account.html" class="user-pill">
        <span class="user-avatar">${user.username[0].toUpperCase()}</span>
        ${user.username}
      </a>`;
  } else {
    li.innerHTML = `<a href="login.html">Login</a>`;
  }
}

/* ── Toast notification ─────────────────────────────────────── */
function showToast(msg, type = 'info') {
  let toast = document.getElementById('cdm-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'cdm-toast';
    toast.style.cssText = `
      position:fixed;bottom:28px;left:50%;
      transform:translateX(-50%) translateY(70px);
      background:#111;border:1px solid #d4af37;color:#fff;
      padding:11px 26px;border-radius:40px;
      font-family:'Cormorant Garamond',serif;font-size:1em;
      z-index:99999;opacity:0;
      transition:transform .35s ease,opacity .35s ease;
      white-space:nowrap;pointer-events:none;`;
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  if (type === 'error') toast.style.borderColor = '#e74c3c';
  else if (type === 'success') toast.style.borderColor = '#2ecc71';
  else toast.style.borderColor = '#d4af37';

  setTimeout(() => {
    toast.style.transform = 'translateX(-50%) translateY(0)';
    toast.style.opacity   = '1';
  }, 10);
  setTimeout(() => {
    toast.style.transform = 'translateX(-50%) translateY(70px)';
    toast.style.opacity   = '0';
  }, 3200);
}