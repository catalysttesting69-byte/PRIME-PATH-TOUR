<?php
/**
 * test.php  — XAMPP Diagnostic Page
 * ─────────────────────────────────────────────────────────────
 * Visit http://localhost/primepath/test.php in your browser.
 * This runs all checks and shows you exactly what's working.
 *
 * DELETE THIS FILE before deploying live — it exposes internals.
 * ─────────────────────────────────────────────────────────────
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>XAMPP Diagnostic — PrimePath</title>
<style>
  body    { font-family: 'Segoe UI', sans-serif; background:#f0f4f2; color:#1a2e24; margin:0; padding:30px; }
  h1      { color:#1a3c2e; font-size:24px; margin-bottom:6px; }
  .sub    { color:#6b7c74; margin-bottom:28px; }
  .card   { background:#fff; border-radius:14px; padding:24px 28px; margin-bottom:18px;
            box-shadow:0 2px 16px rgba(0,0,0,0.06); }
  .card h2{ font-size:16px; margin:0 0 16px; color:#1a3c2e; display:flex; align-items:center; gap:8px; }
  .row    { display:flex; align-items:center; gap:12px; padding:8px 0;
            border-bottom:1px solid #e8f0ec; font-size:14px; }
  .row:last-child { border-bottom:none; }
  .label  { color:#6b7c74; min-width:200px; }
  .ok     { color:#2d6a4f; font-weight:700; }
  .fail   { color:#dc3545; font-weight:700; }
  .warn   { color:#e07700; font-weight:700; }
  .badge  { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
  .badge-ok   { background:#d1e7dd; color:#0f5132; }
  .badge-fail { background:#f8d7da; color:#842029; }
  .badge-warn { background:#fff3cd; color:#856404; }
  .note   { background:#f0f4f2; border-radius:8px; padding:10px 14px; font-size:13px;
            color:#3a5a4a; margin-top:10px; }
  .action { background:#1a3c2e; color:#fff; border-radius:8px; padding:10px 16px;
            font-size:13px; margin-top:10px; font-family:monospace; word-break:break-all; }
  .links  { display:flex; flex-wrap:wrap; gap:10px; margin-top:8px; }
  .links a{ background:#2d6a4f; color:#fff; text-decoration:none; border-radius:8px;
            padding:9px 16px; font-size:13px; font-weight:600; }
  .links a:hover { background:#1a3c2e; }
</style>
</head>
<body>

<h1>🔍 PrimePath XAMPP Diagnostic</h1>
<p class="sub">This page checks every component of your local setup. Visit it at <strong>http://localhost/primepath/test.php</strong></p>

<?php

/* ──────────────────────────────────────────────
   1.  PHP VERSION
────────────────────────────────────────────── */
echo '<div class="card"><h2>⚙️ PHP &amp; Server</h2>';

$phpOk = version_compare(PHP_VERSION, '7.4', '>=');
echo '<div class="row"><span class="label">PHP Version</span>
      <span class="' . ($phpOk ? 'ok' : 'fail') . '">' . PHP_VERSION . '</span>
      <span class="badge badge-' . ($phpOk ? 'ok' : 'fail') . '">' . ($phpOk ? 'OK' : 'Needs 7.4+') . '</span></div>';

echo '<div class="row"><span class="label">Server Software</span>
      <span>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</span></div>';

echo '<div class="row"><span class="label">Document Root</span>
      <span>' . htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . '</span></div>';

echo '<div class="row"><span class="label">Request URL</span>
      <span>http://' . htmlspecialchars($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '</span></div>';

echo '</div>';

/* ──────────────────────────────────────────────
   2.  PHP EXTENSIONS
────────────────────────────────────────────── */
echo '<div class="card"><h2>🧩 Required PHP Extensions</h2>';

$extensions = [
    'pdo'        => 'PDO (database abstraction)',
    'pdo_mysql'  => 'PDO MySQL driver',
    'json'       => 'JSON (API responses)',
    'mbstring'   => 'Multibyte String',
    'openssl'    => 'OpenSSL (SMTP TLS email)',
    'curl'       => 'cURL (optional, for email)',
];

foreach ($extensions as $ext => $label) {
    $loaded = extension_loaded($ext);
    echo '<div class="row"><span class="label">' . $label . '</span>
          <span class="badge badge-' . ($loaded ? 'ok' : 'fail') . '">' . ($loaded ? '✓ Loaded' : '✗ Missing') . '</span></div>';
}

echo '</div>';

/* ──────────────────────────────────────────────
   3.  DATABASE CONNECTION
────────────────────────────────────────────── */
echo '<div class="card"><h2>🗄️ Database Connection</h2>';

$dbOk = false;
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=primepath_db;charset=utf8mb4',
        'root', '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo '<div class="row"><span class="label">Connection to primepath_db</span>
          <span class="badge badge-ok">✓ Connected</span></div>';
    $dbOk = true;

    // Check tables
    $tables      = ['bookings', 'clients', 'subscribers', 'users', 'tours'];
    $foundTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $tbl) {
        $exists = in_array($tbl, $foundTables, true);
        echo '<div class="row"><span class="label">Table: <code>' . $tbl . '</code></span>
              <span class="badge badge-' . ($exists ? 'ok' : 'fail') . '">' . ($exists ? '✓ Exists' : '✗ Missing') . '</span></div>';
    }

    // Check admin user exists
    if (in_array('users', $foundTables, true)) {
        $count = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
        echo '<div class="row"><span class="label">Admin user record</span>
              <span class="badge badge-' . ($count > 0 ? 'ok' : 'fail') . '">'
              . ($count > 0 ? "✓ Found ($count admin)" : '✗ None — run schema.sql') . '</span></div>';
    }

} catch (PDOException $e) {
    echo '<div class="row"><span class="label">Connection to primepath_db</span>
          <span class="badge badge-fail">✗ Failed</span></div>';
    echo '<div class="note">Error: ' . htmlspecialchars($e->getMessage()) . '<br><br>
          Make sure:<br>
          • MySQL is <strong>green</strong> in XAMPP Control Panel<br>
          • Database name is exactly <code>primepath_db</code><br>
          • You ran <code>database/schema.sql</code> in phpMyAdmin</div>';
}
echo '</div>';

/* ──────────────────────────────────────────────
   4.  FILE STRUCTURE
────────────────────────────────────────────── */
echo '<div class="card"><h2>📁 File Structure Check</h2>';

// Derive the project root from this file's location
$root = __DIR__;

$files = [
    'includes/db.php'           => 'Database connection',
    'includes/auth.php'         => 'Admin session guard',
    'includes/mailer.php'       => 'Email helper',
    'includes/admin_layout.php' => 'Admin sidebar layout',
    'includes/admin_footer.php' => 'Admin footer',
    'api/book.php'              => 'Booking API endpoint',
    'api/subscribe.php'         => 'Subscribe API endpoint',
    'admin/login.php'           => 'Admin login page',
    'admin/dashboard.php'       => 'Admin dashboard',
    'admin/bookings.php'        => 'Bookings manager',
    'admin/clients.php'         => 'Clients CRM',
    'admin/subscribers.php'     => 'Subscribers list',
    'admin/tours.php'           => 'Tour packages',
    'admin/logout.php'          => 'Admin logout',
    'contact.html'              => 'Booking form page',
    'database/schema.sql'       => 'DB schema file',
];

foreach ($files as $rel => $label) {
    $exists = file_exists($root . '/' . $rel);
    echo '<div class="row"><span class="label"><code>' . $rel . '</code></span>
          <span style="flex:1;color:#6b7c74;font-size:13px">' . $label . '</span>
          <span class="badge badge-' . ($exists ? 'ok' : 'fail') . '">' . ($exists ? '✓ Found' : '✗ Missing') . '</span></div>';
}

echo '</div>';

/* ──────────────────────────────────────────────
   5.  PHPMAILER CHECK
────────────────────────────────────────────── */
echo '<div class="card"><h2>📧 PHPMailer (Email)</h2>';

$vendorPath = $root . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    echo '<div class="row"><span class="label">PHPMailer / Composer vendor</span>
          <span class="badge badge-ok">✓ Installed</span></div>';
    echo '<div class="note">Email notifications are ready. Fill in your Gmail credentials in <code>includes/mailer.php</code>.</div>';
} else {
    echo '<div class="row"><span class="label">PHPMailer / Composer vendor</span>
          <span class="badge badge-warn">⚠ Not installed yet</span></div>';
    echo '<div class="note">
          <strong>Bookings still save to the database</strong> — email is just skipped for now.<br><br>
          To enable email, open PowerShell in your project folder and run:<br>
          <div class="action">composer require phpmailer/phpmailer</div>
          (Composer must be installed first from getcomposer.org)
        </div>';
}

echo '</div>';

/* ──────────────────────────────────────────────
   6.  QUICK TEST — SUBMIT A BOOKING
────────────────────────────────────────────── */
echo '<div class="card"><h2>🧪 Quick API Test</h2>';
echo '<p style="font-size:14px;color:#6b7c74;margin:0 0 12px">Click the button below to submit a test booking via the API. 
      Check the database afterwards to confirm it was saved.</p>';
echo '
<button id="testBtn" onclick="runTest()" style="background:#1a3c2e;color:#fff;border:none;border-radius:8px;
  padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;">
  Run Test Booking Now
</button>
<div id="testResult" style="margin-top:14px;font-size:14px;"></div>

<script>
async function runTest() {
  const btn = document.getElementById("testBtn");
  const res = document.getElementById("testResult");
  btn.disabled = true;
  btn.textContent = "Testing…";
  res.innerHTML = "";

  try {
    const r = await fetch("/primepath/api/book.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        name: "Test User",
        email: "test@primepath-diagnostic.com",
        phone: "+255 000 000 000",
        tour_name: "Safari + Zanzibar",
        travel_date: "2026-06-01",
        num_people: 2,
        message: "This is an automated diagnostic test booking."
      })
    });
    const data = await r.json();

    if (data.success) {
      res.innerHTML = \'<div style="background:#d1e7dd;color:#0f5132;border-radius:8px;padding:12px 16px;">\' +
        \'<strong>✅ API responded with SUCCESS!</strong><br><small>\' + data.message + \'</small></div>\' +
        \'<p style="font-size:13px;margin-top:10px;color:#6b7c74;">Now check phpMyAdmin → primepath_db → bookings table to see the test row.</p>\';
    } else {
      res.innerHTML = \'<div style="background:#f8d7da;color:#842029;border-radius:8px;padding:12px 16px;">\' +
        \'<strong>❌ API returned an error:</strong><br>\' + data.message + \'</div>\';
    }
  } catch (e) {
    res.innerHTML = \'<div style="background:#f8d7da;color:#842029;border-radius:8px;padding:12px 16px;">\' +
      \'<strong>❌ Network error — PHP might have crashed.</strong><br>\' +
      \'Check C:\\\\xampp\\\\apache\\\\logs\\\\error.log for details.<br>Error: \' + e.message + \'</div>\';
  }
  btn.disabled = false;
  btn.textContent = "Run Test Booking Now";
}
</script>';
echo '</div>';

/* ──────────────────────────────────────────────
   7.  QUICK LINKS
────────────────────────────────────────────── */
echo '<div class="card"><h2>🔗 Quick Links</h2>';
echo '<div class="links">';
echo '<a href="/primepath/index.html" target="_blank">🏠 Homepage</a>';
echo '<a href="/primepath/contact.html" target="_blank">📋 Booking Form</a>';
echo '<a href="/primepath/admin/login.php" target="_blank">🔐 Admin Login</a>';
echo '<a href="http://localhost/phpmyadmin" target="_blank">🗄️ phpMyAdmin</a>';
echo '</div>';
echo '<p style="font-size:12px;color:#dc3545;margin-top:16px;font-weight:600;">
      ⚠️ DELETE this test.php file before uploading to your live server!</p>';
echo '</div>';

?>
</body>
</html>
