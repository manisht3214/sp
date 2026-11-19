<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/index.php' : 'index.php'));
    exit;
}

$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    // $userType = $_POST['user_type'] ?? 'user';

    if (loginUser($username, $password)) {
        header('Location: ' . ($userType === 'admin' ? 'admin/index.php' : 'index.php'));
        exit;
    }

    $error = 'Invalid username, password, or user type.';
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-10 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-9">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Login</h2>
                    <p class="text-secondary mb-0">Use the Admin or User account to continue.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <select name="user_type" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="user" selected>User</option>
                        </select>
                    </div> -->

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder=" Enter username">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Enter password">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                    </button>
                </form>

                <div class="mt-4 p-3 rounded-3 bg-light small text-secondary">
                    <strong>Demo accounts:</strong><br>
                    Admin: admin / admin123<br>
                    User: user / user123
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
