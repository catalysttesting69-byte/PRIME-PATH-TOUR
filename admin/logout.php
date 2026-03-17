<?php
/**
 * admin/logout.php
 * ─────────────────────────────────────────────────────────────
 * Destroys the admin session and redirects to the login page.
 * Linked from the "Logout" button in the dashboard sidebar.
 * ─────────────────────────────────────────────────────────────
 */
session_start();

// Load config so BASE_URL redirect works in subfolders (e.g. /primepath/)
require_once __DIR__ . '/../includes/config.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie as well
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session on the server
session_destroy();

// Redirect to login page — BASE_URL handles both /primepath/ and root
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
