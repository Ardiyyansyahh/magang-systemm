<?php
// Secure logout script
session_start();

// Log logout activity
if (isset($_SESSION['user_id'])) {
    error_log("User logout - ID: " . $_SESSION['user_id'] . ", Role: " . ($_SESSION['role'] ?? 'unknown'));
}

// Unset all session variables
$_SESSION = array();

// Delete session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear any other cookies that might exist
setcookie('remember_me', '', time() - 3600, '/');

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login
header("Location: login.html?logout=1");
exit;
?>