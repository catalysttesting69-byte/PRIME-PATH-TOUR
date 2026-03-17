<?php
/**
 * api/subscribe.php
 * ─────────────────────────────────────────────────────────────
 * Handles newsletter subscription form submissions.
 *
 * WHAT THIS FILE DOES:
 *   1. Validates the submitted email address
 *   2. Saves it to the `subscribers` table
 *   3. If the email already exists, returns a friendly message
 *   4. Returns a JSON response
 *
 * CALLED BY:
 *   The newsletter form in index.html (footer) via fetch() / AJAX
 *
 * ACCEPTS: POST request with JSON body or form data
 * RETURNS: JSON { success: bool, message: string }
 * ─────────────────────────────────────────────────────────────
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// ── Only allow POST requests ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Load database connection ──────────────────────────────────
require_once __DIR__ . '/../includes/db.php';

// ── Read JSON body or fall back to $_POST ────────────────────
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (empty($data)) {
    $data = $_POST;
}

// ── Sanitize and validate email ───────────────────────────────
$email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email address is required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// ── Insert into `subscribers` table ──────────────────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO subscribers (email)
        VALUES (:email)
    ");
    $stmt->execute([':email' => $email]);

} catch (PDOException $e) {
    // MySQL error code 23000 = Duplicate entry (UNIQUE constraint)
    // This happens when the email is already subscribed
    if ($e->getCode() === '23000') {
        echo json_encode([
            'success' => true, // Still show success to avoid email enumeration
            'message' => 'Great news — you\'re already subscribed! 🎉',
        ]);
        exit;
    }

    // Any other database error
    error_log('[SUBSCRIBE ERROR] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not subscribe. Please try again.']);
    exit;
}

// ── Return success ────────────────────────────────────────────
echo json_encode([
    'success' => true,
    'message' => 'You\'re subscribed! 🌍 Get ready for Tanzania travel inspiration.',
]);
