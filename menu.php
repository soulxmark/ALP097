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
    /* ... Existing Styles ... */
    .lm-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.78);
      z-index: 9999;
      align-items: center;
      justify-content: center;
    }

    .lm-overlay.active {
      display: flex;
      animation: lmFade .3s ease;
    }

    @keyframes lmFade {
      from {
        opacity: 0
      }

      to {
        opacity: 1
      }
    }

    .lm-box {
      background: #111;
      border: 1px solid rgba(212, 175, 55, 0.4);
      border-radius: 20px;
      padding: 44px 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 30px 70px rgba(0, 0, 0, 0.7);
      position: relative;
      font-family: 'Cormorant Garamond', serif;
      animation: lmUp .4s ease;
      margin: 20px;
    }

    @keyframes lmUp {
      from {
        opacity: 0;
        transform: translateY(28px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    .lm-close {
      position: absolute;
      top: 14px;
      right: 18px;
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.4);
      font-size: 1.7em;
      cursor: pointer;
      transition: color .2s;
      line-height: 1;
      padding: 0;
    }

    .lm-close:hover {
      color: #d4af37;
    }

    .lm-logo {
      text-align: center;
      margin-bottom: 6px;
    }

    .lm-logo h2 {
      font-family: 'Great Vibes', cursive;
      font-size: 2.5em;
      color: #d4af37;
      line-height: 1;
    }

    .lm-logo p {
      color: rgba(255, 255, 255, 0.45);
      font-size: .78em;
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-top: 4px;
    }

    .lm-notice {
      background: rgba(212, 175, 55, 0.1);
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 10px;
      padding: 10px 14px;
      color: #d4af37;
      font-size: .9em;
      text-align: center;
      margin: 16px 0 20px;
    }

    .lm-error {
      background: rgba(220, 53, 69, 0.15);
      border: 1px solid rgba(220, 53, 69, 0.4);
      color: #ff8a8a;
      padding: 9px 14px;
      border-radius: 8px;
      font-size: .88em;
      margin-bottom: 14px;
      text-align: center;
      display: none;
    }

    .lm-group {
      margin-bottom: 16px;
    }

    .lm-group label {
      display: block;
      color: rgba(255, 255, 255, 0.55);
      font-size: .78em;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 7px;
    }

    .lm-group input {
      width: 100%;
      padding: 12px 15px;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(212, 175, 55, 0.22);
      border-radius: 10px;
      color: #fff;
      font-size: 1em;
      font-family: 'Cormorant Garamond', serif;
      outline: none;
      transition: border-color .3s, background .3s;
      box-sizing: border-box;
    }

    .lm-group input::placeholder {
      color: rgba(255, 255, 255, 0.22);
    }

    .lm-group input:focus {
      border-color: #d4af37;
      background: rgba(255, 255, 255, 0.11);
    }

    .lm-submit {
      width: 100%;
      padding: 13px;
      background: #d4af37;
      color: #111;
      border: none;
      border-radius: 10px;
      font-size: 1.05em;
      font-weight: bold;
      font-family: 'Cormorant Garamond', serif;
      letter-spacing: 1px;
      cursor: pointer;
      transition: background .3s, transform .2s;
      margin-top: 4px;
    }

    .lm-submit:hover {
      background: #e8c84a;
      transform: translateY(-2px);
    }

    .lm-submit:disabled {
      opacity: .6;
      cursor: not-allowed;
      transform: none;
    }

    .lm-divider {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 18px 0;
    }

    .lm-divider::before,
    .lm-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(212, 175, 55, 0.18);
    }

    .lm-divider span {
      color: rgba(255, 255, 255, 0.28);
      font-size: .8em;
    }

    .lm-links {
      text-align: center;
      color: rgba(255, 255, 255, 0.45);
      font-size: .95em;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .lm-links a {
      color: #d4af37;
      text-decoration: none;
      font-weight: 600;
    }

    .lm-links a:hover {
      color: #fff;
    }

    .user-pill {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: rgba(212, 175, 55, 0.12);
      border: 1px solid rgba(212, 175, 55, 0.35);
      border-radius: 30px;
      padding: 4px 14px 4px 5px;
      text-decoration: none;
      color: #d4af37 !important;
      font-size: .9em;
      transition: background .3s;
    }

    .user-pill:hover {
      background: rgba(212, 175, 55, 0.25);
    }

    .user-avatar {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: #d4af37;
      color: #111;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: .8em;
    }

    .menu-skeleton {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
      padding: 20px;
    }

    .skeleton-card {
      border-radius: 16px;
      overflow: hidden;
      height: 320px;
      animation: shimmer 1.5s infinite;
      background: linear-gradient(90deg, #f0e8d0 25%, #fdf9f0 50%, #f0e8d0 75%);
      background-size: 200% 100%;
    }

    @keyframes shimmer {
      0% {
        background-position: 200% 0
      }

      100% {
        background-position: -200% 0
      }
    }

    .cart-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #d4af37;
      color: #111;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      font-size: 11px;
      font-weight: 700;
      position: absolute;
      top: -8px;
      right: -8px;
      opacity: 0;
      transform: scale(0);
      transition: transform .25s ease, opacity .25s ease;
    }

    .cart-badge.visible {
      opacity: 1;
      transform: scale(1);
    }

    .cart-btn {
      position: relative;
    }

    .cart-item {
      display: flex;
      gap: 12px;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid #f0e8d0;
    }

    .cart-item-img {
      width: 60px;
      height: 60px;
      border-radius: 10px;
      object-fit: cover;
      flex-shrink: 0;
      border: 1px solid #f0e8d0;
    }

    .cart-item-body {
      flex: 1;
      min-width: 0;
    }

    .cart-item-name {
      display: block;
      font-weight: 700;
      color: #111;
      font-size: .97em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .cart-item-price {
      display: block;
      color: #d4af37;
      font-weight: 700;
      font-size: .9em;
      margin-top: 2px;
    }

    .cart-item-controls {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 6px;
    }

    .cart-item-controls button {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: none;
      background: #d4af37;
      color: #111;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background .2s;
      padding: 0;
      line-height: 1;
    }

    .cart-item-qty {
      font-weight: 700;
      min-width: 20px;
      text-align: center;
      font-size: .95em;
    }

    .remove-item {
      background: none !important;
      color: #ccc !important;
      font-size: 16px !important;
      width: auto !important;
      height: auto !important;
      border-radius: 0 !important;
      margin-left: 4px;
    }

    .cart-popup {
      position: fixed;
      bottom: 28px;
      right: 28px;
      background: #111;
      border: 1px solid rgba(212, 175, 55, 0.4);
      border-radius: 14px;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
      z-index: 99999;
      max-width: 300px;
      transform: translateX(120%);
      opacity: 0;
      transition: transform .35s ease, opacity .35s ease;
      font-family: 'Cormorant Garamond', serif;
      color: #fff;
    }

    .cart-popup.show {
      transform: translateX(0);
      opacity: 1;
    }

    .cart-popup-img {
      width: 52px;
      height: 52px;
      border-radius: 8px;
      object-fit: cover;
      flex-shrink: 0;
      border: 1px solid rgba(212, 175, 55, 0.3);
    }

    .cart-popup-name {
      display: block;
      color: #d4af37;
      font-weight: 700;
      font-size: 1em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-top: 2px;
    }

    #toastContainer {
      position: fixed;
      top: 90px;
      right: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      z-index: 999999;
    }

    .toast {
      background: #111;
      color: #2ecc71;
      padding: 12px 16px;
      border-radius: 10px;
      border: 1px solid #2ecc71;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
      transform: translateX(120%);
      opacity: 0;
      transition: all .4s ease;
      font-family: 'Cormorant Garamond', serif;
    }

    .toast.show {
      transform: translateX(0);
      opacity: 1;
    }

    .toast.error {
      color: #e74c3c;
      border-color: #e74c3c;
    }
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
      <button class="filter-btn active" onclick="filterMenu('all', this)">All</button>
      <button class="filter-btn" onclick="filterMenu('Mains', this)">Mains</button>
      <button class="filter-btn" onclick="filterMenu('Veggies', this)">Veggies</button>
      <button class="filter-btn" onclick="filterMenu('Desserts', this)">Desserts</button>
      <button class="filter-btn" onclick="filterMenu('Drinks', this)">Drinks</button>
    </div>
    <input type="text" id="searchInput" placeholder="Search dishes..." oninput="searchMenu()" />
  </section>

  <section class="menu-grid" id="menuGrid">
    <div class="menu-skeleton" id="skeletonLoader">
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
        <div class="cart-total"><span>Total:</span><span id="cartTotal">₱0.00</span></div>
        <button class="clear-cart-btn" onclick="clearCart()">Clear All</button>
        <button class="checkout-btn" id="checkoutBtn" onclick="handleCheckout()">Place Order 🔒</button>
      </div>
    </div>
    <div id="cartOverlay" class="cart-overlay" onclick="toggleCart()"></div>
  </section>

  <div class="cart-popup" id="cartPopup">
    <img class="cart-popup-img" id="cartPopupImg" src="" alt="">
    <div class="cart-popup-info">
      <span class="cart-popup-title">Added to cart</span>
      <span class="cart-popup-name" id="cartPopupName"></span>
      <span class="cart-popup-price" id="cartPopupPrice"></span>
    </div>
    <span class="cart-popup-check">✓</span>
  </div>

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
        <input type="text" id="lmUsername" placeholder="Enter your username">
      </div>
      <div class="lm-group">
        <label>Password</label>
        <input type="password" id="lmPassword" placeholder="Enter your password">
      </div>
      <button class="lm-submit" id="lmBtn" onclick="submitLogin()">Sign In & Place Order</button>
      <div class="lm-divider"><span>or</span></div>
      <div class="lm-links">
        <span>No account? <a href="./register.php">Create one →</a></span>
      </div>
    </div>
  </div>

  <audio id="toastSound">
    <source src="./sounds/notify.mp3" type="audio/mpeg">
  </audio>

 <footer class="footer reveal">
    <div class="footer-container">
      <p>&copy; 2026 Casa De Manila. All rights reserved.</p>
      <p>Email: reservations@casamanila.ph | Phone: +63 912 345 6789</p>
      <div class="social-links">
        <a href="https://facebook.com">Facebook</a>
        <a href="htpps://instagram.com">Instagram</a>
        <a href="htpp://twitter.com">X</a>
      </div>
    </div>
  </footer>

  <script src="./scripts/conection_string/api.js"></script>
  <script src="./scripts/function.js"></script>
  <script>
    /* ───────── SINGLE TOAST SYSTEM ───────── */
    window.showToastStack = function(message, type = 'success') {
      let container = document.getElementById('toastContainer');
      if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        document.body.appendChild(container);
      }

      // Clear previous toasts so "only 1" is shown
      container.innerHTML = '';

      const toast = document.createElement('div');
      toast.className = 'toast ' + (type === 'error' ? 'error' : '');
      toast.textContent = message;
      container.appendChild(toast);

      setTimeout(() => toast.classList.add('show'), 10);

      const sound = document.getElementById('toastSound');
      if (sound) {
        sound.currentTime = 0;
        sound.play().catch(() => {});
      }

      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
      }, 2500);
    };

    /* ── State & Initialization ── */
    let currentUser = null,
      allMenuItems = [],
      currentItem = null,
      popupTimer = null;

    document.addEventListener('DOMContentLoaded', async () => {
      currentUser = await checkAuth();
      updateNavbar();
      updateCheckoutBtn();
      await loadMenu();
    });

    function updateNavbar() {
      const li = document.getElementById('nav-auth');
      if (!li) return;
      li.innerHTML = currentUser ?
        `<a href="./account.php" class="user-pill"><span class="user-avatar">${currentUser.username[0].toUpperCase()}</span> ${currentUser.username}</a>` :
        `<a href="./login.php">Login</a>`;
    }

    async function loadMenu() {
      const res = await Menu.getAll();
      const skeleton = document.getElementById('skeletonLoader');
      if (skeleton) skeleton.remove();
      if (res.success) {
        allMenuItems = res.items;
        renderMenu(allMenuItems);
      }
    }

    function renderMenu(items) {
      document.getElementById('menuGrid').innerHTML = items.map(item => `
    <div class="menu-item" onclick="openItemModal('${item._id}')">
      <img src="${item.image}" onerror="this.src='./images/placeholder.jpg'">
      <h3>${item.name}</h3>
      <p class="price">₱${parseFloat(item.price).toLocaleString()}</p>
      <div class="details">${item.description}</div>
    </div>`).join('');
    }

    function openItemModal(id) {
      const item = allMenuItems.find(i => i._id == id);
      if (!item) return;
      currentItem = item;
      document.getElementById('modalImg').src = item.image;
      document.getElementById('modalTitle').textContent = item.name;
      document.getElementById('modalPrice').textContent = `₱${parseFloat(item.price).toLocaleString()}`;
      document.getElementById('modalDetails').textContent = item.description;
      document.getElementById('qty').textContent = 1;
      document.getElementById('menuModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('menuModal').style.display = 'none';
      currentItem = null;
    }

    function changeQty(val) {
      const qtyEl = document.getElementById('qty');
      qtyEl.textContent = Math.max(1, parseInt(qtyEl.textContent) + val);
    }

    function filterMenu(cat, btn) {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderMenu(cat === 'all' ? allMenuItems : allMenuItems.filter(i => i.category.toLowerCase() === cat.toLowerCase()));
    }

    function searchMenu() {
      const q = document.getElementById('searchInput').value.toLowerCase();
      renderMenu(allMenuItems.filter(i => i.name.toLowerCase().includes(q)));
    }

    function showCartPopup(item) {
      const popup = document.getElementById('cartPopup');
      document.getElementById('cartPopupImg').src = item.image;
      document.getElementById('cartPopupName').textContent = item.name;
      document.getElementById('cartPopupPrice').textContent = '₱' + parseFloat(item.price).toLocaleString();
      popup.classList.add('show');
      if (popupTimer) clearTimeout(popupTimer);
      popupTimer = setTimeout(() => popup.classList.remove('show'), 2800);
    }

    function updateCartBadge(count) {
      const badge = document.getElementById('cartBadge');
      badge.textContent = count;
      count > 0 ? badge.classList.add('visible') : badge.classList.remove('visible');
    }

    function toggleCart() {
      document.getElementById('cartSidebar').classList.toggle('open');
      document.getElementById('cartOverlay').classList.toggle('active');
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
        div.className = 'cart-item';
        div.dataset.menuId = currentItem._id;
        div.innerHTML = `
      <img class="cart-item-img" src="${currentItem.image}" onerror="this.src='./images/placeholder.jpg'">
      <div class="cart-item-body">
        <span class="cart-item-name">${currentItem.name}</span>
        <span class="cart-item-price" data-price="${currentItem.price}">₱${parseFloat(currentItem.price).toLocaleString()}</span>
        <div class="cart-item-controls">
          <button onclick="changeCartQty(this,-1)">−</button>
          <span class="cart-item-qty">${qty}</span>
          <button onclick="changeCartQty(this,1)">+</button>
          <button class="remove-item" onclick="removeCartItem(this)">✕</button>
        </div>
      </div>`;
        document.getElementById('cartItems').appendChild(div);
      }

      updateCartUI();
      showCartPopup(currentItem);
      showToastStack("✔ " + currentItem.name + " added", "success");
      closeModal();
    }

   function updateCartUI() {
  const rows = document.querySelectorAll('#cartItems .cart-item');
  let total = 0, count = 0;

  rows.forEach(row => {
    const price = parseFloat(row.querySelector('.cart-item-price').dataset.price);
    const qty = parseInt(row.querySelector('.cart-item-qty').textContent);
    total += price * qty;
    count += qty;
  });

  // Use the formatter for a professional look
  document.getElementById('cartTotal').textContent = `₱${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
  updateCartBadge(count);
}
  function updateCartUI() {
    const rows = document.querySelectorAll('#cartItems .cart-item');
    let total = 0, count = 0;

    rows.forEach(row => {
      const price = parseFloat(row.querySelector('.cart-item-price').dataset.price);
      const qty = parseInt(row.querySelector('.cart-item-qty').textContent);
      total += price * qty;
      count += qty;
    });

    // Car Added formatting for better UX
    document.getElementById('cartTotal').textContent = `₱${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
    updateCartBadge(count); 
}
    /*updateCartUI() {
      const rows = document.querySelectorAll('#cartItems .cart-item');
      let total = 0,
        count = 0;
      rows.forEach(row => {
        total += parseFloat(row.querySelector('.cart-item-price').dataset.price) * parseInt(row.querySelector('.cart-item-qty').textContent);
        count += parseInt(row.querySelector('.cart-item-qty').textContent);
      });
      document.getElementById('cartTotal').textContent = `₱${total.toLocaleString()}`;
      updateCartBadge(count);
    }
      */  
    function changeCartQty(btn, delta) {
      const qtyEl = btn.closest('.cart-item').querySelector('.cart-item-qty');
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

    function clearCart() {
      document.getElementById('cartItems').innerHTML = '<p class="empty-cart">Your cart is empty</p>';
      updateCartUI();
    }

    function updateCheckoutBtn() {
      const btn = document.getElementById('checkoutBtn');
      btn.textContent = currentUser ? 'Place Order' : 'Place Order 🔒';
      btn.onclick = currentUser ? handleCheckout : requireLogin;
    }

    async function handleCheckout() {
      const rows = document.querySelectorAll('#cartItems .cart-item');
      if (!rows.length) {
        showToastStack('🛒 Your cart is empty!', 'error');
        return;
      }
      const items = Array.from(rows).map(row => ({
        name: row.querySelector('.cart-item-name').textContent,
        price: parseFloat(row.querySelector('.cart-item-price').dataset.price),
        quantity: parseInt(row.querySelector('.cart-item-qty').textContent),
        menu_id: row.dataset.menuId
      }));
      const res = await Orders.place({
        items,
        total: items.reduce((s, i) => s + i.price * i.quantity, 0)
      });
      if (res.success) {
        clearCart();
        toggleCart();
        showToastStack('✅ Order placed!', 'success');
        setTimeout(() => {
          window.location.href = './account.php';
        }, 2000);
      }
    }

    function requireLogin() {
      if (!document.querySelectorAll('#cartItems .cart-item').length) {
        showToastStack('🛒 Your cart is empty!', 'error');
        return;
      }
      toggleCart();
      setTimeout(openLM, 250);
    }

    function openLM() {
      document.getElementById('lmOverlay').classList.add('active');
    }

    function closeLM() {
      document.getElementById('lmOverlay').classList.remove('active');
    }

    async function submitLogin() {
      const res = await Auth.login({
        username: document.getElementById('lmUsername').value,
        password: document.getElementById('lmPassword').value
      });
      if (res.success) {
        currentUser = res.user;
        closeLM();
        updateNavbar();
        updateCheckoutBtn();
        showToastStack(`✅ Welcome, ${res.user.username}!`, 'success');
        setTimeout(handleCheckout, 800);
      } else {
        document.getElementById('lmError').textContent = res.message;
        document.getElementById('lmError').style.display = 'block';
      }
    }
  </script>
</body>

</html>