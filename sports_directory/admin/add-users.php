<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin('../login.php');

$pageTitle = 'Add User';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $userType = $_POST['user_type'] ?? 'user';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (!registerUser($username, $password, $userType, $name)) {
        $error = 'That username already exists or is invalid.';
    } else {
        $success = 'User account created successfully.';
    }
}

require_once '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Add User</h2>

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
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Jane Doe">
                </div>

                <div class="col-md-6">
                    <label class="form-label">User Type</label>
                    <select name="user_type" class="form-select">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i> Save User
                    </button>
                    <a href="index.php" class="btn btn-light ms-2">Back to Admin</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
