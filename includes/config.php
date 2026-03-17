<?php
/**
 * includes/config.php
 * ─────────────────────────────────────────────────────────────
 * Auto-detects the project's base URL so all redirects and links
 * work whether the site is:
 *   - In a subfolder:  http://localhost/primepath/
 *   - At the root:     http://yourdomain.com/
 *
 * USAGE: included by auth.php, login.php, logout.php
 *   require_once __DIR__ . '/config.php';
 *   header('Location: ' . BASE_URL . '/admin/login.php');
 * ─────────────────────────────────────────────────────────────
 */

if (!defined('BASE_URL')) {

    // SCRIPT_NAME is the path of the currently executing PHP file.
    // Example on XAMPP:   /primepath/admin/dashboard.php
    // Example on live:    /admin/dashboard.php
    //
    // All admin & api files are exactly 2 levels deep from project root
    // (project/admin/file.php, project/api/file.php).
    // So going up 2 directories from SCRIPT_NAME gives us the project root.
    //
    // dirname('/primepath/admin/dashboard.php') → '/primepath/admin'
    // dirname('/primepath/admin')               → '/primepath'
    // Result: BASE_URL = '/primepath'           ← correct for XAMPP subfolder
    //
    // dirname('/admin/dashboard.php')           → '/admin'
    // dirname('/admin')                         → '/'
    // rtrim('/', '/')                           → ''
    // Result: BASE_URL = ''                     ← correct for live root deployment

    $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $twoUp      = dirname(dirname($scriptPath));
    $base       = rtrim($twoUp, '/');

    // If included from includes/ (1 level deep), go up 2 levels from there
    // But since auth.php is always required FROM an admin page (2 levels deep),
    // SCRIPT_NAME will always be the admin page's path — so this stays correct.

    define('BASE_URL', $base === '.' ? '' : $base);
}
