
<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin('../login.php');

$pageTitle = "Add Person";
$error = '';

$sports = getSports($pdo);
$categories = $pdo->query("
    SELECT * FROM categories WHERE entity_type = 'person'
    ORDER BY name
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['full_name'] ?? '');
    $sportId = (int)($_POST['sport_id'] ?? 0);
    $categoryId = (int)($_POST['category_id'] ?? 0);

    $nationality = trim($_POST['nationality'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = $_POST['date_of_birth'] ?: null;
    $position = trim($_POST['position'] ?? '');
    $biography = trim($_POST['biography'] ?? '');
    $biography = trim($_POST['biography'] ?? '');
$photo = null;

// Process uploaded photo
if (isset($_FILES['photo']) &&
    $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $error = "Photo upload failed.";
    } else {

        $uploadDir = __DIR__ . "/../assets/images/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES['photo']['tmp_name'];
        $mime = mime_content_type($tmpName);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedTypes[$mime])) {
            $error = "Only JPG, PNG and WEBP images allowed.";

        } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            $error = "Image must be smaller than 5MB.";

        } else {

            $fileName = uniqid('person_', true) . '.' . $allowedTypes[$mime];

            if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                $photo = "assets/images/" . $fileName;
            } else {
                $error = "Unable to save photo.";
            }
        }
    }
}

    $status = $_POST['status'] ?? 'active';

    if (!$name || !$sportId || !$categoryId) {
        $error = "Please complete all required fields.";
    } else {

        $check = $pdo->prepare("
            SELECT COUNT(*) FROM sport_categories sc
            JOIN categories c ON c.id = sc.category_id
            WHERE sc.sport_id = ?
              AND sc.category_id = ?
              AND c.entity_type = 'person'
        ");

        $check->execute([$sportId, $categoryId]);

        if (!$check->fetchColumn()) {
            $error = "Invalid sport or category selection.";
        } else {

            try {
                $pdo->beginTransaction();

                $slug = strtolower(trim(
                    preg_replace('/[^a-zA-Z0-9]+/', '-', $name),
                    '-'
                ));

                $stmt = $pdo->prepare("
                    INSERT INTO people
                    (full_name, slug, nationality, gender,
                     date_of_birth, biography, photo, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $name, $slug, $nationality, $gender,
                    $dob, $biography, $photo, $status
                ]);

                $personId = $pdo->lastInsertId();

                $stmt = $pdo->prepare("
                    INSERT INTO person_sports
                    (person_id, sport_id, category_id, position)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $personId, $sportId, $categoryId, $position
                ]);

                $pdo->commit();

                header("Location: ../profile.php?id=" . $personId);
                exit;

            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                error_log($e->getMessage());
                $error = "Unable to save the record.";
            }
        }
    }
}

require_once '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Add New Person</h2>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input type="text"
                           name="full_name"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <select name="nationality" class="form-select" required>
                        <option value=""> Select Country</option>
                        <option value="Nepal"> Nepal</option>
                        <option value="India"> India</option>
                        <option value="Pakistan"> Pakistan</option>
                        <option value="Bhutan"> Bhutan</option>
                        <option value="America"> America</option>
                        <option value="China"> China</option>
                        <option value="Ghana"> Ghana</option>
                        <option value="Maldives"> Maldives</option>
                        <option value="Canada"> Canada</option>
                        <option value="UK"> UK</option>
                        <option value="Germany"> Germany</option>
                        <option value="France"> France</option>
                        <option value="South Korea"> South Korea</option>
                        <option value="Bangladesh"> Bangladesh</option>
                        <option value="Japan"> Japan</option>
        </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sport *</label>
                    <select name="sport_id" class="form-select" required>
                        <option value="">Select sport</option>
                        <?php foreach ($sports as $sport): ?>
                            <option value="<?= $sport['id'] ?>">
                                <?= e($sport['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Position / Designation</label>
                    <input type="text"
                           name="position"
                           class="form-control"
                           placeholder="e.g. Forward, Head Coach">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                        <option>Prefer not to say</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date"
                           name="date_of_birth"
                           class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Upload Photo</label>
                    <input type="file"
                           name="photo"
                           class="form-control"
                           accept="image/jpeg, image/png, image/webp">
                           placeholder="image">
                    <small class="text-secondary">
                        Select JPG, PNG or WEBP (maximum 5MB).
                    </small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Biography</label>
                    <textarea name="biography"
                              class="form-control"
                              rows="5"></textarea>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-save me-2"></i>
                        Save Person
                    </button>

                    <a href="../index.php"
                       class="btn btn-light ms-2">
                        Cancel
                    </a>
                </div>

            </div>

        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
