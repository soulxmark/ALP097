// =============================================
// Casa De Manila — Frontend Form Handler
// scripts/reservation/email.js
// =============================================

const scriptURL = 'https://script.google.com/macros/s/AKfycbyJmBFsvTIk_-tdU59KjOVyvmdURZ282lXUzS412g85b_Sv_PNEuG94wmC1c0HNptQaiA/exec';

const datePicker = document.getElementById('date');
const timePicker = document.getElementById('time');

// ── Fetch real public IP on page load ──
fetch('https://api.ipify.org?format=json')
  .then(res => res.json())
  .then(data => { document.getElementById('user_ip').value = data.ip; })
  .catch(()  => { document.getElementById('user_ip').value = 'Unknown'; });

// =============================================
// REAL-TIME DATE BLOCKING
// Sets today as min, and updates every minute
// so past dates are always blocked even if the
// user leaves the tab open overnight.
// =============================================
function getToday() {
  return new Date().toISOString().split('T')[0]; // YYYY-MM-DD
}

function updateDateMin() {
  datePicker.setAttribute('min', getToday());

  // If user somehow has a past date selected, clear it
  if (datePicker.value && datePicker.value < getToday()) {
    datePicker.value = '';
    showError('date', 'Reservation date cannot be in the past.');
  }
}

updateDateMin();
setInterval(updateDateMin, 60000); // refresh every minute

// =============================================
// REAL-TIME TIME BLOCKING
// When today is selected, blocks past hours AND
// past AM/PM — so 3 AM can't be chosen when it's
// already 3 PM.
// =============================================
function getCurrentTime24() {
  const now = new Date();
  return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
}

function updateTimeMin() {
  if (datePicker.value === getToday()) {
    timePicker.setAttribute('min', getCurrentTime24());

    // If a past time was already typed/selected, clear it
    if (timePicker.value && timePicker.value < getCurrentTime24()) {
      timePicker.value = '';
      showError('time', 'Please select a future time for today.');
    }
  } else {
    timePicker.removeAttribute('min');
    clearError('time');
  }
}

// Re-check time restriction whenever date changes
datePicker.addEventListener('change', () => {
  clearError('date');
  updateTimeMin();
});

// Validate on time change: catches 3 AM vs 3 PM confusion
// because the native time input is a 24h internal value —
// 3:00 AM = "03:00", 3:00 PM = "15:00", so comparison is exact.
timePicker.addEventListener('change', () => {
  clearError('time');
  if (datePicker.value === getToday()) {
    if (timePicker.value && timePicker.value < getCurrentTime24()) {
      showError('time', 'Please select a future time for today.');
      timePicker.value = '';
    }
  }
});

// Update time min every minute (handles open tabs)
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

  // Final safety check: block past time even if min attribute was bypassed
  if (datePicker.value === getToday() && timePicker.value) {
    if (timePicker.value < getCurrentTime24()) {
      showError('time', 'Please select a future time for today.');
      if (!firstInvalid) firstInvalid = timePicker;
      isValid = false;
    }
  }

  // Block past date even if min was bypassed
  if (datePicker.value && datePicker.value < getToday()) {
    showError('date', 'Reservation date cannot be in the past.');
    if (!firstInvalid) firstInvalid = datePicker;
    isValid = false;
  }

  if (firstInvalid) firstInvalid.focus();
  return isValid;
}

// Clear errors live as user fills fields
requiredFields.forEach(field => {
  if (field.id === 'date' || field.id === 'time') return; // handled by listeners above
  const el = document.getElementById(field.id);
  if (el) {
    el.addEventListener('input',  () => clearError(field.id));
    el.addEventListener('change', () => clearError(field.id));
  }
});

// =============================================
// FORM SUBMIT
// 1. Saves to reservations_tbl via submit_reservation.php
// 2. Also sends to Google Sheets as backup
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
    // ── Step 1: Save to database ──
    const dbRes  = await fetch('./submit_reservation.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload),
    });
    const dbResult = await dbRes.json();

    if (dbResult.status !== 'success') {
      alert('⚠️ ' + (dbResult.message || 'Could not save reservation. Please try again.'));
      return;
    }

    // ── Step 2: Also forward to Google Sheets (best-effort) ──
    const params = new URLSearchParams(payload);
    fetch(`${scriptURL}?${params.toString()}`, { method: 'GET' }).catch(() => {});

    // ── Show success modal ──
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