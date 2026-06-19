<?php
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'PHP Blog') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg app-navbar py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">BlogSphere</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="/index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/create.php">Yangi post</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user/profile.php">Profil</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-danger" href="/auth/logout.php">Chiqish</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/auth/login.php">Kirish</a></li>
                    <li class="nav-item"><a class="btn btn-sm btn-primary px-3" href="/auth/register.php">Ro'yxatdan o'tish</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> mb-4"><?= e($flash['message']) ?></div>
    <?php endif; ?>