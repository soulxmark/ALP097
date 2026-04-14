// =============================================
// Casa De Manila — Main Handler
// Code.gs
// =============================================
// IMPORTANT: Delete otp.js.gs after saving this file.
// Having two doGet functions breaks everything.
// =============================================

// ── Run once manually to authorize Gmail ─────────────────────────────────
function authorizeAndTest() {
  GmailApp.getInboxThreads(0, 1);
  GmailApp.sendEmail(
    RECIPIENT_EMAIL,
    "✅ Casa De Manila — Gmail Authorization Successful",
    "GmailApp is authorized and working correctly.\nYour reservation system is ready to receive bookings."
  );
  Logger.log("Authorization and test email sent successfully!");
}

// ── Main GET entry point ──────────────────────────────────────────────────
function doGet(e) {
  var params = (e && e.parameter) ? e.parameter : {};
  var action = params.action || '';

  // ── OTP: called by api.php to send login code ─────────────────────────
  if (action === 'send_otp') {
    var to  = params.email || '';
    var otp = params.otp   || '';

    if (!to || !otp) {
      return jsonResponse({ success: false, message: 'Missing email or otp.' });
    }
    try {
      GmailApp.sendEmail(to, 'Your Casa De Manila Login Code', '', {
        htmlBody:
          '<div style="font-family:Georgia,serif;max-width:480px;margin:0 auto;padding:32px;background:#f9f5ec;border-radius:12px;">' +
            '<h2 style="color:#d4af37;font-size:1.8em;margin:0 0 8px;">Casa De Manila</h2>' +
            '<p style="color:#555;margin:0 0 24px;font-size:0.9em;">Authenticity You Can Taste</p>' +
            '<p style="color:#333;margin:0 0 16px;">Your one-time login code is:</p>' +
            '<div style="font-size:2.4em;font-weight:bold;letter-spacing:10px;color:#111;background:#fff;border:2px solid #d4af37;border-radius:8px;padding:16px 24px;text-align:center;margin-bottom:20px;">' +
              otp +
            '</div>' +
            '<p style="color:#888;font-size:0.82em;margin:0;">Expires in <strong>5 minutes</strong>. Do not share it.</p>' +
          '</div>'
      });
      return jsonResponse({ success: true });
    } catch (err) {
      return jsonResponse({ success: false, message: err.toString() });
    }
  }

  // ── Reservation: save to sheet + send emails ──────────────────────────
  if (Object.keys(params).length > 0) {
    return handleReservation(params);
  }

  return jsonResponse({ status: 'ok', message: 'Casa De Manila Reservation API is live.' });
}

// ── POST entry point ──────────────────────────────────────────────────────
function doPost(e) {
  try {
    if (!e || !e.parameter) throw new Error('No form data received.');
    return handleReservation(e.parameter);
  } catch (error) {
    return jsonResponse({ status: 'error', message: error.message });
  }
}

// ── Reservation handler ───────────────────────────────────────────────────
function handleReservation(data) {
  try {
    var name    = data.name    || 'N/A';
    var email   = data.email   || 'N/A';
    var phone   = data.phone   || 'Not provided';
    var date    = data.date    || 'N/A';
    var time    = data.time    || 'N/A';
    var guests  = data.guests  || 'N/A';
    var event   = data.event   || 'N/A';
    var notes   = data.notes   || 'None';
    var userIP  = data.user_ip || 'Unknown';

    Logger.log("Reservation received: " + JSON.stringify(data));

    // Spam check (defined in spam_guard.gs — keep that file)
    var spamCheck = checkSpam(userIP);
    if (spamCheck.blocked) {
      return jsonResponse({ status: 'blocked', message: spamCheck.message });
    }

    // Save to Google Sheets
    var ss    = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = ss.getSheets()[0];
    sheet.appendRow([new Date(), name, email, phone, date, time, guests, event, notes, userIP]);

    // Send emails (defined in email_response.gs — keep that file)
    sendAdminEmail(name, email, phone, date, time, guests, event, notes, userIP);
    sendCustomerEmail(name, email, date, time, guests, event);

    return jsonResponse({ status: 'success' });

  } catch (error) {
    Logger.log("Error in handleReservation: " + error.message);
    return jsonResponse({ status: 'error', message: error.message });
  }
}

// ── Helper ────────────────────────────────────────────────────────────────
function jsonResponse(obj) {
  return ContentService
    .createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}