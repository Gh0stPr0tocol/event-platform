<?php require APP_ROOT . '/app/views/layouts/header.php'; ?>
<section class="guide-page" aria-labelledby="guide-title">
    <div class="container guide-content">
        <a class="guide-back-button" href="<?= e(BASE_URL) ?>/?page=home&amp;action=index#how-it-works">
            <span class="guide-back-arrow" aria-hidden="true">←</span>
            <span>How it works</span>
        </a>
        <p class="eyebrow">Step 1 · Find your people</p>
        <h1 id="guide-title">Find an event that fits you</h1>
        <p class="guide-lead">Explore free community, student and county-level events across Kenya. Start with a topic you care about, or see what is happening near you.</p>

        <div class="guide-panel">
            <h2>Make your search yours</h2>
            <ul class="guide-list">
                <li><strong>Search by keyword</strong> to find a topic, activity or event name.</li>
                <li><strong>Choose a county or category</strong> to focus on local events and interests.</li>
                <li><strong>Add a date range</strong> when you have a day or weekend in mind.</li>
                <li><strong>Browse category chips</strong> for an easy way to discover something new.</li>
            </ul>
        </div>

        <div class="guide-panel guide-panel-soft">
            <h2>Before you choose</h2>
            <p>Open an event to check its date, start time, venue and remaining seats. You can save an event to revisit it later.</p>
        </div>

        <div class="guide-actions">
            <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=events&amp;action=index">Explore events</a>
            <a class="button button-secondary" href="<?= e(BASE_URL) ?>/?page=guide&amp;action=register-free">Next: Register free</a>
        </div>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
