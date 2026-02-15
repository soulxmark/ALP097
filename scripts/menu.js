const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');
const navbar = document.querySelector('.navbar');

// Hamburger toggle
if (hamburger) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    hamburger.classList.toggle('active');
  });
}

// Navbar scroll effect
window.addEventListener('scroll', () => {
  if (window.scrollY > 50) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

let quantity = 1;

function openModal(item) {
  quantity = 1;
  document.getElementById('qty').textContent = quantity;

  document.getElementById('modalImg').src =
    item.querySelector('img').src;
  document.getElementById('modalTitle').textContent =
    item.querySelector('h3').textContent;
  document.getElementById('modalPrice').textContent =
    item.querySelector('.price').textContent;
  document.getElementById('modalDetails').textContent =
    item.querySelector('.details').textContent;

  document.getElementById('menuModal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('menuModal').style.display = 'none';
}

window.onclick = (e) => {
  const modal = document.getElementById('menuModal');
  if (e.target === modal) closeModal();
};

function changeQty(val) {
  quantity += val;
  if (quantity < 1) quantity = 1;
  document.getElementById('qty').textContent = quantity;
}

function filterMenu(category) {
  const buttons = document.querySelectorAll('.filter-btn');
  buttons.forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  
  // Change background based on category
  changeBackground(category);
  
  document.querySelectorAll('.menu-item').forEach(item => {
    item.style.display =
      category === 'all' || item.classList.contains(category)
        ? 'block'
        : 'none';
  });
}

function changeBackground(category) {
  const body = document.body;
  
  // Remove all existing category classes
  body.classList.remove('bg-all', 'bg-mains', 'bg-veggies', 'bg-desserts', 'bg-drinks');
  
  // Add the new category class
  body.classList.add(`bg-${category}`);
}

function searchMenu() {
  const value = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.menu-item').forEach(item => {
    item.style.display =
      item.querySelector('h3').textContent.toLowerCase().includes(value)
        ? 'block'
        : 'none';
  });
}

// Cart functionality
let cart = [];

function addToCart() {
  const title = document.getElementById('modalTitle').textContent;
  const priceText = document.getElementById('modalPrice').textContent;
  const price = parseFloat(priceText.replace('₱', ''));
  const img = document.getElementById('modalImg').src;
  
  const existingItem = cart.find(item => item.title === title);
  
  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.push({
      title,
      price,
      quantity,
      img
    });
  }
  
  updateCart();
  closeModal();
  showNotification('Added to cart!');
}

function updateCart() {
  const cartItems = document.getElementById('cartItems');
  const cartBadge = document.getElementById('cartBadge');
  const cartTotal = document.getElementById('cartTotal');
  
  if (cart.length === 0) {
    cartItems.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
    cartBadge.style.display = 'none';
  } else {
    let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartBadge.textContent = totalItems;
    cartBadge.style.display = 'flex';
    
    cartItems.innerHTML = cart.map((item, index) => `
      <div class="cart-item">
        <img src="${item.img}" alt="${item.title}" />
        <div class="cart-item-details">
          <h4>${item.title}</h4>
          <p class="cart-item-price">₱${item.price.toFixed(2)}</p>
          <div class="cart-item-qty">
            <button onclick="updateItemQty(${index}, -1)">−</button>
            <span>${item.quantity}</span>
            <button onclick="updateItemQty(${index}, 1)">+</button>
          </div>
        </div>
        <button class="remove-item" onclick="removeItem(${index})">×</button>
      </div>
    `).join('');
  }
  
  const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  cartTotal.textContent = `₱${total.toFixed(2)}`;
}

function updateItemQty(index, change) {
  cart[index].quantity += change;
  if (cart[index].quantity <= 0) {
    cart.splice(index, 1);
  }
  updateCart();
}

function removeItem(index) {
  cart.splice(index, 1);
  updateCart();
}

function clearCart() {
  if (confirm('Are you sure you want to clear your cart?')) {
    cart = [];
    updateCart();
  }
}

function toggleCart() {
  const sidebar = document.getElementById('cartSidebar');
  const overlay = document.getElementById('cartOverlay');
  
  sidebar.classList.toggle('open');
  overlay.classList.toggle('active');
}

function checkout() {
  if (cart.length === 0) {
    alert('Your cart is empty!');
    return;
  }
  alert('Thank you for your order! Total: ' + document.getElementById('cartTotal').textContent);
  cart = [];
  updateCart();
  toggleCart();
}

function showNotification(message) {
  const notification = document.createElement('div');
  notification.className = 'notification show';
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => notification.remove(), 300);
  }, 2000);
}