<?php
/**
 * admin/clients.php
 * ─────────────────────────────────────────────────────────────
 * View all client records that were auto-built from booking
 * form submissions.
 *
 * These are NOT system users — they never log in.
 * Records are created automatically each time someone submits
 * the booking form (via api/book.php).
 * ─────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// ── Fetch all clients, most active (most bookings) first ──────
$clients = $pdo->query("
    SELECT id, name, email, phone, total_bookings, first_seen, last_booking
    FROM clients
    ORDER BY total_bookings DESC, last_booking DESC
")->fetchAll();

$pageTitle  = 'Client Records';
$activePage = 'clients';
require_once __DIR__ . '/../includes/admin_layout.php';
?>

<!-- ─── INFO NOTE ────────────────────────────────────────────── -->
<div style="background:rgba(45,106,79,0.08);border:1px solid rgba(45,106,79,0.2);border-radius:10px;
            padding:14px 18px;margin-bottom:22px;font-size:14px;color:var(--text-muted);">
    <i class="fas fa-info-circle" style="color:var(--green);margin-right:8px"></i>
    Client records are <strong>automatically created</strong> each time someone submits the booking form.
    No manual entry needed — this is your CRM, built from real enquiries.
</div>

<!-- ─── CLIENTS TABLE ───────────────────────────────────────── -->
<div class="adm-card">
    <div class="adm-card-header">
        <h3><i class="fas fa-users" style="color:var(--gold);margin-right:8px"></i>
            All Clients
            <span style="color:var(--text-muted);font-weight:400;font-size:14px;margin-left:8px">(<?= count($clients) ?>)</span>
        </h3>
    </div>
    <div class="adm-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Total Bookings</th>
                    <th>First Enquiry</th>
                    <th>Last Booking</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--text-muted);padding:32px;">
                            No clients yet. They'll appear automatically when visitors submit the booking form.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><strong>#<?= (int)$c['id'] ?></strong></td>
                            <td><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>"
                                   style="color:var(--green)">
                                    <?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($c['phone']): ?>
                                    <a href="tel:<?= htmlspecialchars($c['phone'], ENT_QUOTES, 'UTF-8') ?>"
                                       style="color:var(--green)">
                                        <?= htmlspecialchars($c['phone'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td>
                                <!-- Highlight repeat clients -->
                                <span style="font-weight:<?= $c['total_bookings'] > 1 ? '700;color:var(--green)' : '400' ?>">
                                    <?= (int)$c['total_bookings'] ?>
                                    <?= $c['total_bookings'] > 1 ? ' 🔁' : '' ?>
                                </span>
                            </td>
                            <td style="font-size:13px"><?= date('d M Y', strtotime($c['first_seen'])) ?></td>
                            <td style="font-size:13px"><?= date('d M Y', strtotime($c['last_booking'])) ?></td>
                            <td>
                                <!-- View all bookings for this client -->
                                <a href="bookings.php?email=<?= urlencode($c['email']) ?>"
                                   class="btn btn-primary btn-sm"
                                   title="View bookings by this client">
                                    <i class="fas fa-calendar-check"></i> Bookings
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
