<?php
/**
 * admin/tours.php
 * ─────────────────────────────────────────────────────────────
 * Manage tour packages — add new tours, edit existing ones,
 * and deactivate (hide) tours without deleting them.
 *
 * FEATURES:
 *   - Shows all tours in a table (active + inactive)
 *   - Add new tour via form
 *   - Inline edit via GET ?edit=ID
 *   - Toggle active/inactive status
 *   - Delete a tour (with confirmation)
 * ─────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$message   = '';
$msgType   = '';
$editTour  = null; // Holds tour data when editing

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Sanitize text inputs
    $title       = htmlspecialchars(trim($_POST['title']       ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $price       = (float) ($_POST['price'] ?? 0);
    $duration    = (int)   ($_POST['duration_days'] ?? 0);
    $image_url   = htmlspecialchars(trim($_POST['image_url']   ?? ''), ENT_QUOTES, 'UTF-8');
    $active      = isset($_POST['active']) ? 1 : 0;
    $tourId      = (int) ($_POST['tour_id'] ?? 0);

    // ── Add new tour ──────────────────────────────────────────
    if ($action === 'add') {
        if (empty($title)) {
            $message = 'Tour title is required.';
            $msgType = 'danger';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO tours (title, description, price, duration_days, image_url, active)
                VALUES (:title, :description, :price, :duration, :image_url, :active)
            ");
            $stmt->execute([
                ':title'       => $title,
                ':description' => $description,
                ':price'       => $price,
                ':duration'    => $duration,
                ':image_url'   => $image_url,
                ':active'      => $active,
            ]);
            $message = "Tour '{$title}' added successfully.";
            $msgType = 'success';
        }
    }

    // ── Update existing tour ──────────────────────────────────
    if ($action === 'update' && $tourId > 0) {
        $stmt = $pdo->prepare("
            UPDATE tours
            SET title=:title, description=:description, price=:price,
                duration_days=:duration, image_url=:image_url, active=:active
            WHERE id=:id
        ");
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':price'       => $price,
            ':duration'    => $duration,
            ':image_url'   => $image_url,
            ':active'      => $active,
            ':id'          => $tourId,
        ]);
        $message = "Tour updated successfully.";
        $msgType = 'success';
    }

    // ── Toggle active/inactive ────────────────────────────────
    if ($action === 'toggle' && $tourId > 0) {
        $newActive = (int) ($_POST['current_active'] ?? 0) === 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE tours SET active=:active WHERE id=:id");
        $stmt->execute([':active' => $newActive, ':id' => $tourId]);
        $message = 'Tour status updated.';
        $msgType = 'success';
    }

    // ── Delete tour ───────────────────────────────────────────
    if ($action === 'delete' && $tourId > 0) {
        $stmt = $pdo->prepare("DELETE FROM tours WHERE id=:id");
        $stmt->execute([':id' => $tourId]);
        $message = 'Tour deleted permanently.';
        $msgType = 'success';
        $editTour = null;
    }
}

// ── Load tour for editing (GET ?edit=ID) ─────────────────────
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $editId]);
    $editTour = $stmt->fetch();
}

// ── Fetch all tours ───────────────────────────────────────────
$tours = $pdo->query("SELECT * FROM tours ORDER BY active DESC, created_at DESC")->fetchAll();

$pageTitle  = 'Tour Packages';
$activePage = 'tours';
require_once __DIR__ . '/../includes/admin_layout.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?>">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start">

    <!-- ─── TOURS TABLE ──────────────────────────────────────── -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3><i class="fas fa-map-marked-alt" style="color:var(--gold);margin-right:8px"></i>
                All Tour Packages (<?= count($tours) ?>)
            </h3>
        </div>
        <div class="adm-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Price (USD)</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tours)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--text-muted);padding:28px;">
                                No tours yet. Add your first tour using the form.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tours as $t): ?>
                            <tr>
                                <td>#<?= (int)$t['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if ($t['image_url']): ?>
                                        <br><small style="color:var(--text-muted)">Has image</small>
                                    <?php endif; ?>
                                </td>
                                <td>$<?= number_format((float)$t['price'], 2) ?></td>
                                <td><?= (int)$t['duration_days'] ?> days</td>
                                <td>
                                    <span class="badge badge-<?= $t['active'] ? 'active' : 'inactive' ?>">
                                        <?= $t['active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td style="display:flex;gap:6px;flex-wrap:wrap">
                                    <!-- Edit button — loads tour data into the form on the right -->
                                    <a href="tours.php?edit=<?= (int)$t['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-pencil"></i> Edit
                                    </a>

                                    <!-- Toggle active/inactive -->
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="tour_id" value="<?= (int)$t['id'] ?>">
                                        <input type="hidden" name="current_active" value="<?= (int)$t['active'] ?>">
                                        <button type="submit" class="btn btn-gold btn-sm">
                                            <?= $t['active'] ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>

                                    <!-- Delete with confirmation -->
                                    <form method="POST" onsubmit="return confirm('Delete this tour permanently?')" style="display:inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="tour_id" value="<?= (int)$t['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
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

    <!-- ─── ADD / EDIT FORM ──────────────────────────────────── -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3>
                <i class="fas fa-<?= $editTour ? 'pencil' : 'plus' ?>" style="color:var(--gold);margin-right:8px"></i>
                <?= $editTour ? 'Edit Tour' : 'Add New Tour' ?>
            </h3>
            <?php if ($editTour): ?>
                <a href="tours.php" class="btn btn-primary btn-sm">+ Add New</a>
            <?php endif; ?>
        </div>
        <div style="padding:22px">
            <form method="POST" action="tours.php">
                <!-- Hidden fields to tell PHP which action to take -->
                <input type="hidden" name="action"  value="<?= $editTour ? 'update' : 'add' ?>">
                <?php if ($editTour): ?>
                    <input type="hidden" name="tour_id" value="<?= (int)$editTour['id'] ?>">
                <?php endif; ?>

                <div class="adm-form-group">
                    <label for="title">Tour Title *</label>
                    <input type="text" id="title" name="title" required
                           value="<?= htmlspecialchars($editTour['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="e.g. 7-Day Tanzania Safari">
                </div>

                <div class="adm-form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="Describe the tour highlights..."><?= htmlspecialchars($editTour['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div class="adm-form-group">
                        <label for="price">Price (USD)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0"
                               value="<?= number_format((float)($editTour['price'] ?? 0), 2) ?>"
                               placeholder="1500.00">
                    </div>
                    <div class="adm-form-group">
                        <label for="duration_days">Duration (days)</label>
                        <input type="number" id="duration_days" name="duration_days" min="1"
                               value="<?= (int)($editTour['duration_days'] ?? 7) ?>">
                    </div>
                </div>

                <div class="adm-form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" id="image_url" name="image_url"
                           value="<?= htmlspecialchars($editTour['image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="https://... (optional)">
                </div>

                <div class="adm-form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none">
                        <input type="checkbox" name="active" value="1"
                               <?= ($editTour['active'] ?? 1) ? 'checked' : '' ?>
                               style="width:auto;margin:0">
                        <span style="font-weight:500">Active (visible to visitors)</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center">
                    <i class="fas fa-<?= $editTour ? 'save' : 'plus' ?>"></i>
                    <?= $editTour ? 'Save Changes' : 'Add Tour Package' ?>
                </button>
            </form>
        </div>
    </div>

</div><!-- end grid -->

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
