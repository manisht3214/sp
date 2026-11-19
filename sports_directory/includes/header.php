
<?php
$basePath = str_contains($_SERVER['SCRIPT_NAME'], '/admin/') ? '../' : '';
$pageTitle = $pageTitle ?? 'Sports Directory';
$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($pageTitle) ?> | Sports Directory</title>
    <link href="<?= $basePath ?>assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
   
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark main-navbar">
    <div class="container">

        <a class="navbar-brand fw-bold" href="<?= $basePath ?>index.php">

            <img src="<?= $basePath ?>assets/uploads/logo.png"
                
                 class="navbar-logo">

            Player Profile Management System

        </a>

    </div>
</nav>


        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>



        
<!-- <nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <a class="navbar-brand text-white fw-bold" href="#">
            <img src="<?= $basePath ?>assets/uploads/logo.png" class="navbar-logo" alt="Logo">
            Player Profile Management System
        </a>

        <div class="ms-auto">
            <a href="/logout" class="btn btn-outline-light">
                Logout
            </a>
        </div>
    </div>
</nav> -->


        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>index.php#sports">Sports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>search.php">People Directory</a>
                </li>
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $basePath ?>admin/index.php">Administration</a>
                    </li>
                <?php endif; ?>

                <?php if (isLoggedIn()): ?>
                    <li class="nav-item ms-lg-3 d-flex align-items-center">
                        <span class="navbar-text text-white-50 me-2">
                            <?= e($currentUser['name'] ?? 'User') ?>
                            <small class="d-block text-uppercase text-warning">
                                <?= e($currentUser['user_type'] ?? 'user') ?>
                            </small>
                        </span>
                        <a class="btn btn-sm btn-outline-light" href="<?= $basePath ?>logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-sm btn-warning" href="<?= $basePath ?>login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
