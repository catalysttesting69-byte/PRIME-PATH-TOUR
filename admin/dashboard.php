<?php
/**
 * admin/dashboard.php
 * ─────────────────────────────────────────────────────────────
 * Main admin overview page.
 * Shows key statistics and recently submitted bookings.
 * ─────────────────────────────────────────────────────────────
 */

// ── Security: must be logged in ──────────────────────────────
require_once __DIR__ . '/../includes/auth.php';

// ── Database ──────────────────────────────────────────────────
require_once __DIR__ . '/../includes/db.php';

// ── Fetch stats (one query each, fast on small datasets) ──────

// Total bookings
$totalBookings    = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
// Pending bookings (need attention)
$pendingBookings  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
// Total unique clients
$totalClients     = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
// Total newsletter subscribers
$totalSubscribers = $pdo->query("SELECT COUNT(*) FROM subscribers")->fetchColumn();
// Total active tours
$totalTours       = $pdo->query("SELECT COUNT(*) FROM tours WHERE active = 1")->fetchColumn();

// ── Fetch 5 most recent bookings ──────────────────────────────
$recentBookings = $pdo->query("
    SELECT id, name, email, tour_name, travel_date, num_people, status, created_at
    FROM bookings
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();

// ── Page setup ────────────────────────────────────────────────
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/../includes/admin_layout.php';
?>

<!-- ─── STAT CARDS ─────────────────────────────────────────── -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value"><?= (int)$totalBookings ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-label">Pending Review</div>
            <div class="stat-value"><?= (int)$pendingBookings ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-label">Client Records</div>
            <div class="stat-value"><?= (int)$totalClients ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="stat-label">Subscribers</div>
            <div class="stat-value"><?= (int)$totalSubscribers ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-map-marked-alt"></i></div>
        <div>
            <div class="stat-label">Active Tours</div>
            <div class="stat-value"><?= (int)$totalTours ?></div>
        </div>
    </div>
</div>

<!-- ─── RECENT BOOKINGS TABLE ──────────────────────────────── -->
<div class="adm-card">
    <div class="adm-card-header">
        <h3><i class="fas fa-clock" style="color:var(--gold);margin-right:8px"></i>Recent Booking Enquiries</h3>
        <a href="bookings.php" class="btn btn-primary btn-sm">View All</a>
    </div>
    <div class="adm-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Tour</th>
                    <th>Travel Date</th>
                    <th>People</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentBookings)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--text-muted);padding:28px;">
                            No bookings yet. They'll appear here when visitors submit the form.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td>#<?= (int)$b['id'] ?></td>
                            <td><strong><?= htmlspecialchars($b['name'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><?= htmlspecialchars($b['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($b['tour_name'] ?: '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $b['travel_date'] ? date('d M Y', strtotime($b['travel_date'])) : 'Flexible' ?></td>
                            <td><?= (int)$b['num_people'] ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($b['status'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= ucfirst(htmlspecialchars($b['status'], ENT_QUOTES, 'UTF-8')) ?>
                                </span>
                            </td>
                            <td><?= date('d M Y H:i', strtotime($b['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
