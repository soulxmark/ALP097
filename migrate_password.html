<?php
/* ============================================================
   Casa De Manila — Password Migration Script
   File: migrate_passwords.php
   RUN ONCE then DELETE this file from the server!
============================================================ */

// Basic protection
if (php_sapi_name() !== 'cli') {
    $secret = $_GET['secret'] ?? '';
    if ($secret !== 'CHANGE_THIS_SECRET_KEY') {
        http_response_code(403);
        die('403 Forbidden. Add ?secret=CHANGE_THIS_SECRET_KEY to URL. DELETE this file after use.');
    }
}

require_once 'connection.php';

echo "=== Casa De Manila: Password Migration ===\n\n";

$result   = $mysqli->query("SELECT uid, username, password_us FROM users_tbl1");
$total    = 0;
$migrated = 0;
$skipped  = 0;

while ($row = $result->fetch_assoc()) {
    $total++;
    $uid  = $row['uid'];
    $name = $row['username'];
    $pw   = $row['password_us'];

    // Skip if already hashed
    if (password_get_info($pw)['algo'] !== 0) {
        echo "  [SKIP]   uid=$uid ($name) — already hashed\n";
        $skipped++;
        continue;
    }

    $hashed = password_hash($pw, PASSWORD_DEFAULT);
    $stmt   = $mysqli->prepare("UPDATE users_tbl1 SET password_us = ? WHERE uid = ?");
    $stmt->bind_param("si", $hashed, $uid);

    if ($stmt->execute()) {
        echo "  [HASHED] uid=$uid ($name) — secured\n";
        $migrated++;
    } else {
        echo "  [ERROR]  uid=$uid ($name) — " . $stmt->error . "\n";
    }
    $stmt->close();
}

echo "\n=== Done ===\n";
echo "  Total   : $total\n";
echo "  Hashed  : $migrated\n";
echo "  Skipped : $skipped\n";
echo "\n!! DELETE this file from your server now !!\n";