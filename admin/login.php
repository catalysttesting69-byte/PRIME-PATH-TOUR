<?php
/**
 * admin/login.php
 * ─────────────────────────────────────────────────────────────
 * Hidden admin login page — only the business owner uses this.
 * URL: yourdomain.com/admin/login.php
 *
 * WHAT THIS FILE DOES:
 *   GET  request → Shows the login form
 *   POST request → Verifies credentials and starts a session
 *
 * SECURITY:
 *   - Passwords compared using password_verify() against bcrypt hash
 *   - Session regenerated after login to prevent session fixation
 *   - Failed login shows generic error (doesn't reveal if email exists)
 * ─────────────────────────────────────────────────────────────
 */

// Start session at the very top
session_start();

// Load config FIRST — defines BASE_URL before it's used below
require_once __DIR__ . '/../includes/config.php';

// If admin is already logged in, redirect to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

// Load database connection
require_once __DIR__ . '/../includes/db.php';


$error = ''; // Stores login error message

// ── Handle POST (form submission) ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Read and sanitize inputs
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? ''; // Don't sanitize password before verification

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Look up the admin by email
        $stmt = $pdo->prepare("SELECT id, name, password_hash FROM users WHERE email = :email AND role = 'admin' LIMIT 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // ── Login successful ──────────────────────────────
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Store admin info in the session
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            // Redirect to the dashboard — BASE_URL handles /primepath/ subfolder
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;

        } else {
            // Generic error — don't reveal which part was wrong
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — PrimePath Tours</title>
    <meta name="robots" content="noindex, nofollow"> <!-- Hide from search engines -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-dark: #1a3c2e;
            --green:      #2d6a4f;
            --gold:       #c8a96e;
            --white:      #ffffff;
            --bg:         #0f1f17;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: radial-gradient(ellipse at 20% 50%, rgba(45,106,79,0.15) 0%, transparent 60%),
                              radial-gradient(ellipse at 80% 20%, rgba(200,169,110,0.08) 0%, transparent 50%);
        }

        .login-card {
            background: rgba(26,60,46,0.6);
            border: 1px solid rgba(200,169,110,0.2);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo span {
            display: inline-block;
            background: var(--gold);
            color: var(--green-dark);
            width: 56px; height: 56px;
            border-radius: 14px;
            font-size: 24px;
            line-height: 56px;
        }
        .login-logo h1 {
            color: var(--white);
            font-size: 22px;
            font-weight: 700;
            margin-top: 14px;
        }
        .login-logo p {
            color: rgba(255,255,255,0.5);
            font-size: 13px;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            font-size: 15px;
        }
        .form-group input {
            width: 100%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: var(--white);
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            padding: 13px 14px 13px 42px;
            transition: border-color 0.2s, background 0.2s;
            outline: none;
        }
        .form-group input:focus {
            border-color: var(--gold);
            background: rgba(200,169,110,0.08);
        }

        .error-box {
            background: rgba(220,53,69,0.15);
            border: 1px solid rgba(220,53,69,0.4);
            border-radius: 8px;
            color: #ff8fa3;
            font-size: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-login {
            width: 100%;
            background: var(--gold);
            color: var(--green-dark);
            border: none;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            padding: 14px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 4px;
        }
        .btn-login:hover { background: #dbb97e; }
        .btn-login:active { transform: scale(0.98); }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        .back-link a {
            color: rgba(255,255,255,0.4);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link a:hover { color: var(--gold); }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <span><i class="fas fa-shield-halved"></i></span>
        <h1>Admin Portal</h1>
        <p>PrimePath Tours &amp; Safaris</p>
    </div>

    <?php if ($error): ?>
        <div class="error-box">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action=""><!-- empty action = post to same URL, works in any subfolder -->
        <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="admin@example.com"
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="username"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Sign In to Dashboard
        </button>
    </form>

    <div class="back-link">
        <a href="../index.html"><i class="fas fa-arrow-left"></i> Back to website</a>
    </div>
</div>

</body>
</html>
