/* ============================================================
   Casa De Manila — API Helper
   File: scripts/conection_string/api.js
============================================================ */

const API = '/mainproj/ALP097/api.php?action=';
 
async function apiFetch(action, options = {}) {
  try {
    const res = await fetch(API + action, {
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      ...options
    });
    const text = await res.text();
    if (text.trim().startsWith('<')) {
      console.error('Got HTML instead of JSON for action:', action);
      return { success: false, message: 'Server error. Check api.php exists at project root.' };
    }
    return JSON.parse(text);
  } catch (err) {
    console.error('API error:', err);
    return { success: false, message: 'Network error. Is XAMPP running?' };
  }
}

const Auth = {
  me:       ()     => apiFetch('me'),
  login:    (data) => apiFetch('login',    { method: 'POST', body: JSON.stringify(data) }),
  register: (data) => apiFetch('register', { method: 'POST', body: JSON.stringify(data) }),
  logout:   ()     => apiFetch('logout',   { method: 'POST' })
};

const Menu = {
  getAll: (cat) => apiFetch('menu' + (cat && cat !== 'all' ? '&category=' + encodeURIComponent(cat) : ''))
};

const Orders = {
  place: (data) => apiFetch('place_order', { method: 'POST', body: JSON.stringify(data) }),
  mine:  ()     => apiFetch('my_orders')
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