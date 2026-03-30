<!-- reservation.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Casa De Manila | Reservation</title>
  <link rel="stylesheet" href="./styles/reservation.css">
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
</head>

<body>

  <header>
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
  </header>

  <!-- Reservation Section -->
  <section id="reservation" class="section reveal">
    <h2>Reserve Your Table</h2>
    <div class="reservation-container">
      <!-- Left side -->
      <div class="reservation-info">
        <img src="./images/hero.webp" alt="Casa De Manila Dining" class="reservation-img">
        <p>
          Experience authentic Filipino cuisine in a warm, elegant setting.
          Reserve your table today and enjoy our signature dishes with family and friends.
        </p>
      </div>

      <!-- Right side -->
      <form class="reservation-form" id="res-form" action="https://script.google.com/macros/s/AKfycbyJmBFsvTIk_-tdU59KjOVyvmdURZ282lXUzS412g85b_Sv_PNEuG94wmC1c0HNptQaiA/exec" method="POST">
        <div class="form-group">
          <input type="hidden" id="user_ip" name="user_ip" value="Unknown">
          <label for="event">Event</label>
          <select id="event" name="event" required>
            <option value="">Select event</option>
            <option value="birthday">Birthday</option>
            <option value="corporate">Corporate</option>
            <option value="family">Family Gathering</option>
            <option value="Reserve_Table">Reserve Table Only</option>
          </select>
        </div>


        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="form-group">
          <label for="date">Reservation Date</label>
          <input type="date" id="date" name="date" required>
        </div>

        <div class="form-group">
          <label for="time">Reservation Time</label>
          <input type="time" id="time" name="time" required>
        </div>

        <div class="form-group">
          <label for="guests">Number of Guests</label>
          <input type="number" id="guests" name="guests" min="1" max="20" required>
        </div>
        <div class="form-group">
          <label for="notes">Special Requests</label>
          <textarea id="notes" name="notes" rows="7" cols="70" placeholder="Any special requests?"></textarea>
        </div>
        <button type="submit" class="btn">Book Now</button>
      </form>
    </div>
  </section>

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
  <script src="./scripts/reservation/email.js"></script>
  <div id="successModal" class="modal-overlay">
    <div class="modal-content">
      <span class="close-modal" onclick="closeSuccessModal()">&times;</span>
      <div class="modal-body">
        <div class="success-icon">✔</div>
        <h3>Mabuhay!</h3>
        <p>Your reservation has been sent successfully.</p>
        <p>A confirmation email is on its way to your inbox.</p>
        <button type="button" onclick="closeSuccessModal()" class="btn">Close</button>
      </div>
    </div>
  </div>
</body>

</html>