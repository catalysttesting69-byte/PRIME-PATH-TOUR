<?php
/**
 * includes/admin_layout.php
 * ─────────────────────────────────────────────────────────────
 * Shared HTML layout (sidebar + header) for all admin pages.
 *
 * HOW TO USE:
 *   At the top of any admin page (after auth.php):
 *     $pageTitle = 'Bookings';
 *     $activePage = 'bookings';
 *     require_once __DIR__ . '/../includes/admin_layout.php';
 *   Then write your <main> content.
 *   At the bottom of every admin page:
 *     require_once __DIR__ . '/../includes/admin_footer.php';
 * ─────────────────────────────────────────────────────────────
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?> — PrimePath Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w:   240px;
            --green-dark:  #1a3c2e;
            --green:       #2d6a4f;
            --green-light: #3a8a65;
            --gold:        #c8a96e;
            --gold-light:  #e8c98e;
            --white:       #ffffff;
            --bg:          #f0f4f2;
            --card-bg:     #ffffff;
            --text:        #1a2e24;
            --text-muted:  #6b7c74;
            --border:      #dce8e2;
            --danger:      #dc3545;
            --success:     #28a745;
            --warning:     #ffc107;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* ─── SIDEBAR ─── */
        .adm-sidebar {
            width: var(--sidebar-w);
            background: var(--green-dark);
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow-y: auto;
        }

        .adm-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .adm-brand h2 {
            color: var(--gold);
            font-size: 17px;
            font-weight: 700;
            line-height: 1.3;
        }
        .adm-brand p {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .adm-nav {
            padding: 16px 12px;
            flex: 1;
        }
        .adm-nav-label {
            color: rgba(255,255,255,0.25);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 10px 8px;
            display: block;
        }
        .adm-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 11px 12px;
            border-radius: 8px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }
        .adm-nav a i { width: 18px; font-size: 15px; }
        .adm-nav a:hover {
            background: rgba(255,255,255,0.07);
            color: var(--white);
        }
        .adm-nav a.active {
            background: var(--gold);
            color: var(--green-dark);
            font-weight: 700;
        }

        .adm-sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .adm-sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .adm-sidebar-footer a:hover {
            background: rgba(220,53,69,0.15);
            color: #ff8fa3;
        }

        /* ─── MAIN CONTENT ─── */
        .adm-main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .adm-topbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .adm-topbar h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }
        .adm-topbar .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            font-size: 14px;
        }
        .adm-topbar .admin-avatar {
            width: 36px; height: 36px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--green-dark);
            font-weight: 700;
            font-size: 15px;
        }

        .adm-content {
            padding: 28px;
            flex: 1;
        }

        /* ─── SHARED COMPONENTS ─── */
        .adm-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        .adm-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .adm-card-header h3 {
            font-size: 16px;
            font-weight: 700;
        }

        /* ─── TABLE ─── */
        .adm-table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th {
            background: var(--bg);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            text-align: left;
            white-space: nowrap;
        }
        tbody td {
            padding: 14px 16px;
            border-top: 1px solid var(--border);
            vertical-align: middle;
        }
        tbody tr:hover { background: rgba(45,106,79,0.03); }

        /* ─── STATUS BADGES ─── */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge-pending    { background: #fff3cd; color: #856404; }
        .badge-confirmed  { background: #d1e7dd; color: #0f5132; }
        .badge-cancelled  { background: #f8d7da; color: #842029; }
        .badge-active     { background: #d1e7dd; color: #0f5132; }
        .badge-inactive   { background: #e2e3e5; color: #41464b; }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary   { background: var(--green); color: #fff; }
        .btn-primary:hover { background: var(--green-light); }
        .btn-gold      { background: var(--gold); color: var(--green-dark); }
        .btn-gold:hover { background: var(--gold-light); }
        .btn-danger    { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #bb2d3b; }
        .btn-sm        { padding: 5px 10px; font-size: 12px; }

        /* ─── STAT CARDS (dashboard) ─── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .stat-icon.green  { background: rgba(45,106,79,0.12); color: var(--green); }
        .stat-icon.gold   { background: rgba(200,169,110,0.15); color: #a07840; }
        .stat-icon.blue   { background: rgba(13,110,253,0.1); color: #0d6efd; }
        .stat-icon.orange { background: rgba(255,165,0,0.1); color: #e07700; }
        .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .stat-value { font-size: 28px; font-weight: 700; margin-top: 2px; }

        /* ─── FORM ELEMENTS for admin ─── */
        .adm-form-group { margin-bottom: 18px; }
        .adm-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 6px;
        }
        .adm-form-group input,
        .adm-form-group select,
        .adm-form-group textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: var(--text);
            padding: 10px 14px;
            background: var(--bg);
            outline: none;
            transition: border-color 0.2s;
        }
        .adm-form-group input:focus,
        .adm-form-group select:focus,
        .adm-form-group textarea:focus {
            border-color: var(--green);
            background: #fff;
        }

        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-danger  { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .adm-sidebar { display: none; }
            .adm-main { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- ─── SIDEBAR ──────────────────────────────────────────── -->
<aside class="adm-sidebar">
    <div class="adm-brand">
        <h2>PrimePath<br>Tours &amp; Safaris</h2>
        <p>Admin Dashboard</p>
    </div>

    <nav class="adm-nav">
        <span class="adm-nav-label">Main</span>
        <a href="dashboard.php" class="<?= ($activePage ?? '') === 'dashboard'    ? 'active' : '' ?>">
            <i class="fas fa-gauge-high"></i> Dashboard
        </a>
        <a href="bookings.php"  class="<?= ($activePage ?? '') === 'bookings'     ? 'active' : '' ?>">
            <i class="fas fa-calendar-check"></i> Bookings
        </a>
        <a href="clients.php"   class="<?= ($activePage ?? '') === 'clients'      ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Clients
        </a>
        <a href="subscribers.php" class="<?= ($activePage ?? '') === 'subscribers' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> Subscribers
        </a>
        <span class="adm-nav-label" style="margin-top:12px">Content</span>
        <a href="packages.php"  class="<?= ($activePage ?? '') === 'packages'     ? 'active' : '' ?>">
            <i class="fas fa-safari"></i> Safari Packages
        </a>
        <a href="excursions.php" class="<?= ($activePage ?? '') === 'excursions'   ? 'active' : '' ?>">
            <i class="fas fa-umbrella-beach"></i> Zanzibar Excursions
        </a>
        <a href="tours.php"      style="opacity:0.4; pointer-events:none; display:none;">
            <i class="fas fa-map-marked-alt"></i> Tours (Old)
        </a>
        <span class="adm-nav-label" style="margin-top:12px">Site</span>
        <a href="../index.html" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Website
        </a>
    </nav>

    <div class="adm-sidebar-footer">
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>

<!-- ─── MAIN ─────────────────────────────────────────────── -->
<div class="adm-main">
    <div class="adm-topbar">
        <h1><?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="admin-info">
            <span>Welcome, <?= getAdminName() ?></span>
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
        </div>
    </div>
    <div class="adm-content">
