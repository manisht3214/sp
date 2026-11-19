<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$sport = getSport($pdo, $_GET['sport'] ?? '');

if (!$sport) {
    http_response_code(404);
    exit('Sport not found.');
}

$category = getCategory(
    $pdo,
    $sport['id'],
    $_GET['category'] ?? ''
);

if (!$category) {
    http_response_code(404);
    exit('Category not found.');
}

$pageTitle = $category['name'] . ' - ' . $sport['name'];

$records = [];

if ($category['entity_type'] === 'person') {

    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.full_name,
            p.photo,
            p.nationality,
            p.status,
            ps.position,
            ps.designation,
            ps.jersey_number,
            ps.qualification,
            t.name AS team_name
        FROM person_sports ps
        INNER JOIN people p ON p.id = ps.person_id
        LEFT JOIN teams t ON t.id = ps.team_id
        WHERE ps.sport_id = ?
          AND ps.category_id = ?
        ORDER BY p.full_name
    ");

    $stmt->execute([$sport['id'], $category['id']]);
    $records = $stmt->fetchAll();

} elseif ($category['entity_type'] === 'team') {

    $stmt = $pdo->prepare("
        SELECT t.*, s.name AS sport_name
        FROM teams t
        JOIN sports s ON s.id = t.sport_id
        WHERE t.sport_id = ?
        ORDER BY t.name
    ");

    $stmt->execute([$sport['id']]);
    $records = $stmt->fetchAll();

} else {

    $records = $pdo->query("
        SELECT *
        FROM organizations
        ORDER BY name
    ")->fetchAll();
}

require_once 'includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="sport.php?slug=<?= e($sport['slug']) ?>">
                <?= e($sport['name']) ?>
            </a>
        </li>
        <li class="breadcrumb-item active">
            <?= e($category['name']) ?>
        </li>
    </ol>
</nav>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

    <div>
        <h2 class="fw-bold mb-1">
            <?= e($category['name']) ?>
        </h2>
        <p class="text-secondary mb-0">
            <?= e($sport['name']) ?> directory
        </p>
    </div>

    <?php if (isAdmin()): ?>
        <a href="admin/add-person.php?sport=<?= e($sport['slug']) ?>&category=<?= e($category['slug']) ?>"
            class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Record
        </a>
    <?php endif; ?>

</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">

        <div class="row g-2 mb-3">

            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search" id="tableSearch" class="form-control" placeholder="Search records...">
                </div>
            </div>

            <div class="col-md-3">
                <select id="statusFilter" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="retired">Retired</option>
                </select>
            </div>

            <div class="col-md-3 text-md-end">
                <span class="text-secondary small">
                    Total: <?= count($records) ?> records
                </span>
            </div>

        </div>

        <div class="table-responsive">

            <table class="table align-middle table-hover" id="recordsTable">

                <thead class="table-light">
                    <tr>
                        <th>Profile</th>

                        <?php if ($category['entity_type'] === 'person'): ?>
                            <th>Nationality</th>
                            <th>Position / Role</th>
                            <th>Team / Organization</th>
                            <th>Status</th>
                        <?php elseif ($category['entity_type'] === 'team'): ?>
                            <th>Country</th>
                            <th>Sport</th>
                        <?php else: ?>
                            <th>Country</th>
                            <th>Type</th>
                        <?php endif; ?>

                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($records as $record): ?>

                        <?php if ($category['entity_type'] === 'person'): ?>

                            <tr data-status="<?= e($record['status']) ?>">

                                <td>
                                    <div class="d-flex align-items-center gap-3">

                                        <img src="<?= e(profilePhoto($record['photo'])) ?>" class="table-avatar" alt="">

                                        <div>
                                            <a class="fw-semibold text-dark text-decoration-none"
                                                href="profile.php?id=<?= $record['id'] ?>">
                                                <?= e($record['full_name']) ?>
                                            </a>
                                            <div class="small text-secondary">
                                                ID: #<?= $record['id'] ?>
                                            </div>
                                        </div>

                                    </div>
                                </td>

                                <td><?= e($record['nationality'] ?: '-') ?></td>

                                <td>
                                    <?= e($record['position'] ?: $record['designation'] ?: $record['qualification'] ?: '-') ?>
                                </td>

                                <td><?= e($record['team_name'] ?: '-') ?></td>

                                <td>
                                    <span
                                        class="badge <?= $record['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                        <?= e(ucfirst($record['status'])) ?>
                                    </span>
                                </td>

                                <td class="text-end">

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            <li>
                                                <a class="dropdown-item" href="profile.php?id=<?= $record['id'] ?>">
                                                    <i class="bi bi-eye me-2"></i> View Profile
                                                </a>
                                            </li>

                                            <?php if (isAdmin()): ?>
                                                <li>
                                                    <a class="dropdown-item" href="admin/edit-person.php?id=<?= $record['id'] ?>">
                                                        <i class="bi bi-pencil me-2"></i> Edit
                                                    </a>
                                                </li>

                                                <li>
                                                    <form method="POST" action="admin/delete-person.php"
                                                        onsubmit="return confirm('Are you sure you want to delete this person?')">

                                                        <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">

                                                        <button class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>

                                        </ul>
                                    </div>

                                </td>

                            </tr>

                        <?php elseif ($category['entity_type'] === 'team'): ?>

                            <tr>
                                <td class="fw-semibold"><?= e($record['name']) ?></td>
                                <td><?= e($record['country']) ?></td>
                                <td><?= e($record['sport_name']) ?></td>
                                <td class="text-end">
                                    <span class="text-secondary small">Team record</span>
                                </td>
                            </tr>

                        <?php else: ?>

                            <tr>
                                <td class="fw-semibold"><?= e($record['name']) ?></td>
                                <td><?= e($record['country']) ?></td>
                                <td><?= e($record['organization_type']) ?></td>
                                <td class="text-end">
                                    <span class="text-secondary small">Organization record</span>
                                </td>
                            </tr>

                        <?php endif; ?>

                    <?php endforeach; ?>

                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No records found.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>