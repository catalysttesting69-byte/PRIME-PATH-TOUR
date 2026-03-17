<?php
/**
 * admin/subscribers.php
 * ─────────────────────────────────────────────────────────────
 * View all newsletter subscribers.
 * Also allows deleting a subscriber (unsubscribe management).
 * ─────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$message = '';
$msgType = '';

// ── Handle delete subscriber ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $subId = (int) ($_POST['subscriber_id'] ?? 0);
    if ($subId > 0) {
        $stmt = $pdo->prepare("DELETE FROM subscribers WHERE id = :id");
        $stmt->execute([':id' => $subId]);
        $message = 'Subscriber removed.';
        $msgType = 'success';
    }
}

// ── Fetch all subscribers ─────────────────────────────────────
$subscribers = $pdo->query("SELECT id, email, subscribed_at FROM subscribers ORDER BY subscribed_at DESC")->fetchAll();

$pageTitle  = 'Newsletter Subscribers';
$activePage = 'subscribers';
require_once __DIR__ . '/../includes/admin_layout.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?>">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="adm-card">
    <div class="adm-card-header">
        <h3><i class="fas fa-envelope" style="color:var(--gold);margin-right:8px"></i>
            Newsletter Subscribers
            <span style="color:var(--text-muted);font-weight:400;font-size:14px;margin-left:8px">(<?= count($subscribers) ?>)</span>
        </h3>
    </div>
    <div class="adm-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Email Address</th>
                    <th>Subscribed On</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subscribers)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--text-muted);padding:32px;">
                            No subscribers yet. They'll appear when visitors use the newsletter form.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subscribers as $s): ?>
                        <tr>
                            <td>#<?= (int)$s['id'] ?></td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?>"
                                   style="color:var(--green)">
                                    <?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                            <td><?= date('d M Y H:i', strtotime($s['subscribed_at'])) ?></td>
                            <td>
                                <!-- Delete form with confirmation -->
                                <form method="POST" action="subscribers.php"
                                      onsubmit="return confirm('Remove this subscriber?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="subscriber_id" value="<?= (int)$s['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
