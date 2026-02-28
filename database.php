<?php
/* ============================================================
   Casa De Manila — Database Connection
   File: connection.php
============================================================ */

define('DB_HOST',    'localhost');
define('DB_USER',    'root');        // ← change to your DB username
define('DB_PASS',    '');            // ← change to your DB password
define('DB_NAME',    'casa_de_manila');
define('DB_CHARSET', 'utf8mb4');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    error_log('DB connection failed: ' . $mysqli->connect_error);
    http_response_code(503);
    die('
      <div style="font-family:Cormorant Garamond,serif;text-align:center;
                  padding:80px 20px;background:#111;color:#fff;min-height:100vh;">
        <h1 style="color:#d4af37;font-size:2.5em;">Casa De Manila</h1>
        <p style="margin-top:20px;font-size:1.2em;color:#ccc;">
          Unable to connect to database.<br>Please try again shortly.
        </p>
      </div>
    ');
}

$mysqli->set_charset(DB_CHARSET);