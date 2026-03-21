<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Casa De Manila | Home</title>
  <link rel="stylesheet" href="./styles/main.css">
    <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
</head>
<body>
  <!-- Navbar -->
<div class="navbar" id="navbar">
  <div class="logo">
    <a href="#home">Casa De Manila</a>
    <p>Authenticity You Can Taste</p> 
  </div>
  <div class="hamburger" id="ham  burger">
    <span></span><span></span><span></span>
  </div>
  <nav class="nav-links" id="navLinks">
    <ul>
      <li><a href="./index.php">Home</a></li>
      <li><a href="./reservation.php">Reservation</a></li>
      <li><a href="./menu.php">Menu</a></li>
      <li><a href="./events.php">Events</a></li>
      <li><a href="./contact_us.php">Contact</a></li>
      <li><a href="./about.php">About</a></li>
      <li><a href="./account.php">Login </a></li>
    </ul>
  </nav>
</div>


  <!-- Hero -->
  <section id="home" class="hero reveal visible">
    <div class="hero-text">
      <h1>Welcome to Casa De Manila</h1>
      <p>Authentic Filipino Cuisine • Traditional Foods</p>
      <p>Experience the rich flavors of the Philippines 
        through authentic,  traditional
         dishes made with time-honored recipes and fresh ingredients.</p>
      <a href="./reservation.html" class="btn">Reserve Now!</a>
    </div>
  </section>

  <!-- Events Hero -->
  <section class="events-hero reveal">
    <h1>Upcoming Events</h1>
    <p>Celebrate Filipino culture, food, and moments with us</p>
  </section>

  <section class="events-grid" id="eventsGrid">
    <div class="event-card reveal">
      <img src="./images/accoustic.webp" alt="Live Acoustic Night">
      <div class="event-content">
        <h3>🎶 Live Acoustic Night</h3>
        <p class="event-date">Every Friday | 7:00 PM</p>
        <p class="event-desc">
          Enjoy classic OPM and acoustic hits while dining with family and friends.
        </p>
        <a href="./reservation.html" class="event-btn">See more</a>    
      </div>
    </div>
    <div class="event-card reveal">
      <img src="./images/kamayan.jpg" alt="Kamayan Feast">
      <div class="event-content">
        <h3>🍽️ Kamayan Feast</h3>
        <p class="event-date">March 15, 2026</p>
        <p class="event-desc">
          Experience traditional Filipino boodle fight served on banana leaves.
        </p>
        <a href="./reservation.html" class="event-btn">Reserve Now!</a>
      </div>
    </div>
    <div class="event-card reveal">
      <img src="./images/Celebrate-Birthday-at-Home-with-Family-5-Simple-Ideas-and-Themes.webp" alt="Birthday Packages">
      <div class="event-content">
        <h3>🎉 Birthday Celebrations</h3>
        <p class="event-date">Available Anytime</p>
        <p class="event-desc">
          Custom birthday packages with food bundles, décor, and cake options.
        </p>
        <a href="./reservation.html" class="event-btn">Reserve Now!</a>
      </div>
    </div>
  </section>

  <!-- Menu -->
  <section id="menu" class="section reveal">
    <h2>Our Signature Dishes</h2>
    <div class="menu-items">
      <div class="item reveal">
        <a href="./menu.html">
          <img src="./images/adobo.jpg" alt="Adobo" class="menu-img">
        </a>
        Adobo
      </div>
      <div class="item reveal">
        <a href="./menu.html">
          <img src="./images/sinigang.jpg" alt="Sinigang" class="menu-img">
        </a>
        Tamarind soup<br>
        (Sinigang na baboy)
      </div>
      <div class="item reveal">
        <a href="./menu.html">
          <img src="./images/Lechon Kawali.jpg" alt="Lechon-kawali" class="menu-img">
        </a>
        Lechon Kawali
      </div>
      <div class="item reveal">
        <a href="./menu.html">
          <img src="./images/inasal.jpg" alt="Halo-Halo" class="menu-img">
        </a>
        Chicken Inasal
      </div>
    </div>
  </section>


  <footer class="footer reveal">
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

  <script src="./scripts/function.js"></script>
</body>
</html>