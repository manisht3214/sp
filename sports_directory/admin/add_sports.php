<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin('../login.php');

$pageTitle = 'Add Sports';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) && $_POST['status'] === '0' ? 0 : 1;

    if ($name === '') {
        $error = 'Sport name is required.';
    } else {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO sports (name, slug, description, status) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $slug, $description, $status]);
            $success = 'Sport added successfully.';
        } catch (Exception $e) {
            error_log($e->getMessage());
            $error = 'Unable to save the sport. Please try again.';
        }
    }
}

require_once '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Add Sport</h2>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Sport Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">
                        <i class="bi bi-save me-2"></i> Save Sport
                    </button>
                    <a href="index.php" class="btn btn-light ms-2">Back to Admin</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
