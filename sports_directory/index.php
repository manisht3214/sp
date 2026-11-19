
<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Dashboard";

$sports = getSports($pdo);

$totalSports = count($sports);
$totalPeople = $pdo->query("SELECT COUNT(*) FROM people")->fetchColumn();
$totalTeams = $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn();
$totalOrganizations = $pdo->query("SELECT COUNT(*) FROM organizations")->fetchColumn();

require_once 'includes/header.php';
?>

<section class="hero-section rounded-4 p-4 p-md-5 mb-4">
    <span class="badge bg-warning text-dark mb-3">Central Dashboard</span>

    <h1 class="display-5 fw-bold text-white">
        Search Players
    </h1>

    <p class="text-white-50 col-lg-7">
        <!-- Discover athletes, coaches, referees, teams, clubs
        and sports professionals from around the world. -->
    </p>

    <form action="search.php" method="GET" class="search-box mt-4">
        <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search"></i>
            </span>

            <input type="search"
                   name="q"
                   class="form-control border-0"
                   placeholder="Search athletes, coaches, sports...">

            <button class="btn btn-danger">
                Search
            </button>
        </div>
    </form>
</section>

<div class="row g-3 mb-5">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <i class="bi bi-trophy"></i>
            <h3><?= $totalSports ?></h3>
            <span>Sports</span>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <i class="bi bi-people"></i>
            <h3><?= $totalPeople ?></h3>
            <span>People</span>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <i class="bi bi-shield"></i>
            <h3><?= $totalTeams ?></h3>
            <span>Teams & Clubs</span>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <i class="bi bi-building"></i>
            <h3><?= $totalOrganizations ?></h3>
            <span>Organizations</span>
        </div>
    </div>
</div>

<section id="sports">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Explore Sports</h3>
            <p class="text-secondary">Choose a sport to explore its directory.</p>
        </div>
    </div>

    <div class="mb-4">
        <input type="search"
               id="sportSearch"
               class="form-control form-control-lg"
               placeholder="Filter sports...">
    </div>

    <div class="row g-4" id="sportsGrid">

        <?php foreach ($sports as $sport): ?>

            <?php
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM person_sports
                WHERE sport_id = ?
            ");
            $stmt->execute([$sport['id']]);
            $peopleCount = $stmt->fetchColumn();
            ?>

            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 sport-item"
                 data-name="<?= e(strtolower($sport['name'])) ?>">

                <a href="sport.php?slug=<?= e($sport['slug']) ?>"
                   class="text-decoration-none">

                    <div class="sport-card h-100">

                        <div class="sport-card-image">
                            <?php if (!empty($sport['image'])): ?>
                                <img src="assets/images/<?= e(basename($sport['image'])) ?>"
                                     alt="<?= e($sport['name']) ?>">
                            <?php else: ?>
                                <div class="sport-placeholder">
                                    <i class="bi bi-trophy"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-3">
                            <h5 class="fw-bold text-dark">
                                <?= e($sport['name']) ?>
                            </h5>

                            <p class="text-secondary small mb-2">
                                <?= e($sport['description']) ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary">
                                    <?= $peopleCount ?> profiles
                                </span>

                                <span class="text-primary small fw-semibold">
                                    Explore <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </div>

                    </div>
                </a>
            </div>

        <?php endforeach; ?>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
