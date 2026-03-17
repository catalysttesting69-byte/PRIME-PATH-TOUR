<?php
/**
 * admin/excursions.php
 * ─────────────────────────────────────────────────────────────
 * Manage Zanzibar Excursions.
 * ─────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$message   = '';
$msgType   = '';
$editExc   = null;

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $name        = htmlspecialchars(trim($_POST['name']        ?? ''), ENT_QUOTES, 'UTF-8');
    $category    = htmlspecialchars(trim($_POST['category']    ?? ''), ENT_QUOTES, 'UTF-8');
    $price       = htmlspecialchars(trim($_POST['price']       ?? ''), ENT_QUOTES, 'UTF-8');
    $image_url   = htmlspecialchars(trim($_POST['image_url']   ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $active      = isset($_POST['active']) ? 1 : 0;
    $excId       = (int) ($_POST['exc_id'] ?? 0);

    if ($action === 'add') {
        if (empty($name)) {
            $message = 'Name is required.';
            $msgType = 'danger';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO excursions (name, category, price, image_url, description, active)
                VALUES (:name, :category, :price, :image_url, :description, :active)
            ");
            $stmt->execute([
                ':name'        => $name,
                ':category'    => $category,
                ':price'       => $price,
                ':image_url'   => $image_url,
                ':description' => $description,
                ':active'      => $active,
            ]);
            $message = "Excursion added successfully.";
            $msgType = 'success';
        }
    }

    if ($action === 'update' && $excId > 0) {
        $stmt = $pdo->prepare("
            UPDATE excursions
            SET name=:name, category=:category, price=:price, image_url=:image_url,
                description=:description, active=:active
            WHERE id=:id
        ");
        $stmt->execute([
            ':name'        => $name,
            ':category'    => $category,
            ':price'       => $price,
            ':image_url'   => $image_url,
            ':description' => $description,
            ':active'      => $active,
            ':id'          => $excId,
        ]);
        $message = "Excursion updated successfully.";
        $msgType = 'success';
    }

    if ($action === 'toggle' && $excId > 0) {
        $newActive = (int) ($_POST['current_active'] ?? 0) === 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE excursions SET active=:active WHERE id=:id");
        $stmt->execute([':active' => $newActive, ':id' => $excId]);
        $message = 'Status updated.';
        $msgType = 'success';
    }

    if ($action === 'delete' && $excId > 0) {
        $stmt = $pdo->prepare("DELETE FROM excursions WHERE id=:id");
        $stmt->execute([':id' => $excId]);
        $message = 'Excursion deleted.';
        $msgType = 'success';
    }
}

// ── Load for editing ─────────────────────
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM excursions WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $editId]);
    $editExc = $stmt->fetch();
}

$excursions = $pdo->query("SELECT * FROM excursions ORDER BY active DESC, created_at DESC")->fetchAll();

$pageTitle  = 'Zanzibar Excursions';
$activePage = 'excursions';
require_once __DIR__ . '/../includes/admin_layout.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?>"><?= $message ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start">
    <div class="adm-card">
        <div class="adm-card-header"><h3>All Excursions (<?= count($excursions) ?>)</h3></div>
        <div class="adm-table-wrap">
            <table>
                <thead>
                    <tr><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($excursions as $e): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
                            <td><?= htmlspecialchars($e['category']) ?></td>
                            <td><?= htmlspecialchars($e['price']) ?></td>
                            <td><span class="badge badge-<?= $e['active'] ? 'active' : 'inactive' ?>"><?= $e['active'] ? 'Active' : 'Inactive' ?></span></td>
                            <td>
                                <a href="excursions.php?edit=<?= $e['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="exc_id" value="<?= $e['id'] ?>">
                                    <input type="hidden" name="current_active" value="<?= $e['active'] ?>">
                                    <button type="submit" class="btn btn-gold btn-sm"><?= $e['active'] ? 'Hide' : 'Show' ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header"><h3><?= $editExc ? 'Edit Excursion' : 'Add Excursion' ?></h3></div>
        <div style="padding:20px">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $editExc ? 'update' : 'add' ?>">
                <?php if ($editExc): ?><input type="hidden" name="exc_id" value="<?= $editExc['id'] ?>"><?php endif; ?>
                
                <div class="adm-form-group">
                    <label>Excursion Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($editExc['name'] ?? '') ?>">
                </div>

                <div class="adm-form-group">
                    <label>Category</label>
                    <input type="text" name="category" placeholder="e.g. City Tour, Nature" value="<?= htmlspecialchars($editExc['category'] ?? '') ?>">
                </div>

                <div class="adm-form-group">
                    <label>Price (Formatted)</label>
                    <input type="text" name="price" placeholder="e.g. $35" value="<?= htmlspecialchars($editExc['price'] ?? '') ?>">
                </div>

                <div class="adm-form-group"><label>Image URL</label><input type="url" name="image_url" value="<?= htmlspecialchars($editExc['image_url'] ?? '') ?>"></div>
                <div class="adm-form-group"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars($editExc['description'] ?? '') ?></textarea></div>

                <label style="display:flex;align-items:center;gap:8px;margin-bottom:15px;cursor:pointer">
                    <input type="checkbox" name="active" value="1" <?= ($editExc['active'] ?? 1) ? 'checked' : '' ?>> Active
                </label>

                <button type="submit" class="btn btn-gold" style="width:100%"><?= $editExc ? 'Save' : 'Add' ?></button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
