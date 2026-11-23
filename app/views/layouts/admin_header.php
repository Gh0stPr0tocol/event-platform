<?php $flash = getFlash(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Admin') ?> | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/css/main.css">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" id="admin-sidebar">
        <a class="admin-brand" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=dashboard">tukutane<span>.</span></a>
        <nav class="admin-nav" aria-label="Admin navigation">
            <a href="<?= e(BASE_URL) ?>/?page=admin&amp;action=dashboard"<?= ($activeAdmin ?? '') === 'dashboard' ? ' class="active"' : '' ?>>Dashboard</a>
            <a href="<?= e(BASE_URL) ?>/?page=admin&amp;action=events"<?= ($activeAdmin ?? '') === 'events' ? ' class="active"' : '' ?>>Events</a>
            <a href="<?= e(BASE_URL) ?>/?page=admin&amp;action=categories"<?= ($activeAdmin ?? '') === 'categories' ? ' class="active"' : '' ?>>Categories</a>
            <a href="<?= e(BASE_URL) ?>/?page=admin&amp;action=venues"<?= ($activeAdmin ?? '') === 'venues' ? ' class="active"' : '' ?>>Venues</a>
            <a href="<?= e(BASE_URL) ?>/?page=admin&amp;action=registrations"<?= ($activeAdmin ?? '') === 'registrations' ? ' class="active"' : '' ?>>Registrations</a>
        </nav>
        <div class="admin-sidebar-links">
            <a href="<?= e(BASE_URL) ?>/?page=home&amp;action=index">View site</a>
            <a href="<?= e(BASE_URL) ?>/?page=auth&amp;action=logout">Logout</a>
        </div>
    </aside>
    <main class="admin-main">
        <button class="admin-menu-button" type="button" aria-controls="admin-sidebar" aria-expanded="false" onclick="document.body.classList.toggle('admin-menu-open')">Menu</button>
        <?php if ($flash): ?>
            <div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
        <?php endif; ?>