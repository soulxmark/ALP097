<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Casa De Manila | Home</title>
  <link rel="stylesheet" href="./styles/main.css">
  <link rel="icon" type="image/x-icon" href="./images/logo/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
  <style>
    /* ── Chatbot Button ─────────────────────────────────────── */
    #cdm-chat-btn {
      position: fixed;
      bottom: 28px;
      right: 28px;
      width: 58px;
      height: 58px;
      border-radius: 50%;
      background: #d4af37;
      border: none;
      cursor: pointer;
      box-shadow: 0 6px 24px rgba(212, 175, 55, 0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6em;
      z-index: 8888;
      transition: transform .25s, box-shadow .25s;
    }

    #cdm-chat-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 10px 32px rgba(212, 175, 55, 0.6);
    }

    /* ── Chat Window ────────────────────────────────────────── */
    #cdm-chat-window {
      position: fixed;
      bottom: 100px;
      right: 28px;
      width: 360px;
      max-height: 520px;
      background: #111;
      border: 1px solid rgba(212, 175, 55, 0.35);
      border-radius: 20px;
      display: flex;
      flex-direction: column;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.55);
      z-index: 8888;
      overflow: hidden;
      transform: scale(0.92) translateY(16px);
      opacity: 0;
      pointer-events: none;
      transition: transform .3s ease, opacity .3s ease;
    }

    #cdm-chat-window.open {
      transform: scale(1) translateY(0);
      opacity: 1;
      pointer-events: all;
    }

    .cdm-chat-header {
      background: linear-gradient(135deg, #1a1200, #2a1f00);
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .cdm-chat-header-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .cdm-chat-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: rgba(212, 175, 55, 0.15);
      border: 1.5px solid #d4af37;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1em;
    }

    .cdm-chat-name {
      font-family: 'Cormorant Garamond', serif;
      color: #d4af37;
      font-size: 1em;
      font-weight: 600;
    }

    .cdm-chat-status {
      font-size: .72em;
      color: rgba(255, 255, 255, .4);
      letter-spacing: .5px;
    }

    .cdm-chat-close {
      background: none;
      border: none;
      color: rgba(255, 255, 255, .4);
      font-size: 1.4em;
      cursor: pointer;
      line-height: 1;
      padding: 0;
      transition: color .2s;
    }

    .cdm-chat-close:hover {
      color: #d4af37;
    }

    .cdm-chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      scroll-behavior: smooth;
    }

    .cdm-chat-messages::-webkit-scrollbar {
      width: 4px;
    }

    .cdm-chat-messages::-webkit-scrollbar-thumb {
      background: rgba(212, 175, 55, .3);
      border-radius: 2px;
    }

    .cdm-msg {
      max-width: 82%;
      font-family: 'Cormorant Garamond', serif;
      font-size: .97em;
      line-height: 1.55;
      padding: 10px 14px;
      border-radius: 14px;
      word-break: break-word;
    }

    .cdm-msg.bot {
      background: rgba(212, 175, 55, 0.1);
      border: 1px solid rgba(212, 175, 55, 0.18);
      color: rgba(255, 255, 255, .88);
      align-self: flex-start;
      border-bottom-left-radius: 4px;
    }

    .cdm-msg.user {
      background: #d4af37;
      color: #111;
      align-self: flex-end;
      font-weight: 600;
      border-bottom-right-radius: 4px;
    }

    .cdm-msg.typing {
      opacity: .6;
      font-style: italic;
    }

    .cdm-chat-footer {
      border-top: 1px solid rgba(212, 175, 55, 0.15);
      padding: 12px 14px;
      display: flex;
      gap: 8px;
      align-items: flex-end;
      background: rgba(255, 255, 255, .03);
    }

    #cdm-chat-input {
      flex: 1;
      background: rgba(255, 255, 255, .07);
      border: 1px solid rgba(212, 175, 55, .22);
      border-radius: 10px;
      color: #fff;
      font-family: 'Cormorant Garamond', serif;
      font-size: .97em;
      padding: 9px 13px;
      outline: none;
      resize: none;
      max-height: 100px;
      line-height: 1.4;
      transition: border-color .3s;
    }

    #cdm-chat-input::placeholder {
      color: rgba(255, 255, 255, .25);
    }

    #cdm-chat-input:focus {
      border-color: #d4af37;
    }

    #cdm-chat-send {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: #d4af37;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1em;
      transition: background .2s, transform .15s;
      flex-shrink: 0;
    }

    #cdm-chat-send:hover {
      background: #e8c84a;
      transform: scale(1.08);
    }

    #cdm-chat-send:disabled {
      opacity: .5;
      cursor: not-allowed;
      transform: none;
    }

    /* Quick replies */
    .cdm-quick-replies {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      padding: 0 16px 10px;
    }

    .cdm-qr {
      background: rgba(212, 175, 55, .1);
      border: 1px solid rgba(212, 175, 55, .28);
      color: #d4af37;
      border-radius: 20px;
      padding: 5px 12px;
      font-size: .82em;
      font-family: 'Cormorant Garamond', serif;
      cursor: pointer;
      transition: background .2s;
      white-space: nowrap;
    }

    .cdm-qr:hover {
      background: rgba(212, 175, 55, .22);
    }

    @media (max-width: 480px) {
      #cdm-chat-window {
        width: calc(100vw - 20px);
        right: 10px;
        bottom: 90px;
      }
    }
  </style>
</head>

<body>

  <div class="navbar" id="navbar">
    <div class="logo">
      <a href="#home">Casa De Manila</a>
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
        <li id="nav-auth"><a href="./login.php">Login</a></li>
      </ul>
    </nav>
  </div>

  <section id="home" class="hero reveal visible">
    <div class="hero-text">
      <h1>Welcome to Casa De Manila</h1>
      <p>Authentic Filipino Cuisine • Traditional Foods</p>
      <p>Experience the rich flavors of the Philippines through authentic, traditional dishes made with time-honored recipes and fresh ingredients.</p>
      <a href="./reservation.php" class="btn">Reserve Now!</a>
    </div>
  </section>

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
        <p class="event-desc">Enjoy classic OPM and acoustic hits while dining with family and friends.</p>
        <a href="./reservation.php" class="event-btn">See more</a>
      </div>
    </div>
    <div class="event-card reveal">
      <img src="./images/kamayan.jpg" alt="Kamayan Feast">
      <div class="event-content">
        <h3>🍽️ Kamayan Feast</h3>
        <p class="event-date">March 15, 2026</p>
        <p class="event-desc">Experience traditional Filipino boodle fight served on banana leaves.</p>
        <a href="./reservation.php" class="event-btn">Reserve Now!</a>
      </div>
    </div>
    <div class="event-card reveal">
      <img src="./images/Celebrate-Birthday-at-Home-with-Family-5-Simple-Ideas-and-Themes.webp" alt="Birthday Packages">
      <div class="event-content">
        <h3>🎉 Birthday Celebrations</h3>
        <p class="event-date">Available Anytime</p>
        <p class="event-desc">Custom birthday packages with food bundles, décor, and cake options.</p>
        <a href="./reservation.php" class="event-btn">Reserve Now!</a>
      </div>
    </div>
  </section>

  <section id="menu" class="section reveal">
    <h2>Our Signature Dishes</h2>
    <div class="menu-items">
      <div class="item reveal">
        <a href="./menu.php"><img src="./images/adobo.jpg" alt="Adobo" class="menu-img"></a>
        Adobo
      </div>
      <div class="item reveal">
        <a href="./menu.php"><img src="./images/sinigang.jpg" alt="Sinigang" class="menu-img"></a>
        Tamarind soup
      </div>
      <div class="item reveal">
        <a href="./menu.php"><img src="./images/Lechon Kawali.jpg" alt="Lechon-kawali" class="menu-img"></a>
        Lechon Kawali
      </div>
      <div class="item reveal">
        <a href="./menu.php"><img src="./images/inasal.jpg" alt="Chicken Inasal" class="menu-img"></a>
        Chicken Inasal
      </div>
    </div>
  </section>

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

  <!-- ── Chatbot Button ──────────────────────────────────────── -->
  <button id="cdm-chat-btn" onclick="toggleChat()" title="Chat with us">🍽️</button>

  <!-- ── Chat Window ────────────────────────────────────────── -->
  <div id="cdm-chat-window">
    <div class="cdm-chat-header">
      <div class="cdm-chat-header-left">
        <div class="cdm-chat-avatar">🍽️</div>
        <div>
          <div class="cdm-chat-name">Casa De Manila</div>
          <div class="cdm-chat-status">AI Concierge • Always here</div>
        </div>
      </div>
      <button class="cdm-chat-close" onclick="toggleChat()">×</button>
    </div>

    <div class="cdm-chat-messages" id="cdmMessages"></div>

    <div class="cdm-quick-replies" id="cdmQuickReplies">
      <button class="cdm-qr" onclick="sendQuick('What dishes do you serve?')">Our dishes</button>
      <button class="cdm-qr" onclick="sendQuick('How do I make a reservation?')">Reservations</button>
      <button class="cdm-qr" onclick="sendQuick('What are your opening hours?')">Hours</button>
      <button class="cdm-qr" onclick="sendQuick('Where are you located?')">Location</button>
    </div>

    <div class="cdm-chat-footer">
      <textarea id="cdm-chat-input" placeholder="Ask me anything…" rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
      <button id="cdm-chat-send" onclick="sendMessage()">➤</button>
    </div>
  </div>

  <script src="./scripts/function.js"></script>
  <script>
    /* ── Chat State ─────────────────────────────────────────────── */
    const CDM_HISTORY = [];
    let cdmOpen = false;

    const CDM_SYSTEM = `You are the friendly AI concierge for Casa De Manila, an authentic Filipino restaurant in SM City Manila, Metro Manila, Philippines.

Key facts:
- Address: SM City Manila, Ermita, Manila, 1000, Metro Manila
- Phone: +63 912 345 6789
- Email: reservations@casamanila.ph
- Hours: Mon–Thu 11AM–9PM, Fri–Sat 11AM–10:30PM, Sunday 10AM–9PM
- Cuisine: Authentic Filipino home-style cooking (lutong bahay)

Menu highlights:
Mains: Chicken Adobo ₱250, Pork Steak ₱250, Beef Afritada ₱250, Pork Afritada ₱250, Lechon Kawali ₱320, Kare-Kare ₱300
Veggies: Chopsuey ₱180, Pakbet ₱180
Desserts: Leche Flan ₱150, Halo-Halo ₱180, Turon ₱120, Buko Pie ₱120
Drinks: Calamansi Juice ₱80, Mango Shake ₱100, Buko Juice ₱90

Events: Live Acoustic Night every Friday 7PM, Kamayan Feast, Birthday Celebration packages
Reservations: Available online at the Reservation page or by calling +63 912 345 6789

Keep responses warm, concise, and helpful. Use Filipino hospitality. End with a helpful follow-up offer when appropriate. Use ₱ for prices.`;

    /* ── Toggle ─────────────────────────────────────────────────── */
    function toggleChat() {
      cdmOpen = !cdmOpen;
      document.getElementById('cdm-chat-window').classList.toggle('open', cdmOpen);
      if (cdmOpen && CDM_HISTORY.length === 0) {
        appendMsg('bot', 'Mabuhay! 👋 Welcome to Casa De Manila. I\'m your AI concierge. How can I help you today — reservations, menu, or anything else?');
      }
      if (cdmOpen) setTimeout(() => document.getElementById('cdm-chat-input').focus(), 300);
    }

    /* ── Send (Manual Version) ──────────────────────────────────── */
    function sendMessage() {
      const input = document.getElementById('cdm-chat-input');
      const text = input.value.trim();
      if (!text) return;

      input.value = '';
      input.style.height = 'auto';
      appendMsg('user', text);

      // Hide quick replies after first message
      document.getElementById('cdmQuickReplies').style.display = 'none';

      const typingId = appendMsg('bot', '…', true);
      document.getElementById('cdm-chat-send').disabled = true;

      // Simulate a slight delay so it feels natural
      setTimeout(() => {
        removeMsg(typingId);

        // Get the manual response based on keywords
        const reply = getManualResponse(text);

        appendMsg('bot', reply);
        document.getElementById('cdm-chat-send').disabled = false;
        document.getElementById('cdm-chat-input').focus();
      }, 600);
    }

    /* ── Manual Response Logic ──────────────────────────────────── */
    function getManualResponse(userInput) {
      // Convert input to lowercase to make keyword matching easier
      const text = userInput.toLowerCase();

      // Menu / Food Queries
      if (text.includes('menu') || text.includes('dish') || text.includes('food') || text.includes('serve')) {
        return "Here are some highlights from our menu:\n\n🍗 Mains: Chicken Adobo ₱250, Lechon Kawali ₱320, Kare-Kare ₱300\n🥦 Veggies: Chopsuey ₱180, Pakbet ₱180\n🍧 Desserts: Halo-Halo ₱180, Leche Flan ₱150\n\nWould you like to know about our drinks?";
      }
      // Drink Queries
      else if (text.includes('drink') || text.includes('beverage') || text.includes('juice')) {
        return "For drinks, we offer refreshing Calamansi Juice ₱80, Mango Shake ₱100, and Buko Juice ₱90.";
      }
      // Reservation Queries
      else if (text.includes('reserve') || text.includes('reservation') || text.includes('book')) {
        return "You can easily reserve a table by clicking the 'Reservation' link in our navigation bar, or by calling us directly at +63 912 345 6789.";
      }
      // Hours Queries
      else if (text.includes('hour') || text.includes('open') || text.includes('time')) {
        return "Our operating hours are:\nMon–Thu: 11:00 AM – 9:00 PM\nFri–Sat: 11:00 AM – 10:30 PM\nSunday: 10:00 AM – 9:00 PM";
      }
      // Location Queries
      else if (text.includes('location') || text.includes('where') || text.includes('address')) {
        return "We are located at SM City Manila, Ermita, Manila, 1000, Metro Manila.";
      }
      // Default Fallback
      else {
        return "Mabuhay! I'm here to help with information about Casa De Manila. You can ask me about our menu, reservations, opening hours, or location!";
      }
    }

    function sendQuick(text) {
      document.getElementById('cdm-chat-input').value = text;
      sendMessage();
    }

    /* ── DOM helpers ────────────────────────────────────────────── */
    function appendMsg(role, text, isTyping = false) {
      const box = document.getElementById('cdmMessages');
      const div = document.createElement('div');
      const id = 'cdm-msg-' + Date.now() + Math.random();
      div.id = id;
      div.className = 'cdm-msg ' + role + (isTyping ? ' typing' : '');
      div.textContent = text;
      box.appendChild(div);
      box.scrollTop = box.scrollHeight;
      return id;
    }

    function removeMsg(id) {
      const el = document.getElementById(id);
      if (el) el.remove();
    }
  </script>
</body>

</html>