
<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$sport = getSport($pdo, $_GET['slug'] ?? '');

if (!$sport) {
    http_response_code(404);
    exit('Sport not found.');
}

$pageTitle = $sport['name'];
$categories = getCategories($pdo, $sport['id']);

require_once 'includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">
            <?= e($sport['name']) ?>
        </li>
    </ol>
</nav>

<div class="sport-banner mb-4">
    <span class="badge bg-warning text-dark mb-2">SPORT DIRECTORY</span>
    <h1 class="fw-bold"><?= e($sport['name']) ?></h1>
    <p class="mb-0">
        <?= e($sport['description']) ?>
    </p>
</div>

<h4 class="fw-bold mb-3">Explore Categories</h4>

<div class="row g-3">

<?php foreach ($categories as $category): ?>

    <?php
    $count = 0;

    if ($category['entity_type'] === 'person') {
        $count = countCategory($pdo, $sport['id'], $category['id']);
    } elseif ($category['entity_type'] === 'team') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM teams WHERE sport_id = ?");
        $stmt->execute([$sport['id']]);
        $count = $stmt->fetchColumn();
    } elseif ($category['entity_type'] === 'organization') {
        $count = $pdo->query("SELECT COUNT(*) FROM organizations")->fetchColumn();
    }

    $icons = [
        'players' => 'bi-person-badge',
        'coaches' => 'bi-person-video3',
        'referees-umpires' => 'bi-clipboard-check',
        'judges-officials' => 'bi-gavel',
        'other-staff' => 'bi-people',
        'teams-clubs' => 'bi-shield',
        'organizations' => 'bi-building'
    ];

    $icon = $icons[$category['slug']] ?? 'bi-grid';
    ?>

    <div class="col-12 col-sm-6 col-lg-4">
        <a href="listing.php?sport=<?= e($sport['slug']) ?>&category=<?= e($category['slug']) ?>"
           class="text-decoration-none">

            <div class="category-card">
                <div class="category-icon">
                    <i class="bi <?= e($icon) ?>"></i>
                </div>

                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-1">
                        <?= e($category['name']) ?>
                    </h5>

                    <span class="text-secondary small">
                        <?= $count ?> registered records
                    </span>
                </div>

                <i class="bi bi-chevron-right text-secondary"></i>
            </div>
        </a>
    </div>

<?php endforeach; ?>

</div>

<?php require_once 'includes/footer.php'; ?>
