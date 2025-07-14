<?php
// File untuk menangani session management yang lebih baik
// session_helper.php

function initializeSession()
{
    if (session_status() == PHP_SESSION_NONE) {
        // Konfigurasi session yang lebih aman
        ini_set('session.gc_maxlifetime', 3600); // 1 jam
        ini_set('session.cookie_lifetime', 3600); // 1 jam
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0); // Set ke 1 jika menggunakan HTTPS

        session_set_cookie_params([
            'lifetime' => 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => false, // Set ke true jika menggunakan HTTPS
            'samesite' => 'Lax'
        ]);

        session_start();
    }
}

function checkSessionTimeout()
{
    $timeout = 3600; // 1 jam dalam detik

    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $timeout) {
            // Session expired
            session_unset();
            session_destroy();
            return false;
        }
    }

    // Update last activity
    $_SESSION['last_activity'] = time();
    return true;
}

function validateUserSession($required_role = null)
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return false;
    }

    if ($required_role && $_SESSION['role'] !== $required_role) {
        return false;
    }

    return checkSessionTimeout();
}

function redirectToLogin($message = '')
{
    $redirect_url = '../login.html';
    if ($message) {
        $redirect_url .= '?error=' . urlencode($message);
    }
    header("Location: $redirect_url");
    exit;
}

function logActivity($action, $details = '')
{
    $log_message = date('Y-m-d H:i:s') . " - User ID: " . ($_SESSION['user_id'] ?? 'unknown') .
        " - Role: " . ($_SESSION['role'] ?? 'unknown') .
        " - Action: $action";
    if ($details) {
        $log_message .= " - Details: $details";
    }
    error_log($log_message);
}
?>