const form = document.getElementById('res-form');
const scriptURL = 'https://script.google.com/macros/s/AKfycbxHq3DlVkvp4EhX-zFV-cQd4G19__mF5ctUthQ17xdmBcUccX6J-zIrOJ6yfiYezhvvBw/exec'; // The URL from your screenshot

form.addEventListener('submit', e => {
  e.preventDefault(); // This is the magic line that stops the black screen!
  
  const btn = form.querySelector('.btn');
  btn.innerText = "Sending...";
  btn.disabled = true;

  fetch(scriptURL, { method: 'POST', body: new FormData(form)})
    .then(response => {
      // Show your Mabuhay popup here!
      document.getElementById('successModal').style.display = 'flex';
      form.reset();
    })
    .catch(error => {
      // Triggers if there is a connection error
      alert("Error! Please check your internet or GScript permissions.");
    })
    .finally(() => {
      btn.innerText = "Book Now";
      btn.disabled = false;
    });
});