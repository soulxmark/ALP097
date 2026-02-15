// 1. CATCH THE IP
  fetch('https://api.ipify.org?format=json')
    .then(response => response.json())
    .then(data => {
      document.getElementById('user_ip').value = data.ip;
    })
    .catch(err => console.error("IP Catch Error:", err));

  // 2. SUBMIT TO GSCRIPT
  const form = document.querySelector('.reservation-form');
  form.addEventListener("submit", function(e) {
    e.preventDefault();
    
    const btn = form.querySelector('.btn');
    btn.innerText = "Processing...";
    btn.disabled = true;

    const formData = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(text => {
      if (text === "Success") {
        alert("Reservation successful! We've sent you an email.");
        form.reset();
      } else {
        alert("Oops! Something went wrong: " + text);
      }
      btn.innerText = "Book Now";
      btn.disabled = false;
    })
    .catch(err => {
      alert("Error submitting form.");
      btn.innerText = "Book Now";
      btn.disabled = false;
    });
  });