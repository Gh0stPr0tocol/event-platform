<?php require APP_ROOT . '/app/views/layouts/header.php'; ?>
<section class="guide-page" aria-labelledby="guide-title">
    <div class="container guide-content">
        <a class="guide-back-button" href="<?= e(BASE_URL) ?>/?page=home&amp;action=index#how-it-works">
            <span class="guide-back-arrow" aria-hidden="true">←</span>
            <span>How it works</span>
        </a>
        <p class="eyebrow">Step 2 · Save your place</p>
        <h1 id="guide-title">Register free</h1>
        <p class="guide-lead">There is no ticket fee to join a Tukutane event. Create an account or log in, then reserve a place from the event details page.</p>

        <div class="guide-panel">
            <h2>Three quick steps</h2>
            <ol class="guide-list">
                <li>Create your free account, or log in if you already have one.</li>
                <li>Open an event and review its details, venue and available seats.</li>
                <li>Select <strong>Register for this event</strong> and check your status.</li>
            </ol>
        </div>

        <div class="guide-panel guide-panel-soft">
            <h2>Confirmed or waitlisted?</h2>
            <p>If a confirmed place is available, your registration is confirmed. If the event is full, you can join its waitlist; your status is shown on the event page and in My registrations.</p>
        </div>

        <div class="guide-actions">
            <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=auth&amp;action=register">Create account</a>
            <a class="button button-secondary" href="<?= e(BASE_URL) ?>/?page=auth&amp;action=login">Log in</a>
            <a class="text-link" href="<?= e(BASE_URL) ?>/?page=guide&amp;action=show-up">Next: Show up →</a>
        </div>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
