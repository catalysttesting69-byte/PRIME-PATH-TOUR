<?php
/**
 * includes/auth.php
 * ─────────────────────────────────────────────────────────────
 * Session guard — include this at the TOP of every admin page.
 * If no valid admin session exists, the visitor is immediately
 * redirected to the login page.
 *
 * HOW TO USE (at the very top of any admin PHP file):
 *   require_once __DIR__ . '/../includes/auth.php';
 * ─────────────────────────────────────────────────────────────
 */

// Start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config to get BASE_URL (works in subfolder or root)
require_once __DIR__ . '/config.php';

// Check that the admin is logged in
// $_SESSION['admin_id'] is set by login.php after successful authentication
if (empty($_SESSION['admin_id'])) {
    // Not logged in — redirect to the login page
    // BASE_URL handles subfolder: e.g. /primepath/admin/login.php
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit; // Always exit after a redirect to stop further code execution
}

/**
 * Helper: returns the logged-in admin's name (for display in dashboard)
 */
function getAdminName(): string {
    return htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
}
