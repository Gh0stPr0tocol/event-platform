<?php require APP_ROOT . '/app/views/layouts/header.php'; ?>
<section class="guide-page" aria-labelledby="guide-title">
    <div class="container guide-content">
        <a class="guide-back-button" href="<?= e(BASE_URL) ?>/?page=home&amp;action=index#how-it-works">
            <span class="guide-back-arrow" aria-hidden="true">←</span>
            <span>How it works</span>
        </a>
        <p class="eyebrow">Step 3 · Be part of it</p>
        <h1 id="guide-title">Show up and get involved</h1>
        <p class="guide-lead">A community event is more than a date on your calendar. Come ready to participate, meet people and make a positive contribution.</p>

        <div class="guide-panel">
            <h2>Make the day go smoothly</h2>
            <ul class="guide-list">
                <li>Check My registrations for your event status and revisit the event page for time and venue details.</li>
                <li>Plan your trip and arrive in time for the organiser’s welcome or opening activities.</li>
                <li>Follow event guidance, respect other participants and ask before taking or sharing photos.</li>
                <li>Be welcoming and leave room for everyone to contribute.</li>
            </ul>
        </div>

        <div class="guide-panel guide-panel-soft">
            <h2>Plans changed?</h2>
            <p>For a future event, cancel from My registrations so the place can become available to someone else. Waitlisted? Check your status before you travel.</p>
        </div>

        <div class="guide-actions">
            <?php if (isLoggedIn() && currentUser()['role'] !== 'admin'): ?>
                <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=dashboard&amp;action=registrations">My registrations</a>
            <?php elseif (!isLoggedIn()): ?>
                <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=auth&amp;action=login">Log in to view registrations</a>
            <?php endif; ?>
            <a class="button button-secondary" href="<?= e(BASE_URL) ?>/?page=events&amp;action=index">Explore more events</a>
        </div>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
