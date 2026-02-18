const form = document.getElementById('res-form');
const scriptURL = 'https://script.google.com/macros/s/AKfycbzPUb_h-BENvBxHoE4rGq_Li5BCDSS9_IaQX9TT-YodfRGj22wHNbsflTfg9H6hdFKZIA/exec'; // The URL from your screenshot

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