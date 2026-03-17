<?php
/**
 * admin/packages.php
 * ─────────────────────────────────────────────────────────────
 * Manage trip packages — safari, zanzibar, or combined.
 * ─────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$message   = '';
$msgType   = '';
$editPkg   = null;

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $title       = htmlspecialchars(trim($_POST['title']       ?? ''), ENT_QUOTES, 'UTF-8');
    $price       = (float) ($_POST['price'] ?? 0);
    $duration    = (int)   ($_POST['duration_days'] ?? 0);
    $image_url   = htmlspecialchars(trim($_POST['image_url']   ?? ''), ENT_QUOTES, 'UTF-8');
    $highlights  = htmlspecialchars(trim($_POST['highlights']  ?? ''), ENT_QUOTES, 'UTF-8');
    $route       = htmlspecialchars(trim($_POST['route']       ?? ''), ENT_QUOTES, 'UTF-8');
    $type        = htmlspecialchars(trim($_POST['type']        ?? 'safari'), ENT_QUOTES, 'UTF-8');
    $active      = isset($_POST['active']) ? 1 : 0;
    $pkgId       = (int) ($_POST['pkg_id'] ?? 0);

    if ($action === 'add') {
        if (empty($title)) {
            $message = 'Package title is required.';
            $msgType = 'danger';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO packages (title, price, duration_days, image_url, highlights, route, type, active)
                VALUES (:title, :price, :duration, :image_url, :highlights, :route, :type, :active)
            ");
            $stmt->execute([
                ':title'      => $title,
                ':price'      => $price,
                ':duration'   => $duration,
                ':image_url'  => $image_url,
                ':highlights' => $highlights,
                ':route'      => $route,
                ':type'       => $type,
                ':active'     => $active,
            ]);
            $message = "Package added successfully.";
            $msgType = 'success';
        }
    }

    if ($action === 'update' && $pkgId > 0) {
        $stmt = $pdo->prepare("
            UPDATE packages
            SET title=:title, price=:price, duration_days=:duration, image_url=:image_url,
                highlights=:highlights, route=:route, type=:type, active=:active
            WHERE id=:id
        ");
        $stmt->execute([
            ':title'      => $title,
            ':price'      => $price,
            ':duration'   => $duration,
            ':image_url'  => $image_url,
            ':highlights' => $highlights,
            ':route'      => $route,
            ':type'       => $type,
            ':active'     => $active,
            ':id'          => $pkgId,
        ]);
        $message = "Package updated successfully.";
        $msgType = 'success';
    }

    if ($action === 'toggle' && $pkgId > 0) {
        $newActive = (int) ($_POST['current_active'] ?? 0) === 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE packages SET active=:active WHERE id=:id");
        $stmt->execute([':active' => $newActive, ':id' => $pkgId]);
        $message = 'Status updated.';
        $msgType = 'success';
    }

    if ($action === 'delete' && $pkgId > 0) {
        $stmt = $pdo->prepare("DELETE FROM packages WHERE id=:id");
        $stmt->execute([':id' => $pkgId]);
        $message = 'Package deleted.';
        $msgType = 'success';
    }
}

// ── Load for editing ─────────────────────
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $editId]);
    $editPkg = $stmt->fetch();
}

$packages = $pdo->query("SELECT * FROM packages ORDER BY active DESC, created_at DESC")->fetchAll();

$pageTitle  = 'Safari Packages';
$activePage = 'packages';
require_once __DIR__ . '/../includes/admin_layout.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?>"><?= $message ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start">
    <div class="adm-card">
        <div class="adm-card-header"><h3>All Packages (<?= count($packages) ?>)</h3></div>
        <div class="adm-table-wrap">
            <table>
                <thead>
                    <tr><th>Title</th><th>Price</th><th>Type</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['title']) ?></strong><br><small><?= $p['duration_days'] ?> days</small></td>
                            <td>$<?= number_format($p['price'], 2) ?></td>
                            <td><span class="badge" style="background:#eee;color:var(--text)"><?= $p['type'] ?></span></td>
                            <td><span class="badge badge-<?= $p['active'] ? 'active' : 'inactive' ?>"><?= $p['active'] ? 'Active' : 'Inactive' ?></span></td>
                            <td>
                                <a href="packages.php?edit=<?= $p['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="pkg_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="current_active" value="<?= $p['active'] ?>">
                                    <button type="submit" class="btn btn-gold btn-sm"><?= $p['active'] ? 'Hide' : 'Show' ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header"><h3><?= $editPkg ? 'Edit Package' : 'Add Package' ?></h3></div>
        <div style="padding:20px">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $editPkg ? 'update' : 'add' ?>">
                <?php if ($editPkg): ?><input type="hidden" name="pkg_id" value="<?= $editPkg['id'] ?>"><?php endif; ?>
                
                <div class="adm-form-group">
                    <label>Title *</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($editPkg['title'] ?? '') ?>">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="adm-form-group"><label>Price ($)</label><input type="number" step="0.01" name="price" value="<?= $editPkg['price'] ?? 0 ?>"></div>
                    <div class="adm-form-group"><label>Days</label><input type="number" name="duration_days" value="<?= $editPkg['duration_days'] ?? 1 ?>"></div>
                </div>

                <div class="adm-form-group">
                    <label>Type</label>
                    <select name="type">
                        <option value="safari" <?= ($editPkg['type'] ?? '') === 'safari' ? 'selected' : '' ?>>Safari</option>
                        <option value="zanzibar" <?= ($editPkg['type'] ?? '') === 'zanzibar' ? 'selected' : '' ?>>Zanzibar</option>
                        <option value="combined" <?= ($editPkg['type'] ?? '') === 'combined' ? 'selected' : '' ?>>Combined</option>
                    </select>
                </div>

                <div class="adm-form-group"><label>Image URL</label><input type="url" name="image_url" value="<?= htmlspecialchars($editPkg['image_url'] ?? '') ?>"></div>
                <div class="adm-form-group"><label>Highlights (comma separated)</label><input type="text" name="highlights" value="<?= htmlspecialchars($editPkg['highlights'] ?? '') ?>"></div>
                <div class="adm-form-group"><label>Route (comma separated)</label><input type="text" name="route" value="<?= htmlspecialchars($editPkg['route'] ?? '') ?>"></div>

                <label style="display:flex;align-items:center;gap:8px;margin-bottom:15px;cursor:pointer">
                    <input type="checkbox" name="active" value="1" <?= ($editPkg['active'] ?? 1) ? 'checked' : '' ?>> Active
                </label>

                <button type="submit" class="btn btn-gold" style="width:100%"><?= $editPkg ? 'Save' : 'Add' ?></button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
