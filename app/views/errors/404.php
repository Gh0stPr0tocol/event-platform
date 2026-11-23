<?php
$title = 'Page not found';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<section class="empty-state">
    <div class="container">
        <h1>Page not found</h1>
        <p>We could not find the event or page you were looking for.</p>
        <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=home&amp;action=index">Back to home</a>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>