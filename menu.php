<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Casa De Manila | Menu</title>
  <link rel="stylesheet" href="./styles/menu.css" />
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
</head>
<body>

<header>
  <nav class="navbar" id="navbar">
    <div class="logo">
      <a href="./index.html">Casa De Manila</a>
      <p>Authenticity You Can Taste</p>
    </div>
    <div class="hamburger" id="hamburger">
      <span></span><span></span><span></span>
    </div>
    <nav class="nav-links" id="navLinks">
      <ul>
        <li><a href="./index.html">Home</a></li>
          <li><a href="./reservation.html">Reservation</a></li>
              <li><a href="./menu.html">Menu</a></li>
                <li><a href="./events.html">Events</a></li>
              <li><a href="./contact_us.html">Contact</a></li>
            <li><a href="./about.html">About</a></li>
        <li>
          <a href="#" onclick="toggleCart(); return false;" class="cart-btn">
            🛒 Cart
            <span id="cartBadge" class="cart-badge">0</span>
          </a>
        </li>
      </ul>
    </nav>
  </nav>
</header>

<section class="menu-controls">
    <div class="filters">
      <button class="filter-btn active" onclick="filterMenu('all')">All</button>
        <button class="filter-btn" onclick="filterMenu('mains')">Mains</button>
          <button class="filter-btn" onclick="filterMenu('veggies')">Veggies</button>
        <button class="filter-btn" onclick="filterMenu('desserts')">Desserts</button>
      <button class="filter-btn" onclick="filterMenu('drinks')">Drinks</button>
    </div>
  <input type="text" id="searchInput" placeholder="Search dishes..." onkeyup="searchMenu()" />
</section>

<section class="menu-grid" id="menuGrid">
<!-- Mains -->
  <div class="menu-item mains" onclick="openModal(this)">
    <img src="./images/adobo.jpg" alt="Chicken Adobo" />
      <h3>Chicken Adobo</h3>
        <p class="price">₱250</p>
        <div class="details">"Succulent chicken slow-braised in a savory-tangy blend of fermented vinegar, premium soy sauce, and toasted garlic. 
      Finished with cracked black peppercorns for a deep, aromatic flavor."
    </div>
  </div>
<!--New Menu line-->
 <div class="menu-item mains" onclick="openModal(this)">
  <img src="./images/pork-steak-5.jpg" alt="Filipino Pork Steak" />
    <h3>Pork Steak</h3> <p class="price">₱250</p>
      <div class="details">
        Tender pork slices marinated in soy sauce and calamansi, topped with a generous serving .
      </div>
  </div>
 <div class="menu-item mains" onclick="openModal(this)">
    <img src="./images/beef-afritada.jpg" alt="Beef Afritada" />
      <h3>Beef Afritada</h3>
        <p class="price">₱250</p>
    <div class="details">
    Tender beef chunks slow-cooked in a rich tomato sauce with bell peppers, potatoes, and carrots.
  </div>
</div>
<div class="menu-item mains" onclick="openModal(this)">
  <img src="./images/pork-afritada.jpg" alt="Pork Afritada" />
    <h3>Pork Afritada</h3>
      <p class="price">₱250</p>
    <div class="details">
    Succulent pork chunks slow-cooked in a savory tomato sauce with potatoes, carrots, and bell peppers.
  </div>
</div>
<!--Veggies Section-->
<div class="menu-item veggies" onclick="openModal(this)">
    <img src="./images/chapsuey.webp" alt="Chopsuey" />
      <h3>Chopsuey</h3>
        <p class="price">₱180</p>
      <div class="details">
    A vibrant medley of crisp cauliflower, carrots, and cabbage stir-fried in a silky, savory glaze.
  </div>
</div>
 <!--End Last Edited-->
   <div class="menu-item veggies" onclick="openModal(this)">
      <img src="./images/pakbet.jpg" alt="pakbet" />
        <h3>Pakbet</h3>
      <p class="price">₱180</p>
    <div class="details">Sautéed bitter melon with eggs, tomatoes, and onions. Healthy and flavorful.</div>
  </div>
  

    <!--End line-->
 

  <div class="menu-item mains" onclick="openModal(this)">
    <img src="./images/Lechon Kawali.jpg" alt="Lechon Kawali" />
      <h3>Lechon Kawali</h3>
          <p class="price">₱320</p>
      <div class="details">Crispy deep-fried pork belly served with liver sauce.</div>
  </div>

  <div class="menu-item mains" onclick="openModal(this)">
    <img src="./images/karekare.jpg" alt="Kare-Kare" />
      <h3>Kare-Kare</h3>
        <p class="price">₱300</p>
    <div class="details">Oxtail stew in peanut sauce with vegetables and bagoong.</div>
  </div>

  <!-- Desserts -->
  <div class="menu-item desserts" onclick="openModal(this)">
    <img src="./images/Leche-flan.jpg" alt="Leche Flan" />
      <h3>Leche Flan</h3>
        <p class="price">₱150</p>
    <div class="details">Rich caramel custard dessert. Smooth and creamy.</div>
  </div>

  <div class="menu-item desserts" onclick="openModal(this)">
    <img src="./images/halo-halo.jpg" alt="Halo-Halo" />
      <h3>Halo-Halo</h3>
        <p class="price">₱180</p>
    <div class="details">Mixed shaved ice with sweet beans, fruits, and ube ice cream.</div>
  </div>

  <div class="menu-item desserts" onclick="openModal(this)">
    <img src="./images/turon.webp" alt="Turon" />
      <h3>Turon</h3>
        <p class="price">₱120</p>
    <div class="details">Fried banana spring rolls with jackfruit and brown sugar.</div>
  </div>


   <div class="menu-item desserts" onclick="openModal(this)">
    <img src="./images/buko-pie.jpg" alt="Buko Pie" />
      <h3>Buko Pie</h3>
        <p class="price">₱120</p>
    <div class="details">A classic Filipino favorite featuring a buttery, flaky crust filled with a creamy, sweet custard and tender strips of young coconut.</div>
  </div>

  <!-- Drinks -->
  <div class="menu-item drinks" onclick="openModal(this)">
    <img src="./images/kalamnsi.webp" alt="Calamansi Juice" />
    <h3>Calamansi Juice</h3>
    <p class="price">₱80</p>
    <div class="details">Fresh squeezed calamansi lime juice. Refreshing and tangy.</div>
  </div>

  <div class="menu-item drinks" onclick="openModal(this)">
    <img src="./images/Mango-Shake-Wide.webp" alt="Mango Shake" />
    <h3>Mango Shake</h3>
    <p class="price">₱100</p>
    <div class="details">Creamy mango shake made with fresh Philippine mangoes.</div>
  </div>

  <div class="menu-item drinks" onclick="openModal(this)">
    <img src="./images/buko.webp" alt="Buko Juice" />
    <h3>Buko Juice</h3>
    <p class="price">₱90</p>
    <div class="details">Fresh young coconut water. Natural and hydrating.</div>
  </div>
</section>

<!-- Modal Section -->
<section id="modal-section">
  <div id="menuModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <img id="modalImg" alt="Menu Item" />
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

<!-- Cart Section -->
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
      <button class="checkout-btn" onclick="checkout()">Place Order</button>
    </div>
  </div>

  <!-- Cart Overlay -->
  <div id="cartOverlay" class="cart-overlay" onclick="toggleCart()"></div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="footer-container">
    <p>&copy; 2026 Casa De Manila. All rights reserved.</p>
    <p>Email: reservations@casamanila.ph | Phone: +63 912 345 6789</p>
    <div class="social-links">
      <a href="#">Facebook</a>
      <a href="#">Instagram</a>
      <a href="#">Twitter</a>
    </div>
  </div>
</footer>

<script src="./scripts/menu.js"></script>
</body>
</html>