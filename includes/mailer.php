<?php
/**
 * includes/mailer.php
 * ─────────────────────────────────────────────────────────────
 * PHPMailer SMTP wrapper — XAMPP-safe version.
 *
 * This version will NOT crash if PHPMailer isn't installed yet.
 * It checks first, and if PHPMailer is missing, it gracefully
 * skips email sending (the booking still saves to the database).
 *
 * SETUP FOR EMAIL:
 *   1. Open PowerShell, cd into your project folder, run:
 *        composer require phpmailer/phpmailer
 *   2. Fill in your Gmail credentials below.
 *   3. Use a Gmail App Password — NOT your regular Gmail password.
 *      Get one at: myaccount.google.com → Security → App Passwords
 * ─────────────────────────────────────────────────────────────
 */

// ── SMTP Credentials — fill these in ─────────────────────────
// Leave them as-is while testing locally; email will be skipped
// gracefully if PHPMailer isn't installed yet.
define('MAIL_HOST',      'smtp.gmail.com');          // Gmail SMTP host
define('MAIL_PORT',      587);                        // 587 = TLS (recommended)
define('MAIL_USERNAME',  'your@gmail.com');           // ← your Gmail address
define('MAIL_PASSWORD',  'your_app_password_here');   // ← 16-char Gmail App Password
define('MAIL_FROM',      'your@gmail.com');           // Sender address
define('MAIL_FROM_NAME', 'PrimePath Tours & Safaris');// Sender display name
define('ADMIN_EMAIL',    'your@gmail.com');           // ← where booking alerts go

// ── Check if PHPMailer is installed ──────────────────────────
// PHPMailer is installed by running: composer require phpmailer/phpmailer
// It creates a vendor/ folder in your project root.
$_vendorAutoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($_vendorAutoload)) {
    // PHPMailer IS installed — load it
    require_once $_vendorAutoload;
    define('PHPMAILER_AVAILABLE', true);
} else {
    // PHPMailer NOT installed yet — define a stub so the app
    // doesn't crash. Booking still saves; email is just skipped.
    define('PHPMAILER_AVAILABLE', false);
}

/**
 * sendAdminNotification()
 * ─────────────────────────────────────────────────────────────
 * Sends an HTML email to the admin's inbox using PHPMailer.
 * If PHPMailer isn't installed, returns a graceful failure
 * instead of crashing the booking form.
 *
 * @param  string $subject   Email subject line
 * @param  string $htmlBody  Full HTML body of the email
 * @return array  ['success' => bool, 'error' => string|null]
 */
function sendAdminNotification(string $subject, string $htmlBody): array {

    // Skip gracefully if PHPMailer hasn't been installed yet
    if (!PHPMAILER_AVAILABLE) {
        return [
            'success' => false,
            'error'   => 'PHPMailer not installed. Run: composer require phpmailer/phpmailer',
        ];
    }

    // Only reference PHPMailer classes if the library is loaded
    $mailerClass    = 'PHPMailer\\PHPMailer\\PHPMailer';
    $exceptionClass = 'PHPMailer\\PHPMailer\\Exception';

    $mail = new $mailerClass(true); // true = throw exceptions on errors

    try {
        // ── Server / connection settings ──────────────────────
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = 'tls';   // Use TLS (port 587)
        $mail->Port       = MAIL_PORT;

        // ── Sender & recipient ────────────────────────────────
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL, 'Admin');

        // ── Email content ─────────────────────────────────────
        $mail->isHTML(true);
        $mail->CharSet  = 'UTF-8';
        $mail->Subject  = $subject;
        $mail->Body     = $htmlBody;
        $mail->AltBody  = strip_tags(str_replace(['<br>', '<br/>'], "\n", $htmlBody));

        $mail->send();
        return ['success' => true, 'error' => null];

    } catch (\Exception $e) {
        error_log('[MAILER ERROR] ' . $mail->ErrorInfo);
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}
