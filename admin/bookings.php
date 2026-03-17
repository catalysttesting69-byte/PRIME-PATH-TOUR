<?php
/**
 * admin/bookings.php
 * ─────────────────────────────────────────────────────────────
 * View all booking enquiries and change their status.
 *
 * FEATURES:
 *   - Lists all bookings, newest first
 *   - Status filter (all / pending / confirmed / cancelled)
 *   - Inline status change via POST form
 *   - Booking details shown inline
 * ─────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$message = '';
$msgType = '';

// ── Handle status change POST ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';

    // Whitelist of allowed statuses (prevents arbitrary SQL values)
    $allowed = ['pending', 'confirmed', 'cancelled'];

    if ($bookingId > 0 && in_array($newStatus, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $bookingId]);
        $message = "Booking #{$bookingId} status updated to '{$newStatus}'.";
        $msgType = 'success';
    } else {
        $message = 'Invalid status update request.';
        $msgType = 'danger';
    }
}

// ── Filter by status (from URL: ?filter=pending) ──────────────
$filter  = $_GET['filter'] ?? 'all';
$allowed = ['all', 'pending', 'confirmed', 'cancelled'];
if (!in_array($filter, $allowed, true)) $filter = 'all';

// ── Fetch bookings ────────────────────────────────────────────
if ($filter === 'all') {
    $bookings = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE status = :status ORDER BY created_at DESC");
    $stmt->execute([':status' => $filter]);
    $bookings = $stmt->fetchAll();
}

$pageTitle  = 'Bookings';
$activePage = 'bookings';
require_once __DIR__ . '/../includes/admin_layout.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?>">
        <i class="fas fa-<?= $msgType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<!-- ─── STATUS FILTER TABS ──────────────────────────────────── -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <?php foreach (['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'] as $val => $label): ?>
        <a href="bookings.php?filter=<?= $val ?>"
           class="btn <?= $filter === $val ? 'btn-gold' : 'btn-primary' ?> btn-sm">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- ─── BOOKINGS TABLE ──────────────────────────────────────── -->
<div class="adm-card">
    <div class="adm-card-header">
        <h3><i class="fas fa-calendar-check" style="color:var(--gold);margin-right:8px"></i>
            <?= $filter === 'all' ? 'All Bookings' : ucfirst($filter) . ' Bookings' ?>
            <span style="color:var(--text-muted);font-weight:400;font-size:14px;margin-left:8px">(<?= count($bookings) ?>)</span>
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
                    <th>Tour</th>
                    <th>Date</th>
                    <th>Pax</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Change Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="11" style="text-align:center;color:var(--text-muted);padding:32px;">
                            No bookings found for this filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><strong>#<?= (int)$b['id'] ?></strong></td>
                            <td><?= htmlspecialchars($b['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($b['email'], ENT_QUOTES, 'UTF-8') ?>"
                                   style="color:var(--green)">
                                    <?= htmlspecialchars($b['email'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($b['phone']): ?>
                                    <a href="tel:<?= htmlspecialchars($b['phone'], ENT_QUOTES, 'UTF-8') ?>"
                                       style="color:var(--green)">
                                        <?= htmlspecialchars($b['phone'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($b['tour_name'] ?: '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $b['travel_date'] ? date('d M Y', strtotime($b['travel_date'])) : 'Flexible' ?></td>
                            <td><?= (int)$b['num_people'] ?></td>
                            <td style="max-width:180px;white-space:normal;font-size:12px;color:var(--text-muted)">
                                <?= htmlspecialchars(mb_strimwidth($b['message'] ?: '—', 0, 80, '…'), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($b['status'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= ucfirst($b['status']) ?>
                                </span>
                            </td>
                            <td>
                                <!-- Inline status change form — one form per row -->
                                <form method="POST" action="bookings.php?filter=<?= $filter ?>"
                                      style="display:flex;gap:6px;align-items:center">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                    <select name="status" style="font-size:12px;padding:5px 8px;border-radius:6px;border:1px solid var(--border);">
                                        <option value="pending"   <?= $b['status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $b['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="cancelled" <?= $b['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </form>
                            </td>
                            <td style="white-space:nowrap;font-size:12px;color:var(--text-muted)">
                                <?= date('d M Y', strtotime($b['created_at'])) ?><br>
                                <?= date('H:i', strtotime($b['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
