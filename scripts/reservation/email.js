// =============================================
// Casa De Manila — Frontend Form Handler
// scripts/reservation/email.js
// =============================================

const API_BASE = '/mainproj/ALP097/api.php?action=';

const datePicker = document.getElementById('date');
const timePicker = document.getElementById('time');

// ── Fetch real public IP on page load ──
fetch('https://api.ipify.org?format=json')
  .then(res => res.json())
  .then(data => { document.getElementById('user_ip').value = data.ip; })
  .catch(()  => { document.getElementById('user_ip').value = 'Unknown'; });

// =============================================
// REAL-TIME DATE BLOCKING
// =============================================
function getToday() {
  return new Date().toISOString().split('T')[0];
}

function updateDateMin() {
  datePicker.setAttribute('min', getToday());
  if (datePicker.value && datePicker.value < getToday()) {
    datePicker.value = '';
    showError('date', 'Reservation date cannot be in the past.');
  }
}

updateDateMin();
setInterval(updateDateMin, 60000);

// =============================================
// REAL-TIME TIME BLOCKING
// =============================================
function getCurrentTime24() {
  const now = new Date();
  return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
}

function updateTimeMin() {
  if (datePicker.value === getToday()) {
    timePicker.setAttribute('min', getCurrentTime24());
    if (timePicker.value && timePicker.value < getCurrentTime24()) {
      timePicker.value = '';
      showError('time', 'Please select a future time for today.');
    }
  } else {
    timePicker.removeAttribute('min');
    clearError('time');
  }
}

datePicker.addEventListener('change', () => {
  clearError('date');
  updateTimeMin();
});

timePicker.addEventListener('change', () => {
  clearError('time');
  if (datePicker.value === getToday()) {
    if (timePicker.value && timePicker.value < getCurrentTime24()) {
      showError('time', 'Please select a future time for today.');
      timePicker.value = '';
    }
  }
});

setInterval(updateTimeMin, 60000);

// =============================================
// VALIDATION
// =============================================
const requiredFields = [
  { id: 'event',  label: 'Event' },
  { id: 'name',   label: 'Full Name' },
  { id: 'email',  label: 'Email Address' },
  { id: 'phone',  label: 'Phone Number' },
  { id: 'date',   label: 'Reservation Date' },
  { id: 'time',   label: 'Reservation Time' },
  { id: 'guests', label: 'Number of Guests' },
];

function showError(id, message) {
  const el        = document.getElementById(id);
  const errorSpan = document.getElementById(`error-${id}`);
  if (el) el.style.borderColor = '#e53e3e';
  if (errorSpan) {
    errorSpan.textContent      = message;
    errorSpan.style.visibility = 'visible';
  }
}

function clearError(id) {
  const el        = document.getElementById(id);
  const errorSpan = document.getElementById(`error-${id}`);
  if (el) el.style.borderColor = '';
  if (errorSpan) {
    errorSpan.textContent      = '';
    errorSpan.style.visibility = 'hidden';
  }
}

function clearAllErrors() {
  requiredFields.forEach(f => clearError(f.id));
}

function validateForm() {
  clearAllErrors();
  let isValid      = true;
  let firstInvalid = null;

  for (const field of requiredFields) {
    const el = document.getElementById(field.id);
    if (!el || !el.value.trim()) {
      showError(field.id, `${field.label} is required.`);
      if (!firstInvalid) firstInvalid = el;
      isValid = false;
    }
  }

  if (datePicker.value && datePicker.value < getToday()) {
    showError('date', 'Reservation date cannot be in the past.');
    if (!firstInvalid) firstInvalid = datePicker;
    isValid = false;
  }

  if (datePicker.value === getToday() && timePicker.value) {
    if (timePicker.value < getCurrentTime24()) {
      showError('time', 'Please select a future time for today.');
      if (!firstInvalid) firstInvalid = timePicker;
      isValid = false;
    }
  }

  if (firstInvalid) firstInvalid.focus();
  return isValid;
}

requiredFields.forEach(field => {
  if (field.id === 'date' || field.id === 'time') return;
  const el = document.getElementById(field.id);
  if (el) {
    el.addEventListener('input',  () => clearError(field.id));
    el.addEventListener('change', () => clearError(field.id));
  }
});

// =============================================
// FORM SUBMIT — saves to DB only via api.php
// =============================================
document.getElementById('res-form').addEventListener('submit', async function (e) {
  e.preventDefault();

  if (!validateForm()) return;

  const form = this;
  const btn  = form.querySelector('.btn');

  btn.innerText = 'Sending...';
  btn.disabled  = true;

  const formData = new FormData(form);
  const payload  = Object.fromEntries(formData.entries());

  try {
    const dbRes = await fetch(API_BASE + 'save_reservation', {
      method:      'POST',
      headers:     { 'Content-Type': 'application/json' },
      credentials: 'include',
      body:        JSON.stringify(payload),
    });
    const dbResult = await dbRes.json();

    if (!dbResult.success) {
      alert('⚠️ ' + (dbResult.message || 'Could not save reservation. Please try again.'));
      return;
    }

    clearAllErrors();
    document.getElementById('successModal').style.display = 'flex';
    form.reset();
    updateDateMin();
    timePicker.removeAttribute('min');

  } catch (error) {
    console.error('Submit error:', error);
    alert('Connection error! Please check your internet and try again.');
  } finally {
    btn.innerText = 'Book Now';
    btn.disabled  = false;
  }
});

function closeSuccessModal() {
  document.getElementById('successModal').style.display = 'none';
}