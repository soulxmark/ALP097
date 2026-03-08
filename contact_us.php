<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Casa De Manila | Contact Us</title>
  <link rel="stylesheet" href="./styles/contact_us.css">
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
</head>
<body>

  <!-- Navbar -->
  <div class="navbar" id="navbar">
    <div class="logo">
      <a href="./index.php">Casa De Manila</a>
      <p>Authenticity You Can Taste</p>
    </div>
    <div class="hamburger" id="hamburger">
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
      </ul>
    </nav>
  </div>

  <!-- Hero Banner -->
  <section class="contact-hero">
    <div class="contact-hero-content">
      <h1>Contact Us</h1>
      <p>We'd love to hear from you</p>
      <div class="hero-rule"></div>
    </div>
  </section>

  <!-- Contact Grid -->
  <div class="contact-section">

    <!-- Info -->
    <div class="contact-info reveal">
      <h2>Get in Touch</h2>
      <p>Whether you have a question about our menu, need help with a reservation, or want to plan a private event — our team is always ready to assist you.</p>

      <div class="info-item">
        <div class="info-icon">📍</div>
        <div class="info-body">
          <h3>Location</h3>
          <p>SM City Manila<br>Ermita, Manila, 1000<br>Metro Manila, Philippines</p>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">📞</div>
        <div class="info-body">
          <h3>Phone</h3>
          <p>+63 912 345 6789</p>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">✉️</div>
        <div class="info-body">
          <h3>Email</h3>
          <p>reservations@casamanila.ph</p>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">🕐</div>
        <div class="info-body">
          <h3>Hours</h3>
          <div class="hours-table">
            <span>Mon – Thu</span><span>11:00 AM – 9:00 PM</span>
            <span>Fri – Sat</span><span>11:00 AM – 10:30 PM</span>
            <span>Sunday</span><span>10:00 AM – 9:00 PM</span>
          </div>
        </div>
      </div>

      <div class="contact-social">
        <a href="#">Facebook</a>
        <a href="#">Instagram</a>
        <a href="#">Twitter</a>
      </div>
    </div>

    <!-- Form -->
    <div class="contact-form-wrap reveal" style="transition-delay: 0.15s;">
      <h2>Send a Message</h2>
      <p class="form-sub">Fill out the form and we'll respond within 24 hours.</p>

      <form id="contactForm">
        <div class="form-row">
          <div class="field">
            <label for="fname">First Name</label>
            <input type="text" id="fname" placeholder="Juan" required>
          </div>
          <div class="field">
            <label for="lname">Last Name</label>
            <input type="text" id="lname" placeholder="dela Cruz" required>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" placeholder="juan@email.com" required>
          </div>
          <div class="field">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" placeholder="+63 9XX XXX XXXX">
          </div>
        </div>

        <div class="field">
          <label for="subject">Subject</label>
          <select id="subject">
            <option value="" disabled selected>Select a topic</option>
            <option>General Inquiry</option>
            <option>Reservation</option>
            <option>Private Event</option>
            <option>Feedback</option>
            <option>Catering</option>
            <option>Other</option>
          </select>
        </div>

        <div class="field">
          <label for="message">Message</label>
          <textarea id="message" placeholder="Tell us how we can help..." required></textarea>
        </div>

        <button type="submit" class="form-submit">Send Message</button>
      </form>

      <div class="success-msg" id="successMsg">
        <div class="check">✅</div>
        <h3>Message Sent!</h3>
        <p>Salamat! We'll get back to you within 24 hours.</p>
      </div>
    </div>

  </div>

  <!-- Map -->
  <div class="map-strip reveal">
    <h2>Find Us</h2>
    <p>SM City Manila, Metro Manila</p>
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7722.354434765172!2d120.97522846447067!3d14.588975556121387!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cb209479aa11%3A0x872db4487b1bb367!2sSM%20City%20Manila!5e0!3m2!1sen!2sph!4v1771427686354!5m2!1sen!2sph"
      allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>

  <!-- Footer -->
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
  <script src="./scripts/contact_us/contact.js"></script>
</body>
</html>