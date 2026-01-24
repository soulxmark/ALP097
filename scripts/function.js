const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');
const navbar = document.querySelector('.navbar');

// Shopping cart array
let cart = [];

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
let currentItem = null;

function openModal(item) {
  quantity = 1;
  currentItem = item;
  document.getElementById('qty').textContent = quantity;

  document.getElementById('modalImg').src = item.querySelector('img').src;
  document.getElementById('modalTitle').textContent = item.querySelector('h3').textContent;
  document.getElementById('modalPrice').textContent = item.querySelector('.price').textContent;
  document.getElementById('modalDetails').textContent = item.querySelector('.details').textContent;

  document.getElementById('menuModal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('menuModal').style.display = 'none';
  currentItem = null;
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
  
  document.querySelectorAll('.menu-item').forEach(item => {
    item.style.display =
      category === 'all' || item.classList.contains(category)
        ? 'block'
        : 'none';
  });
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

// Add to cart function using array methods
function addToCart() {
  if (!currentItem) return;
  
  const itemName = document.getElementById('modalTitle').textContent;
  const itemPrice = document.getElementById('modalPrice').textContent;
  const itemImage = document.getElementById('modalImg').src;
  
  // Extract numeric price (remove ₱ symbol)
  const price = parseFloat(itemPrice.replace('₱', '').replace(',', ''));
  
  // Find existing item using array method
  const existingItemIndex = cart.findIndex(item => item.name === itemName);
  
  if (existingItemIndex !== -1) {
    // Update quantity using map
    cart = cart.map((item, index) => 
      index === existingItemIndex 
        ? { ...item, quantity: item.quantity + quantity }
        : item
    );
  } else {
    // Add new item using spread operator
    cart = [...cart, {
      name: itemName,
      price: price,
      quantity: quantity,
      image: itemImage
    }];
  }
  
  updateCartDisplay();
  closeModal();
  showNotification(`${itemName} added to cart!`);
}

// Update cart display using array methods
function updateCartDisplay() {
  // Calculate total count using reduce
  const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
  const cartBadge = document.getElementById('cartBadge');
  
  if (cartBadge) {
    cartBadge.textContent = cartCount;
    cartBadge.style.display = cartCount > 0 ? 'flex' : 'none';
  }
  
  // Save cart to localStorage
  localStorage.setItem('cart', JSON.stringify(cart));
}

// Show notification
function showNotification(message) {
  const notification = document.createElement('div');
  notification.className = 'notification';
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.add('show');
  }, 10);
  
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 2000);
}

// Toggle cart sidebar
function toggleCart() {
  const cartSidebar = document.getElementById('cartSidebar');
  cartSidebar.classList.toggle('open');
  renderCartItems();
}

// Render cart items in sidebar using array methods
function renderCartItems() {
  const cartItemsContainer = document.getElementById('cartItems');
  const cartTotal = document.getElementById('cartTotal');
  
  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
    cartTotal.textContent = '₱0';
    return;
  }
  
  // Map cart items to HTML strings
  const cartHTML = cart.map((item, index) => {
    const itemTotal = item.price * item.quantity;
    
    return `
      <div class="cart-item">
        <img src="${item.image}" alt="${item.name}">
        <div class="cart-item-details">
          <h4>${item.name}</h4>
          <p class="cart-item-price">₱${item.price.toFixed(2)}</p>
          <div class="cart-item-qty">
            <button onclick="updateCartQty(${index}, -1)">−</button>
            <span>${item.quantity}</span>
            <button onclick="updateCartQty(${index}, 1)">+</button>
          </div>
        </div>
        <button class="remove-item" onclick="removeFromCart(${index})">×</button>
      </div>
    `;
  }).join('');
  
  // Calculate total using reduce
  const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  
  cartItemsContainer.innerHTML = cartHTML;
  cartTotal.textContent = `₱${total.toFixed(2)}`;
}

// Update cart item quantity using array methods
function updateCartQty(index, change) {
  // Update quantity using map
  cart = cart.map((item, i) => 
    i === index 
      ? { ...item, quantity: item.quantity + change }
      : item
  );
  
  // Filter out items with quantity <= 0
  cart = cart.filter(item => item.quantity > 0);
  
  updateCartDisplay();
  renderCartItems();
}

// Remove item from cart using array methods
function removeFromCart(index) {
  // Filter out the item at the specified index
  cart = cart.filter((_, i) => i !== index);
  
  updateCartDisplay();
  renderCartItems();
}

// Clear entire cart
function clearCart() {
  if (confirm('Are you sure you want to clear your cart?')) {
    cart = [];
    updateCartDisplay();
    renderCartItems();
  }
}

// Get cart summary using array methods
function getCartSummary() {
  return {
    totalItems: cart.reduce((sum, item) => sum + item.quantity, 0),
    totalPrice: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
    items: cart.map(item => ({
      name: item.name,
      quantity: item.quantity,
      subtotal: item.price * item.quantity
    }))
  };
}

// Checkout function
function checkout() {
  if (cart.length === 0) {
    alert('Your cart is empty!');
    return;
  }
  
  const summary = getCartSummary();
  const itemsList = summary.items
    .map(item => `${item.name} x${item.quantity} - ₱${item.subtotal.toFixed(2)}`)
    .join('\n');
  
  const message = `Order Summary:\n\n${itemsList}\n\nTotal Items: ${summary.totalItems}\nTotal: ₱${summary.totalPrice.toFixed(2)}\n\nProceed to checkout?`;
  
  if (confirm(message)) {
    alert('Proceeding to checkout... (This would redirect to checkout page)');
    // You can redirect to a checkout page or process the order here
    // window.location.href = './checkout.html';
  }
}

// Load cart from localStorage on page load
window.addEventListener('DOMContentLoaded', () => {
  const savedCart = localStorage.getItem('cart');
  if (savedCart) {
    cart = JSON.parse(savedCart);
    updateCartDisplay();
  }
  
  // Add event listener to "Add to Cart" button
  const addToCartBtn = document.querySelector('.add-to-cart');
  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', addToCart);
  }
});