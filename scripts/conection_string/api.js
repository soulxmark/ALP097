/* ============================================================
   Casa De Manila — API Helper
   File: scripts/conection_string/api.js
============================================================ */

const API = '/mainproj/ALP097/api';

async function apiFetch(endpoint, options = {}) {
  try {
    const res = await fetch(API + endpoint, {
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      ...options
    });

    const text = await res.text();

    // Guard: if response is HTML, the path is wrong
    if (text.trim().startsWith('<')) {
      console.error('API returned HTML instead of JSON. Check const API path.', API + endpoint);
      return { success: false, message: 'Server error. Check API path configuration.' };
    }

    return JSON.parse(text);
  } catch (err) {
    console.error('API error:', err);
    return { success: false, message: 'Network error. Is XAMPP running?' };
  }
}

const Auth = {
  me:       ()     => apiFetch('/auth/me.php'),
  login:    (data) => apiFetch('/auth/login.php',    { method: 'POST', body: JSON.stringify(data) }),
  register: (data) => apiFetch('/auth/register.php', { method: 'POST', body: JSON.stringify(data) }),
  logout:   ()     => apiFetch('/auth/logout.php',   { method: 'POST' })
};

const Menu = {
  getAll: (cat) => apiFetch('/menu/get_all.php' + (cat && cat !== 'all' ? `?category=${encodeURIComponent(cat)}` : ''))
};

const Orders = {
  place: (data) => apiFetch('/orders/place.php',   { method: 'POST', body: JSON.stringify(data) }),
  mine:  ()     => apiFetch('/orders/my_orders.php')
};

async function checkAuth() {
  const res = await Auth.me();
  return res.success ? res.user : null;
}

async function requireAuth(redirectTo = 'login.php') {
  const user = await checkAuth();
  if (!user) { window.location.href = redirectTo; return null; }
  return user;
}

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
  toast.style.borderColor = type === 'error' ? '#e74c3c' : type === 'success' ? '#2ecc71' : '#d4af37';
  setTimeout(() => { toast.style.transform = 'translateX(-50%) translateY(0)'; toast.style.opacity = '1'; }, 10);
  setTimeout(() => { toast.style.transform = 'translateX(-50%) translateY(70px)'; toast.style.opacity = '0'; }, 3200);
}