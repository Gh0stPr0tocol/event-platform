<?php $flash = getFlash(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? APP_NAME) ?> | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/css/main.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= e(BASE_URL) ?>/?page=home&amp;action=index" aria-label="Tukutane home">
            <span>tukutane.</span>
        </a>
        <nav class="main-nav" aria-label="Main navigation">
            <a href="<?= e(BASE_URL) ?>/?page=events&amp;action=index">Explore events</a>
            <a href="<?= e(BASE_URL) ?>/?page=home&amp;action=index#how-it-works">How it works</a>
            <?php if (isLoggedIn()):
                $user = currentUser(); ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= e(BASE_URL) ?>/?page=admin&amp;action=dashboard">Dashboard</a>
                <?php endif; ?>
                <?php if ($user['role'] !== 'admin'): ?>
                    <a href="<?= e(BASE_URL) ?>/?page=dashboard&amp;action=index">Dashboard</a>
                    <a href="<?= e(BASE_URL) ?>/?page=dashboard&amp;action=registrations">My registrations</a>
                    <a href="<?= e(BASE_URL) ?>/?page=dashboard&amp;action=profile">Profile</a>
                <?php endif; ?>
                <a class="nav-button nav-button-logout" href="<?= e(BASE_URL) ?>/?page=auth&amp;action=logout">Logout</a>
            <?php else: ?>
                <a href="<?= e(BASE_URL) ?>/?page=auth&amp;action=login">Log in</a>
                <a class="nav-button" href="<?= e(BASE_URL) ?>/?page=auth&amp;action=register">Create account</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
<?php if ($flash): ?>
    <div class="container">
        <div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
    </div>
<?php endif; ?>