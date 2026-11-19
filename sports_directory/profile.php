
<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(404);
    exit('Invalid profile.');
}

$stmt = $pdo->prepare("
    SELECT
        p.*,
        ps.position,
        ps.designation,
        ps.jersey_number,
        ps.qualification,
        ps.statistics,
        ps.start_date,
        ps.end_date,
        s.name AS sport_name,
        s.slug AS sport_slug,
        c.name AS category_name,
        c.slug AS category_slug,
        t.name AS team_name,
        o.name AS organization_name
    FROM people p
    LEFT JOIN person_sports ps ON ps.person_id = p.id
    LEFT JOIN sports s ON s.id = ps.sport_id
    LEFT JOIN categories c ON c.id = ps.category_id
    LEFT JOIN teams t ON t.id = ps.team_id
    LEFT JOIN organizations o ON o.id = ps.organization_id
    WHERE p.id = ?
    LIMIT 1
");

$stmt->execute([$id]);
$person = $stmt->fetch();

if (!$person) {
    http_response_code(404);
    exit('Profile not found.');
}

$historyStmt = $pdo->prepare("
    SELECT * FROM career_history
    WHERE person_id = ?
    ORDER BY start_date DESC
");
$historyStmt->execute([$id]);
$history = $historyStmt->fetchAll();

$achievementStmt = $pdo->prepare("
    SELECT * FROM achievements
    WHERE person_id = ?
    ORDER BY achievement_year DESC
");
$achievementStmt->execute([$id]);
$achievements = $achievementStmt->fetchAll();

$statistics = json_decode($person['statistics'] ?? '{}', true) ?: [];

$pageTitle = $person['full_name'];

require_once 'includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php">Dashboard</a>
        </li>

        <?php if ($person['sport_slug']): ?>
            <li class="breadcrumb-item">
                <a href="sport.php?slug=<?= e($person['sport_slug']) ?>">
                    <?= e($person['sport_name']) ?>
                </a>
            </li>
        <?php endif; ?>

        <li class="breadcrumb-item active">
            Profile
        </li>
    </ol>
</nav>

<div class="profile-header rounded-4 mb-4">

    <div class="row align-items-center g-4">

        <div class="col-auto">
            <img src="<?= e(profilePhoto($person['photo'])) ?>"
                 class="profile-photo"
                 alt="<?= e($person['full_name']) ?>">
        </div>

        <div class="col">

            <span class="badge bg-warning text-dark mb-2">
                <?= e($person['category_name'] ?? 'Sports Professional') ?>
            </span>

            <h1 class="text-white fw-bold mb-2">
                <?= e($person['full_name']) ?>
            </h1>

            <p class="text-white-50 mb-2">
                <?= e($person['position'] ?: $person['designation'] ?: $person['qualification'] ?: 'Sports Professional') ?>
            </p>

            <div class="text-white-50 small">
                <?= e($person['nationality'] ?: 'Nationality not specified') ?>

                <?php if ($person['team_name']): ?>
                    &nbsp; | &nbsp; <?= e($person['team_name']) ?>
                <?php endif; ?>

                <?php if ($person['sport_name']): ?>
                    &nbsp; | &nbsp; <?= e($person['sport_name']) ?>
                <?php endif; ?>
            </div>

        </div>

        <div class="col-md-auto">
            <span class="badge bg-success">
                <?= e(ucfirst($person['status'])) ?>
            </span>
        </div>

    </div>

</div>

<div class="row g-4">

    <div class="col-lg-8">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">Biography</h5>

                <p class="text-secondary">
                    <?= nl2br(e($person['biography'] ?: 'No biography available.')) ?>
                </p>

            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">Career & Professional Information</h5>

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th>Sport</th>
                            <td><?= e($person['sport_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td><?= e($person['category_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th>Position / Role</th>
                            <td><?= e($person['position'] ?: $person['designation'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th>Qualification</th>
                            <td><?= e($person['qualification'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th>Team / Club</th>
                            <td><?= e($person['team_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th>Organization</th>
                            <td><?= e($person['organization_name'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th>Jersey Number</th>
                            <td><?= e($person['jersey_number'] ?: '-') ?></td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>

        <?php if ($statistics): ?>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Performance Statistics</h5>

                    <div class="row g-3">
                        <?php foreach ($statistics as $key => $value): ?>
                            <div class="col-6 col-md-4">
                                <div class="stat-card">
                                    <h3><?= e($value) ?></h3>
                                    <span><?= e(ucwords(str_replace('_', ' ', $key))) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">Career History</h5>

                <?php if ($history): ?>

                    <?php foreach ($history as $item): ?>
                        <div class="timeline-item mb-3">
                            <h6 class="fw-bold mb-1">
                                <?= e($item['role']) ?>
                            </h6>
                            <div class="text-secondary small">
                                <?= e($item['organization']) ?>
                            </div>
                            <div class="text-secondary small">
                                <?= e($item['start_date'] ?: 'Unknown') ?>
                                –
                                <?= e($item['end_date'] ?: 'Present') ?>
                            </div>
                            <p class="small mt-2">
                                <?= e($item['description']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <p class="text-secondary">No career history added.</p>
                <?php endif; ?>

            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">Achievements & Awards</h5>

                <?php if ($achievements): ?>

                    <?php foreach ($achievements as $item): ?>
                        <div class="d-flex gap-3 mb-3">
                            <i class="bi bi-trophy-fill text-warning fs-4"></i>

                            <div>
                                <h6 class="fw-bold mb-1">
                                    <?= e($item['title']) ?>
                                </h6>
                                <p class="text-secondary small mb-1">
                                    <?= e($item['competition']) ?>
                                    · <?= e($item['achievement_year']) ?>
                                </p>
                                <p class="small">
                                    <?= e($item['description']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <p class="text-secondary">No achievements added.</p>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <div class="col-lg-4">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">Personal Information</h5>

                <p class="small text-secondary mb-1">Full Name</p>
                <p class="fw-semibold"><?= e($person['full_name']) ?></p>

                <p class="small text-secondary mb-1">Date of Birth</p>
                <p class="fw-semibold">
                    <?= e($person['date_of_birth'] ?: '-') ?>
                </p>

                <p class="small text-secondary mb-1">Gender</p>
                <p class="fw-semibold"><?= e($person['gender'] ?: '-') ?></p>

                <p class="small text-secondary mb-1">Nationality</p>
                <p class="fw-semibold"><?= e($person['nationality'] ?: '-') ?></p>

                <p class="small text-secondary mb-1">Email</p>
                <p class="fw-semibold"><?= e($person['email'] ?: '-') ?></p>

                <p class="small text-secondary mb-1">Website</p>

                <?php if ($person['website']): ?>
                    <a href="<?= e($person['website']) ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        Visit Website
                    </a>
                <?php else: ?>
                    <p>-</p>
                <?php endif; ?>

            </div>
        </div>

        <?php if (isAdmin()): ?>
            <a href="admin/edit-person.php?id=<?= $person['id'] ?>"
               class="btn btn-primary w-100 mb-2">
                <i class="bi bi-pencil me-2"></i> Edit Profile
            </a>
        <?php endif; ?>

        <a href="javascript:history.back()"
           class="btn btn-outline-secondary w-100">
            Back to Directory
        </a>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
