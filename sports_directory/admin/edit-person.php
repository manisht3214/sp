
<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin('../login.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare("SELECT * FROM people WHERE id = ?");
$stmt->execute([$id]);
$person = $stmt->fetch();

if (!$person) {
    http_response_code(404);
    exit('Person not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['full_name'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = $_POST['date_of_birth'] ?: null;
    $biography = trim($_POST['biography'] ?? '');
    $photo = trim($_POST['photo'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if (!$name || !in_array($status, ['active','inactive','retired'], true)) {
        $error = 'Please enter a valid name and status.';
    } else {
        $stmt = $pdo->prepare("
            UPDATE people SET
                full_name = ?,
                nationality = ?,
                gender = ?,
                date_of_birth = ?,
                biography = ?,
                photo = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $name, $nationality, $gender, $dob,
            $biography, $photo, $status, $id
        ]);

        header("Location: ../profile.php?id=" . $id);
        exit;
    }
}

$pageTitle = 'Edit Person';
require_once '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Edit Person</h2>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input class="form-control"
                       name="full_name"
                       required
                       value="<?= e($person['full_name']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Nationality</label>
                <input class="form-control"
                       name="nationality"
                       value="<?= e($person['nationality']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Gender</label>
                <input class="form-control"
                       name="gender"
                       value="<?= e($person['gender']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date"
                       class="form-control"
                       name="date_of_birth"
                       value="<?= e($person['date_of_birth']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Photo Filename</label>
                <input class="form-control"
                       name="photo"
                       value="<?= e($person['photo']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Biography</label>
                <textarea name="biography"
                          class="form-control"
                          rows="5"><?= e($person['biography']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['active','inactive','retired'] as $status): ?>
                        <option value="<?= $status ?>"
                            <?= $person['status'] === $status ? 'selected' : '' ?>>
                            <?= ucfirst($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn btn-primary">Update Profile</button>

            <a href="../profile.php?id=<?= $id ?>"
               class="btn btn-light">Cancel</a>

        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
