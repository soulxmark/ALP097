<?php
/* ============================================================
   Casa De Manila — Database Connection
   File: connection.php  (must be in ROOT project folder)
============================================================ */

// Show errors during development — REMOVE in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ── Credentials ───────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_USER',    'root');        // ← your phpMyAdmin username
define('DB_PASS',    '');            // ← your phpMyAdmin password (blank = XAMPP default)
define('DB_NAME',    'casa_de_manila');
define('DB_CHARSET', 'utf8mb4');
// ─────────────────────────────────────────────────────────────

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die('
      <div style="font-family:sans-serif;text-align:center;
                  padding:60px 20px;background:#111;color:#fff;min-height:100vh;">
        <h2 style="color:#d4af37;">Database Connection Failed</h2>
        <p style="color:#ccc;margin-top:16px;">
          Error: ' . htmlspecialchars($mysqli->connect_error) . '
        </p>
        <p style="color:#aaa;margin-top:12px;font-size:0.9em;">
          Check that:<br>
          1. XAMPP MySQL is running (green in XAMPP control panel)<br>
          2. DB_USER and DB_PASS are correct in connection.php<br>
          3. Database "casa_de_manila" exists in phpMyAdmin
        </p>
      </div>
    ');
}

$mysqli->set_charset(DB_CHARSET);