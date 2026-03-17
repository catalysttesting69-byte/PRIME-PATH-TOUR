<?php
/**
 * api/book.php
 * ─────────────────────────────────────────────────────────────
 * Handles tour booking form submissions from the website.
 *
 * WHAT THIS FILE DOES:
 *   1. Validates and sanitizes the submitted form data
 *   2. Saves the booking to the `bookings` table
 *   3. Auto-saves or updates the visitor's details in `clients`
 *      (this is how the admin builds a CRM over time)
 *   4. Sends an email notification to the admin via PHPMailer
 *   5. Returns a JSON response (success or error)
 *
 * CALLED BY:
 *   The booking form in contact.html via fetch() / AJAX
 *
 * ACCEPTS: POST request with JSON body
 * RETURNS: JSON { success: bool, message: string }
 * ─────────────────────────────────────────────────────────────
 */

// ── Allow cross-origin requests from your own domain ─────────
// (Needed if your HTML and PHP are on the same server — safe to keep)
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

// ── Load mailer ───────────────────────────────────────────────
require_once __DIR__ . '/../includes/mailer.php';

// ── Read JSON body from the fetch() call ─────────────────────
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

// Fallback to $_POST if form submits as regular form data
if (empty($data)) {
    $data = $_POST;
}

// ── Sanitize all inputs ───────────────────────────────────────
// htmlspecialchars() prevents XSS. trim() removes whitespace.
// PDO prepared statements below prevent SQL injection.
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$name        = sanitize($data['name']        ?? '');
$email       = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone       = sanitize($data['phone']       ?? '');
$tour_name   = sanitize($data['tour_name']   ?? '');
$travel_date = sanitize($data['travel_date'] ?? '');  // Expected: YYYY-MM-DD
$num_people  = (int) ($data['num_people']    ?? 1);
$message     = sanitize($data['message']     ?? '');

// ── Validate required fields ──────────────────────────────────
if (empty($name) || empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

// ── Validate travel_date (allow empty) ───────────────────────
$travel_date_sql = null; // NULL is stored if no date provided
if (!empty($travel_date)) {
    $d = DateTime::createFromFormat('Y-m-d', $travel_date);
    if ($d && $d->format('Y-m-d') === $travel_date) {
        $travel_date_sql = $travel_date;
    }
}

// ── Insert booking into the `bookings` table ──────────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO bookings (name, email, phone, tour_name, travel_date, num_people, message)
        VALUES (:name, :email, :phone, :tour_name, :travel_date, :num_people, :message)
    ");

    $stmt->execute([
        ':name'        => $name,
        ':email'       => $email,
        ':phone'       => $phone,
        ':tour_name'   => $tour_name,
        ':travel_date' => $travel_date_sql,
        ':num_people'  => $num_people,
        ':message'     => $message,
    ]);

    $bookingId = $pdo->lastInsertId(); // Used in the email notification

} catch (PDOException $e) {
    error_log('[BOOKING INSERT ERROR] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save booking. Please try again.']);
    exit;
}

// ── Upsert (insert or update) the `clients` table ────────────
// "Upsert" = if this email already exists, update the record;
// otherwise, create a new client record.
// This is how the admin builds a client database over time.
try {
    $stmt = $pdo->prepare("
        INSERT INTO clients (name, email, phone, total_bookings)
        VALUES (:name, :email, :phone, 1)
        ON DUPLICATE KEY UPDATE
            name           = VALUES(name),
            phone          = IF(VALUES(phone) != '', VALUES(phone), phone),
            total_bookings = total_bookings + 1,
            last_booking   = CURRENT_TIMESTAMP
    ");

    $stmt->execute([
        ':name'  => $name,
        ':email' => $email,
        ':phone' => $phone,
    ]);

} catch (PDOException $e) {
    // Non-critical: log but don't fail the request
    error_log('[CLIENT UPSERT ERROR] ' . $e->getMessage());
}

// ── Send email notification to admin ─────────────────────────
$emailSubject = "New Booking Request #{$bookingId} — {$name}";

$emailBody = "
<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; color: #333; }
    .header { background: #1a3c2e; color: #fff; padding: 20px; border-radius: 8px 8px 0 0; }
    .body   { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
    .row    { margin-bottom: 12px; }
    .label  { font-weight: bold; color: #555; font-size: 12px; text-transform: uppercase; }
    .value  { font-size: 16px; color: #222; margin-top: 2px; }
    .footer { font-size: 12px; color: #999; margin-top: 20px; }
  </style>
</head>
<body>
  <div class='header'>
    <h2 style='margin:0'>🌍 New Booking Request</h2>
    <p style='margin:4px 0 0'>PrimePath Tours &amp; Safaris — Booking #{$bookingId}</p>
  </div>
  <div class='body'>
    <div class='row'><div class='label'>Full Name</div><div class='value'>{$name}</div></div>
    <div class='row'><div class='label'>Email</div><div class='value'><a href='mailto:{$email}'>{$email}</a></div></div>
    <div class='row'><div class='label'>Phone / WhatsApp</div><div class='value'>" . ($phone ?: 'Not provided') . "</div></div>
    <div class='row'><div class='label'>Tour / Trip Type</div><div class='value'>" . ($tour_name ?: 'Not specified') . "</div></div>
    <div class='row'><div class='label'>Travel Date</div><div class='value'>" . ($travel_date ?: 'Flexible') . "</div></div>
    <div class='row'><div class='label'>Number of People</div><div class='value'>{$num_people}</div></div>
    <div class='row'><div class='label'>Message</div><div class='value'>" . nl2br($message ?: 'No message provided') . "</div></div>
    <p style='margin-top:20px'>
      <a href='/admin/bookings.php' 
         style='background:#c8a96e;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none;font-weight:bold'>
        View in Admin Dashboard →
      </a>
    </p>
    <p class='footer'>This notification was automatically sent by PrimePath Tours &amp; Safaris booking system.</p>
  </div>
</body>
</html>
";

// Send the email — if it fails, log the error but still return success
// (the booking was already saved to the database)
$mailResult = sendAdminNotification($emailSubject, $emailBody);
if (!$mailResult['success']) {
    error_log('[BOOKING EMAIL FAILED] Booking #' . $bookingId . ' — ' . $mailResult['error']);
}

// ── All done — return success ─────────────────────────────────
echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your enquiry has been received. We\'ll be in touch within 24 hours.',
]);
