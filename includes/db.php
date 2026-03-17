<?php
/**
 * includes/db.php
 * ─────────────────────────────────────────────────────────────
 * Creates a PDO database connection that every PHP file uses.
 *
 * HOW TO USE:
 *   require_once __DIR__ . '/../includes/db.php';
 *   // $pdo is now available as a PDO object
 *
 * SETUP:
 *   1. Fill in your cPanel hosting details below.
 *   2. The database name, user, and password are found in
 *      cPanel → MySQL Databases.
 * ─────────────────────────────────────────────────────────────
 */

// ── Database credentials ──────────────────────────────────────
//
//  XAMPP LOCAL (default — works out of the box):
//    DB_USER = 'root'
//    DB_PASS = ''         ← empty string, no password
//
//  LIVE HOSTING (cPanel):
//    DB_USER = 'your_cpanel_db_username'
//    DB_PASS = 'your_cpanel_db_password'
//
define('DB_HOST', 'localhost');   // Same for both XAMPP and cPanel
define('DB_NAME', 'primepath_db');// Create this database in phpMyAdmin first
define('DB_USER', 'root');        // XAMPP default → change when deploying live
define('DB_PASS', '');            // XAMPP default (empty) → add password for live
define('DB_CHARSET', 'utf8mb4'); // Full Unicode support (emoji-safe)

// ── DSN (Data Source Name) ────────────────────────────────────
$dsn = "mysql:host=" . DB_HOST
     . ";dbname=" . DB_NAME
     . ";charset=" . DB_CHARSET;

// ── PDO options ───────────────────────────────────────────────
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return rows as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

// ── Connect ───────────────────────────────────────────────────
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log the real error to a file — never show it to visitors
    error_log('[DB ERROR] ' . $e->getMessage());

    // Return a safe JSON error if called from an API endpoint
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed. Please try again later.']));
}
