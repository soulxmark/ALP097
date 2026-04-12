<?php
// =============================================
// session_check.php
// Include at the top of every protected page.
// Auto-logs out after 15 minutes of inactivity.
// =============================================

$TIMEOUT_MINUTES = 15;
$TIMEOUT_SECONDS = $TIMEOUT_MINUTES * 60;

if (isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1) {
    // Check last activity
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > $TIMEOUT_SECONDS) {
            // Session expired — destroy and redirect
            session_unset();
            session_destroy();
            header('Location: login.php?timeout=1');
            exit;
        }
    }
    // Update last activity timestamp
    $_SESSION['last_activity'] = time();
}  