// =============================================
// Casa De Manila — Frontend Form Handler
// scripts/email.js
// =============================================

const scriptURL = 'https://script.google.com/macros/s/AKfycbyJmBFsvTIk_-tdU59KjOVyvmdURZ282lXUzS412g85b_Sv_PNEuG94wmC1c0HNptQaiA/exec'; // STEP 1: Replace with your Google Apps Script Web App URL

// ── Fetch real public IP on page load ──
fetch('https://api.ipify.org?format=json')
  .then(res => res.json())
  .then(data => {
    document.getElementById('user_ip').value = data.ip;
  })
  .catch(() => {
    document.getElementById('user_ip').value = 'Unknown';
  });

// ── Form Submit ──
document.getElementById('res-form').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = this;
  const btn  = form.querySelector('.btn');

  btn.innerText = "Sending...";
  btn.disabled  = true;

  const formData = new FormData(form);
  const params   = new URLSearchParams();
  formData.forEach((value, key) => params.append(key, value));

  fetch(`${scriptURL}?${params.toString()}`, { method: 'GET' })
    .then(res => res.text())
    .then(text => {
      let result;
      try {
        result = JSON.parse(text);
      } catch {
        result = { status: "success" }; // Apps Script responded, treat as success
      }

      if (result.status === "success") {
        document.getElementById('successModal').style.display = 'flex';
        form.reset();
      } else if (result.status === "blocked") {
        alert("🚫 " + result.message);
      } else {
        alert("Something went wrong: " + (result.message || "Please try again."));
      }
    })
    .catch(error => {
      console.error("Fetch error:", error);
      alert("Connection error! Please check your internet and try again.");
    })
    .finally(() => {
      btn.innerText = "Book Now";
      btn.disabled  = false;
    });
});

function closeSuccessModal() {
  document.getElementById('successModal').style.display = 'none';
}
// Add this line at the start of handleReservation
sheet.appendRow([new Date(), "DEBUG", "Script triggered!"]);