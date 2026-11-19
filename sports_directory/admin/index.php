
<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin('../login.php');

$pageTitle = "Administration";

$counts = [
    'Sports' => $pdo->query("SELECT COUNT(*) FROM sports")->fetchColumn(),
    'People' => $pdo->query("SELECT COUNT(*) FROM people")->fetchColumn(),
    'Teams' => $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn(),
    'Organizations' => $pdo->query("SELECT COUNT(*) FROM organizations")->fetchColumn()
];

require_once '../includes/header.php';
?>

<h2 class="fw-bold mb-2">Administration</h2>
<p class="text-secondary mb-4">Manage the sports directory records.</p>

<div class="row g-3 mb-4">

    <?php foreach ($counts as $label => $count): ?>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <h3><?= (int)$count ?></h3>
                <span><?= e($label) ?></span>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<div class="row g-3">

    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <i class="bi bi-person-plus fs-2 text-primary"></i>
                <h5 class="fw-bold mt-3">Add Person</h5>
                <p class="text-secondary">
                    Register players, coaches, referees and other staff.
                </p>
                <a href="add-person.php" class="btn btn-primary">
                    Add Person
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <i class="bi bi-trophy fs-2 text-primary"></i>
                <h5 class="fw-bold mt-3">Add Sports</h5>
                <p class="text-secondary">
                    Create new sports categories and directory entries.
                </p>
                <a href="add_sports.php" class="btn btn-outline-primary">
                    Add Sports
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <i class="bi bi-people fs-2 text-primary"></i>
                <h5 class="fw-bold mt-3">Add Users</h5>
                <p class="text-secondary">
                    Create new Admin and User accounts for the portal.
                </p>
                <a href="add-users.php" class="btn btn-outline-primary">
                    Add Users
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <i class="bi bi-globe fs-2 text-primary"></i>
                <h5 class="fw-bold mt-3">View Sports Directory</h5>
                <p class="text-secondary">
                    Browse sports, categories and professional profiles.
                </p>
                <a href="../index.php" class="btn btn-outline-primary">
                    Open Directory
                </a>
            </div>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>
