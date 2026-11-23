<?php
$title = $event['title'];
require APP_ROOT . '/app/views/layouts/header.php';
?>
<section class="event-detail">
    <div class="container">
        <a class="text-link back-link" href="<?= e(BASE_URL) ?>/?page=events&amp;action=index">← Back to events</a>
        <div class="event-detail-grid">
            <div class="event-detail-content">
                <span class="category-tag"><?= e($event['category_name']) ?></span>
                <h1><?= e($event['title']) ?></h1>
                <p class="event-detail-description"><?= nl2br(e($event['description'])) ?></p>
                <dl class="event-facts">
                    <div><dt>Date</dt><dd><?= e(formatDate($event['event_date'])) ?></dd></div>
                    <div><dt>Time</dt><dd><?= e(formatTime($event['start_time'])) ?></dd></div>
                    <div><dt>Venue</dt><dd><?= e($event['venue_name']) ?>, <?= e($event['address']) ?></dd></div>
                    <div><dt>County</dt><dd><?= e($event['region_name']) ?></dd></div>
                </dl>
            </div>
            <aside class="event-register-card">
                <strong><?= (int) $event['seats_remaining'] ?> seats remaining</strong>
                <?php if (isLoggedIn()): ?>
                    <form method="post" action="<?= e(BASE_URL) ?>/?page=registrations&amp;action=store">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                        <button class="button button-primary button-full" type="submit">Register for free</button>
                    </form>
                <?php else: ?>
                    <a class="button button-primary button-full" href="<?= e(BASE_URL) ?>/?page=auth&amp;action=login">Log in to register</a>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>