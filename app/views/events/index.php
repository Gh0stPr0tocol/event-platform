<?php
$title = 'Find community events';
$isSearchPage = isset($events);
$keyword = trim($_GET['keyword'] ?? '');
$regionId = trim($_GET['region_id'] ?? '');
$categoryId = trim($_GET['category_id'] ?? '');
require APP_ROOT . '/app/views/layouts/header.php';
?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow">Find your people. Make a difference.</p>
            <h1>Good things happen when we come together.</h1>
            <p>Discover community, student and county-level events across Kenya. Register for free and show up.</p>
        </div>
        <div class="hero-art" aria-hidden="true">
            <div class="hero-circle hero-circle-one"></div>
            <div class="hero-circle hero-circle-two"></div>
            <span class="hero-badge">Kenya, connected</span>
        </div>
    </div>
</section>

<section class="search-section" aria-labelledby="search-heading">
    <div class="container">
        <h2 id="search-heading" class="visually-hidden">Search events</h2>
        <form class="event-search" method="get" action="<?= e(BASE_URL) ?>/index.php">
            <input type="hidden" name="page" value="events">
            <input type="hidden" name="action" value="index">
            <div class="search-field search-keyword">
                <label for="keyword">Keyword</label>
                <input id="keyword" name="keyword" type="search" value="<?= e($keyword) ?>" placeholder="Search events">
            </div>
            <div class="search-field">
                <label for="region_id">County</label>
                <select id="region_id" name="region_id">
                    <option value="">Any county</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= (int) $region['id'] ?>" <?= (string) $region['id'] === $regionId ? 'selected' : '' ?>>
                            <?= e($region['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="search-field">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= (string) $category['id'] === $categoryId ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="button button-primary search-button" type="submit">Search</button>
        </form>
    </div>
</section>

<?php if ($isSearchPage): ?>
<section class="events-section">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Explore</p>
                <h2>Events</h2>
            </div>
            <span class="result-count"><?= count($events) ?> <?= count($events) === 1 ? 'event' : 'events' ?></span>
        </div>
        <?php if ($events): ?>
            <div class="event-grid">
                <?php foreach ($events as $event): ?>
                    <?php require APP_ROOT . '/app/views/events/_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>No events found</h3>
                <p>Try a different keyword, county or category.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php else: ?>
<section class="events-section">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Don't miss out</p>
                <h2>Coming up soon</h2>
            </div>
            <a class="text-link" href="<?= e(BASE_URL) ?>/?page=events&amp;action=index">View all events <span aria-hidden="true">→</span></a>
        </div>
        <?php if ($upcomingEvents): ?>
            <div class="event-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <?php require APP_ROOT . '/app/views/events/_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>No upcoming events yet</h3>
                <p>Check back soon for something near you.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<section class="steps-section" id="how-it-works">
    <div class="container">
        <p class="eyebrow">Simple and free</p>
        <h2>Show up. Get involved.</h2>
        <div class="steps-grid">
            <article class="step-card">
                <span class="step-number">1</span>
                <h3>Find an event</h3>
                <p>Search by keyword, county or category to find what matters to you.</p>
            </article>
            <article class="step-card">
                <span class="step-number">2</span>
                <h3>Register free</h3>
                <p>Create an account and reserve your place in a few quick steps.</p>
            </article>
            <article class="step-card">
                <span class="step-number">3</span>
                <h3>Show up</h3>
                <p>Meet your neighbours and contribute to something meaningful.</p>
            </article>
        </div>
    </div>
</section>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>