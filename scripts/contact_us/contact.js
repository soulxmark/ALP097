// =============================================
// Casa De Manila — Contact Form Handler
// scripts/contact.js
// =============================================

// STEP 1: Replace this URL with your Google Apps Script Web App URL
const GAS_URL = "https://script.google.com/macros/s/AKfycbyJmBFsvTIk_-tdU59KjOVyvmdURZ282lXUzS412g85b_Sv_PNEuG94wmC1c0HNptQaiA/exec";

const contactForm = document.getElementById('contactForm');
const successMsg  = document.getElementById('successMsg');
const submitBtn   = contactForm ? contactForm.querySelector('.form-submit') : null;

if (contactForm && successMsg && submitBtn) {
  contactForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';

    const payload = {
      fname:   document.getElementById('fname').value.trim(),
      lname:   document.getElementById('lname').value.trim(),
      email:   document.getElementById('email').value.trim(),
      phone:   document.getElementById('phone').value.trim(),
      subject: document.getElementById('subject').value,
      message: document.getElementById('message').value.trim(),
    };

    try {
      const response = await fetch(GAS_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'text/plain' },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      if (result.status === 'success') {
        contactForm.style.display = 'none';
        successMsg.style.display  = 'block';
      } else {
        alert('Something went wrong. Please try again or email us directly.');
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Send Message';
      }

    } catch (err) {
      // GAS still processes the request even if CORS blocks the response
      contactForm.style.display = 'none';
      successMsg.style.display  = 'block';
    }
  });
}