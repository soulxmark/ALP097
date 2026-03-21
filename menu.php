<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Casa De Manila | Menu</title>
  <link rel="stylesheet" href="./styles/menu.css" />
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
  <style>
    .lm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.78); z-index:9999; align-items:center; justify-content:center; }
    .lm-overlay.active { display:flex; animation:lmFade .3s ease; }
    @keyframes lmFade { from{opacity:0} to{opacity:1} }
    .lm-box { background:#111; border:1px solid rgba(212,175,55,0.4); border-radius:20px; padding:44px 40px; width:100%; max-width:420px; box-shadow:0 30px 70px rgba(0,0,0,0.7); position:relative; font-family:'Cormorant Garamond',serif; animation:lmUp .4s ease; margin:20px; }
    @keyframes lmUp { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
    .lm-close { position:absolute; top:14px; right:18px; background:none; border:none; color:rgba(255,255,255,0.4); font-size:1.7em; cursor:pointer; transition:color .2s; line-height:1; padding:0; }
    .lm-close:hover { color:#d4af37; }
    .lm-logo { text-align:center; margin-bottom:6px; }
    .lm-logo h2 { font-family:'Great Vibes',cursive; font-size:2.5em; color:#d4af37; line-height:1; }
    .lm-logo p  { color:rgba(255,255,255,0.45); font-size:.78em; letter-spacing:2px; text-transform:uppercase; margin-top:4px; }
    .lm-notice  { background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); border-radius:10px; padding:10px 14px; color:#d4af37; font-size:.9em; text-align:center; margin:16px 0 20px; }
    .lm-error   { background:rgba(220,53,69,0.15); border:1px solid rgba(220,53,69,0.4); color:#ff8a8a; padding:9px 14px; border-radius:8px; font-size:.88em; margin-bottom:14px; text-align:center; display:none; }
    .lm-group   { margin-bottom:16px; }
    .lm-group label { display:block; color:rgba(255,255,255,0.55); font-size:.78em; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:7px; }
    .lm-group input { width:100%; padding:12px 15px; background:rgba(255,255,255,0.07); border:1px solid rgba(212,175,55,0.22); border-radius:10px; color:#fff; font-size:1em; font-family:'Cormorant Garamond',serif; outline:none; transition:border-color .3s,background .3s; box-sizing:border-box; }
    .lm-group input::placeholder { color:rgba(255,255,255,0.22); }
    .lm-group input:focus { border-color:#d4af37; background:rgba(255,255,255,0.11); }
    .lm-submit  { width:100%; padding:13px; background:#d4af37; color:#111; border:none; border-radius:10px; font-size:1.05em; font-weight:bold; font-family:'Cormorant Garamond',serif; letter-spacing:1px; cursor:pointer; transition:background .3s,transform .2s; margin-top:4px; }
    .lm-submit:hover { background:#e8c84a; transform:translateY(-2px); }
    .lm-submit:disabled { opacity:.6; cursor:not-allowed; transform:none; }
    .lm-divider { display:flex; align-items:center; gap:10px; margin:18px 0; }
    .lm-divider::before,.lm-divider::after { content:''; flex:1; height:1px; background:rgba(212,175,55,0.18); }
    .lm-divider span { color:rgba(255,255,255,0.28); font-size:.8em; }
    .lm-links { text-align:center; color:rgba(255,255,255,0.45); font-size:.95em; display:flex; flex-direction:column; gap:8px; }
    .lm-links a { color:#d4af37; text-decoration:none; font-weight:600; }
    .lm-links a:hover { color:#fff; }
    .user-pill { display:inline-flex; align-items:center; gap:7px; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.35); border-radius:30px; padding:4px 14px 4px 5px; text-decoration:none; color:#d4af37 !important; font-size:.9em; transition:background .3s; }
    .user-pill:hover { background:rgba(212,175,55,0.25); }
    .user-avatar { width:26px; height:26px; border-radius:50%; background:#d4af37; color:#111; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:.8em; }
    .menu-skeleton { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:20px; padding:20px; }
    .skeleton-card { border-radius:16px; overflow:hidden; height:320px; animation:shimmer 1.5s infinite; background:linear-gradient(90deg,#f0e8d0 25%,#fdf9f0 50%,#f0e8d0 75%); background-size:200% 100%; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
  </style>
</head>
<body>

<header>
  <nav class="navbar" id="navbar">
    <div class="logo">
      <a href="./index.php">Casa De Manila</a>
      <p>Authenticity You Can Taste</p>
    </div>
    <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
    <nav class="nav-links" id="navLinks">
      <ul>
        <li><a href="./index.php">Home</a></li>
        <li><a href="./reservation.php">Reservation</a></li>
        <li><a href="./menu.php" class="active">Menu</a></li>
        <li><a href="./events.php">Events</a></li>
        <li><a href="./contact_us.php">Contact</a></li>
        <li><a href="./about.php">About</a></li>
        <li id="nav-auth"><a href="./login.php">Login</a></li>
        <li>
          <a href="#" onclick="toggleCart(); return false;" class="cart-btn">
            🛒 Cart <span id="cartBadge" class="cart-badge">0</span>
          </a>
        </li>
      </ul>
    </nav>
  </nav>
</header>

<section class="menu-controls">
  <div class="filters">
    <button class="filter-btn active" onclick="filterMenu('all')">All</button>
    <button class="filter-btn" onclick="filterMenu('Mains')">Mains</button>
    <button class="filter-btn" onclick="filterMenu('Veggies')">Veggies</button>
    <button class="filter-btn" onclick="filterMenu('Desserts')">Desserts</button>
    <button class="filter-btn" onclick="filterMenu('Drinks')">Drinks</button>
  </div>
  <input type="text" id="searchInput" placeholder="Search dishes..." oninput="searchMenu()" />
</section>

<section class="menu-grid" id="menuGrid">
  <div class="menu-skeleton" id="skeletonLoader">
    <div class="skeleton-card"></div>
    <div class="skeleton-card"></div>
    <div class="skeleton-card"></div>
    <div class="skeleton-card"></div>
    <div class="skeleton-card"></div>
    <div class="skeleton-card"></div>
  </div>
</section>

<section id="modal-section">
  <div id="menuModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <img id="modalImg" alt="Menu Item">
      <h3 id="modalTitle"></h3>
      <p class="price" id="modalPrice"></p>
      <div class="details" id="modalDetails"></div>
      <div class="qty-control">
        <button onclick="changeQty(-1)">−</button>
        <span id="qty">1</span>
        <button onclick="changeQty(1)">+</button>
      </div>
      <button class="add-to-cart" onclick="addToCart()">🛒 Add to Cart</button>
    </div>
  </div>
</section>

<section id="cart-section">
  <div id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
      <h3>My Orders</h3>
      <button class="close-cart" onclick="toggleCart()">×</button>
    </div>
    <div id="cartItems" class="cart-items">
      <p class="empty-cart">Your cart is empty</p>
    </div>
    <div class="cart-footer">
      <div class="cart-total">
        <span>Total:</span>
        <span id="cartTotal">₱0.00</span>
      </div>
      <button class="clear-cart-btn" onclick="clearCart()">Clear All</button>
      <button class="checkout-btn" id="checkoutBtn" onclick="handleCheckout()">Place Order 🔒</button>
    </div>
  </div>
  <div id="cartOverlay" class="cart-overlay" onclick="toggleCart()"></div>
</section>

<div class="lm-overlay" id="lmOverlay">
  <div class="lm-box">
    <button class="lm-close" onclick="closeLM()">×</button>
    <div class="lm-logo">
      <h2>Casa De Manila</h2>
      <p>Member Portal</p>
    </div>
    <div class="lm-notice">🔒 Sign in to place your order</div>
    <div class="lm-error" id="lmError"></div>
    <div class="lm-group">
      <label>Username</label>
      <input type="text" id="lmUsername" placeholder="Enter your username" autocomplete="username">
    </div>
    <div class="lm-group">
      <label>Password</label>
      <input type="password" id="lmPassword" placeholder="Enter your password" autocomplete="current-password">
    </div>
    <button class="lm-submit" id="lmBtn" onclick="submitLogin()">Sign In & Place Order</button>
    <div class="lm-divider"><span>or</span></div>
    <div class="lm-links">
      <span>No account? <a href="./register.php">Create one →</a></span>
      <span><a href="./login.php">Go to full login page</a></span>
    </div>
  </div>
</div>

<footer class="footer">
  <div class="footer-container">
    <p>&copy; 2026 Casa De Manila. All rights reserved.</p>
    <p>Email: reservations@casamanila.ph | Phone: +63 912 345 6789</p>
    <div class="social-links">
      <a href="#">Facebook</a><a href="#">Instagram</a><a href="#">Twitter</a>
    </div>
  </div>
</footer>

<!-- CORRECT script path -->
<script src="./scripts/function.js"></script>
<script src="./scripts/conection_string/api.js"></script>
<script>
let currentUser  = null;
let allMenuItems = [];
let currentItem  = null;

document.addEventListener('DOMContentLoaded', async () => {
  currentUser = await checkAuth();
  updateNavbar();
  updateCheckoutBtn();
  await loadMenu();

  ['lmUsername','lmPassword'].forEach(id => {
    document.getElementById(id).addEventListener('keydown', e => {
      if (e.key === 'Enter') submitLogin();
    });
  });
});

function updateNavbar() {
  const li = document.getElementById('nav-auth');
  if (!li) return;
  if (currentUser) {
    li.innerHTML = `
      <a href="./account.php" class="user-pill">
        <span class="user-avatar">${currentUser.username[0].toUpperCase()}</span>
        ${currentUser.username}
      </a>`;
  } else {
    li.innerHTML = `<a href="./login.php">Login</a>`;
  }
}

async function loadMenu() {
  try {
    const res = await Menu.getAll();
    const skeleton = document.getElementById('skeletonLoader');
    if (skeleton) skeleton.remove();

    if (!res.success || !res.items || !res.items.length) {
      document.getElementById('menuGrid').innerHTML =
        `<div style="text-align:center;padding:60px;color:#aaa;font-size:1.2em;">
           No menu items found.<br>
           <small>Make sure the database is seeded.</small>
         </div>`;
      return;
    }
    allMenuItems = res.items;
    renderMenu(allMenuItems);
  } catch(e) {
    console.error('loadMenu error:', e);
  }
}

function renderMenu(items) {
  const grid = document.getElementById('menuGrid');
  if (!items.length) {
    grid.innerHTML = `<div style="text-align:center;padding:60px;color:#aaa;">No items found.</div>`;
    return;
  }
  grid.innerHTML = items.map(item => `
    <div class="menu-item ${item.category.toLowerCase()}"
         onclick="openItemModal('${item._id}')">
      <img src="${item.image}" alt="${item.name}"
           onerror="this.src='./images/placeholder.jpg'">
      <h3>${item.name}</h3>
      <p class="price">₱${parseFloat(item.price).toLocaleString()}</p>
      <div class="details">${item.description}</div>
    </div>`).join('');
}

function openItemModal(id) {
  const item = allMenuItems.find(i => i._id == id);
  if (!item) return;
  currentItem = item;
  document.getElementById('modalImg').src                  = item.image;
  document.getElementById('modalImg').onerror              = () => { document.getElementById('modalImg').src = './images/placeholder.jpg'; };
  document.getElementById('modalTitle').textContent        = item.name;
  document.getElementById('modalPrice').textContent        = `₱${parseFloat(item.price).toLocaleString()}`;
  document.getElementById('modalDetails').textContent      = item.description;
  document.getElementById('qty').textContent               = 1;
  document.getElementById('menuModal').style.display       = 'flex';
}

function filterMenu(cat) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  event.target.classList.add('active');
  const filtered = cat === 'all'
    ? allMenuItems
    : allMenuItems.filter(i => i.category.toLowerCase() === cat.toLowerCase());
  renderMenu(filtered);
}

function searchMenu() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  const filtered = allMenuItems.filter(i =>
    i.name.toLowerCase().includes(q) || i.description.toLowerCase().includes(q)
  );
  renderMenu(filtered);
}

function addToCart() {
  if (!currentItem) return;
  const qty = parseInt(document.getElementById('qty').textContent);
  const existing = document.querySelector(`#cartItems [data-menu-id="${currentItem._id}"]`);
  if (existing) {
    const qtyEl = existing.querySelector('.cart-item-qty');
    qtyEl.textContent = parseInt(qtyEl.textContent) + qty;
  } else {
    const emptyMsg = document.querySelector('#cartItems .empty-cart');
    if (emptyMsg) emptyMsg.remove();
    const div = document.createElement('div');
    div.className      = 'cart-item';
    div.dataset.menuId = currentItem._id;
    div.innerHTML = `
      <div class="cart-item-info">
        <span class="cart-item-name">${currentItem.name}</span>
        <span class="cart-item-price" data-price="${currentItem.price}">₱${currentItem.price}</span>
      </div>
      <div class="cart-item-controls">
        <button onclick="changeCartQty(this,-1)">−</button>
        <span class="cart-item-qty">${qty}</span>
        <button onclick="changeCartQty(this,1)">+</button>
        <button class="remove-item" onclick="removeCartItem(this)">✕</button>
      </div>`;
    document.getElementById('cartItems').appendChild(div);
  }
  updateCartUI();
  closeModal();
  showToast(`✅ ${currentItem.name} added to cart`, 'success');
}

function changeCartQty(btn, delta) {
  const row   = btn.closest('.cart-item');
  const qtyEl = row.querySelector('.cart-item-qty');
  qtyEl.textContent = Math.max(1, parseInt(qtyEl.textContent) + delta);
  updateCartUI();
}

function removeCartItem(btn) {
  btn.closest('.cart-item').remove();
  if (!document.querySelectorAll('#cartItems .cart-item').length) {
    document.getElementById('cartItems').innerHTML = '<p class="empty-cart">Your cart is empty</p>';
  }
  updateCartUI();
}

function updateCartUI() {
  const rows = document.querySelectorAll('#cartItems .cart-item');
  let total = 0, count = 0;
  rows.forEach(row => {
    const price = parseFloat(row.querySelector('.cart-item-price').dataset.price);
    const qty   = parseInt(row.querySelector('.cart-item-qty').textContent);
    total += price * qty;
    count += qty;
  });
  document.getElementById('cartTotal').textContent = `₱${total.toLocaleString()}`;
  document.getElementById('cartBadge').textContent = count;
}

function clearCart() {
  document.getElementById('cartItems').innerHTML = '<p class="empty-cart">Your cart is empty</p>';
  updateCartUI();
}

function updateCheckoutBtn() {
  const btn = document.getElementById('checkoutBtn');
  if (currentUser) {
    btn.textContent = 'Place Order';
    btn.onclick     = handleCheckout;
  } else {
    btn.textContent = 'Place Order 🔒';
    btn.onclick     = requireLogin;
  }
}

async function handleCheckout() {
  if (!currentUser) { requireLogin(); return; }
  const rows = document.querySelectorAll('#cartItems .cart-item');
  if (!rows.length) { showToast('🛒 Your cart is empty!'); return; }
  const items = [];
  rows.forEach(row => {
    items.push({
      name:     row.querySelector('.cart-item-name').textContent.trim(),
      price:    parseFloat(row.querySelector('.cart-item-price').dataset.price),
      quantity: parseInt(row.querySelector('.cart-item-qty').textContent),
      menu_id:  row.dataset.menuId
    });
  });
  const total = items.reduce((s, i) => s + i.price * i.quantity, 0);
  const btn   = document.getElementById('checkoutBtn');
  btn.disabled    = true;
  btn.textContent = 'Placing order...';
  const res = await Orders.place({ items, total });
  if (res.success) {
    clearCart();
    toggleCart();
    showToast('✅ Order placed! Redirecting...', 'success');
    setTimeout(() => { window.location.href = './account.php'; }, 2000);
  } else {
    showToast('❌ ' + (res.message || 'Failed. Try again.'), 'error');
    btn.disabled    = false;
    btn.textContent = 'Place Order';
  }
}

function requireLogin() {
  if (!document.querySelectorAll('#cartItems .cart-item').length) {
    showToast('🛒 Your cart is empty!'); return;
  }
  const sidebar = document.getElementById('cartSidebar');
  if (sidebar) sidebar.classList.remove('open');
  document.getElementById('cartOverlay')?.classList.remove('active');
  document.body.style.overflow = '';
  setTimeout(openLM, 250);
}

function openLM() {
  document.getElementById('lmOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
  document.getElementById('lmUsername').focus();
}

function closeLM() {
  document.getElementById('lmOverlay').classList.remove('active');
  document.body.style.overflow = '';
}

document.getElementById('lmOverlay').addEventListener('click', e => {
  if (e.target === document.getElementById('lmOverlay')) closeLM();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLM(); });

async function submitLogin() {
  const btn      = document.getElementById('lmBtn');
  const errDiv   = document.getElementById('lmError');
  const username = document.getElementById('lmUsername').value.trim();
  const password = document.getElementById('lmPassword').value.trim();
  if (!username || !password) {
    errDiv.textContent = 'Please fill in all fields.';
    errDiv.style.display = 'block';
    return;
  }
  btn.disabled    = true;
  btn.textContent = 'Signing in...';
  errDiv.style.display = 'none';
  const res = await Auth.login({ username, password });
  if (res.success) {
    currentUser = res.user;
    closeLM();
    updateNavbar();
    updateCheckoutBtn();
    showToast(`✅ Welcome, ${res.user.username}! Placing order...`, 'success');
    setTimeout(handleCheckout, 800);
  } else {
    errDiv.textContent   = res.message || 'Login failed.';
    errDiv.style.display = 'block';
    btn.disabled    = false;
    btn.textContent = 'Sign In & Place Order';
  }
}
</script>
</body>
</html>