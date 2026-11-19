
<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "People Directory";
$q = trim($_GET['q'] ?? '');

$people = [];

if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.full_name,
            p.photo,
            p.nationality,
            p.status,
            s.name AS sport_name,
            c.name AS category_name,
            ps.position,
            ps.designation
        FROM people p
        LEFT JOIN person_sports ps ON ps.person_id = p.id
        LEFT JOIN sports s ON s.id = ps.sport_id
        LEFT JOIN categories c ON c.id = ps.category_id
        WHERE p.full_name LIKE ?
           OR p.nationality LIKE ?
           OR s.name LIKE ?
           OR c.name LIKE ?
        ORDER BY p.full_name
        LIMIT 100
    ");

    $term = "%$q%";
    $stmt->execute([$term, $term, $term, $term]);
    $people = $stmt->fetchAll();
}

require_once 'includes/header.php';
?>

<h2 class="fw-bold mb-2">People Directory</h2>
<p class="text-secondary">Search sports professionals across all sports.</p>

<form method="GET" class="mb-4">
    <div class="input-group input-group-lg">
        <input type="search"
               name="q"
               value="<?= e($q) ?>"
               class="form-control"
               placeholder="Search by name, nationality, sport...">

        <button class="btn btn-primary">
            <i class="bi bi-search"></i> Search
        </button>
    </div>
</form>

<?php if ($q !== ''): ?>

    <p class="text-secondary">
        <?= count($people) ?> results for
        <strong><?= e($q) ?></strong>
    </p>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Person</th>
                        <th>Sport</th>
                        <th>Category</th>
                        <th>Nationality</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($people as $person): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= e(profilePhoto($person['photo'])) ?>"
                                     class="table-avatar"
                                     alt="">
                                <strong><?= e($person['full_name']) ?></strong>
                            </div>
                        </td>

                        <td><?= e($person['sport_name'] ?: '-') ?></td>
                        <td><?= e($person['category_name'] ?: '-') ?></td>
                        <td><?= e($person['nationality'] ?: '-') ?></td>

                        <td>
                            <a href="profile.php?id=<?= $person['id'] ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$people): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            No matching records found.
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
