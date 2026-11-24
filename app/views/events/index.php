<?php
$title = 'Find community events';
$isSearchPage = isset($events);
$isHomePage = !$isSearchPage;
$keyword = is_string($keyword ?? null)
    ? $keyword
    : (is_string($_GET['keyword'] ?? null) ? trim($_GET['keyword']) : '');
$regionId = is_string($regionId ?? null)
    ? $regionId
    : (is_string($_GET['region_id'] ?? null) ? trim($_GET['region_id']) : '');
$categoryValue = $categoryId ?? ($_GET['category_id'] ?? $_GET['category'] ?? '');
$categoryId = is_string($categoryValue) ? trim($categoryValue) : '';
$dateFrom = is_string($dateFrom ?? null)
    ? $dateFrom
    : (is_string($_GET['date_from'] ?? null) ? trim($_GET['date_from']) : '');
$dateTo = is_string($dateTo ?? null)
    ? $dateTo
    : (is_string($_GET['date_to'] ?? null) ? trim($_GET['date_to']) : '');
$filterErrors = is_array($filterErrors ?? null) ? $filterErrors : [];
$savedOnly = isset($savedOnly) ? (bool) $savedOnly : (($_GET['saved'] ?? '') === '1');
$currentPage = max(1, (int) ($currentPage ?? 1));
$eventPages = max(1, (int) ($eventPages ?? 1));
$eventTotal = max(0, (int) ($eventTotal ?? 0));
$activeFilterCount = isset($activeFilterCount)
    ? (int) $activeFilterCount
    : count(array_filter(
        [$keyword, $regionId, $categoryId, $dateFrom, $dateTo],
        static fn (string $value): bool => $value !== ''
    )) + ($savedOnly ? 1 : 0);
$activeFilterBaseCount = (int) ($activeFilterBaseCount ?? max(0, $activeFilterCount - ($savedOnly ? 1 : 0)));
$firstResult = (int) ($firstResult ?? ($eventTotal === 0 ? 0 : (($currentPage - 1) * 9) + 1));
$lastResult = (int) ($lastResult ?? min($currentPage * 9, $eventTotal));
require APP_ROOT . '/app/views/layouts/header.php';
?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow">Find your people. Make a difference.</p>
            <?php if ($isHomePage): ?>
                <h1>Good things happen when we <span class="hero-word" data-rotating-word aria-hidden="true"><span data-word-display>learn.</span><span class="hero-word-reserve">volunteer.</span></span><span class="visually-hidden">learn.</span></h1>
            <?php else: ?>
                <h1>Good things happen when we come together.</h1>
            <?php endif; ?>
            <p>Discover community, student and county-level events across Kenya. Register for free and show up.</p>
        </div>
        <div class="hero-art">
            <div class="hero-circle hero-circle-one">
                <img class="hero-image" src="<?= e(BASE_URL . '/images/tree.avif') ?>" alt="Silhouette of branching palm trees at sunset across Kenya">
            </div>
            <div class="hero-circle hero-circle-two" aria-hidden="true"></div>
            <span class="hero-badge">Kenya, connected</span>
        </div>
    </div>
</section>

<?php if ($isHomePage): ?>
<section class="home-stats" aria-label="Tukutane at a glance" data-count-section>
    <div class="container home-stats-grid">
        <div class="home-stat">
            <strong data-count-up="<?= (int) $homeStats['upcoming_events'] ?>"><?= number_format((int) $homeStats['upcoming_events']) ?></strong>
            <span>Upcoming events</span>
        </div>
        <div class="home-stat">
            <strong data-count-up="<?= (int) $homeStats['counties_with_events'] ?>"><?= number_format((int) $homeStats['counties_with_events']) ?></strong>
            <span>Counties with events</span>
        </div>
        <div class="home-stat">
            <strong data-count-up="<?= (int) $homeStats['confirmed_registrations'] ?>"><?= number_format((int) $homeStats['confirmed_registrations']) ?></strong>
            <span>Confirmed registrations</span>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="search-section" aria-labelledby="search-heading">
    <div class="container">
        <h2 id="search-heading" class="visually-hidden">Search events</h2>
        <form class="event-search" method="get" action="<?= e(BASE_URL) ?>/index.php">
            <input type="hidden" name="page" value="events">
            <input type="hidden" name="action" value="index">
            <div class="event-search-fields">
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
            </div>
            <div class="event-search-dates">
                <div class="search-field">
                    <label for="date_from">From date</label>
                    <input id="date_from" name="date_from" type="date" value="<?= e($dateFrom) ?>">
                    <?php if (!empty($filterErrors['date_from'])): ?>
                        <p class="field-error" role="alert"><?= e($filterErrors['date_from']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="search-field">
                    <label for="date_to">To date</label>
                    <input id="date_to" name="date_to" type="date" value="<?= e($dateTo) ?>">
                    <?php if (!empty($filterErrors['date_to'])): ?>
                        <p class="field-error" role="alert"><?= e($filterErrors['date_to']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <input type="hidden" name="saved" value="1" data-saved-param<?= $savedOnly ? '' : ' disabled' ?>>
            <button class="button button-primary search-button" type="submit">Search</button>
            <?php if ($isSearchPage): ?>
                <a class="clear-filters" data-clear-filters href="<?= e(BASE_URL) ?>/?page=events&amp;action=index"<?= $activeFilterCount > 0 ? '' : ' hidden' ?>>Clear filters</a>
            <?php endif; ?>
        </form>
        <?php if ($categories): ?>
            <nav class="category-chips" aria-label="Browse events by category">
                <span class="category-chips-label">Browse by category</span>
                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryQuery = ['page' => 'events', 'action' => 'index'];
                    if ($isSearchPage) {
                        foreach ([
                            'keyword' => $keyword,
                            'region_id' => $regionId,
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                        ] as $key => $value) {
                            if ($value !== '') {
                                $categoryQuery[$key] = $value;
                            }
                        }
                        if ($savedOnly) {
                            $categoryQuery['saved'] = '1';
                        }
                    }
                    $categoryQuery['category_id'] = (int) $category['id'];
                    $categoryUrl = BASE_URL . '/index.php?' . http_build_query($categoryQuery);
                    $activeCategory = $isSearchPage && $categoryId === (string) $category['id'];
                    ?>
                    <a class="category-chip<?= $activeCategory ? ' is-active' : '' ?>"<?= $activeCategory ? ' aria-current="page"' : '' ?> href="<?= e($categoryUrl) ?>"><?= e($category['name']) ?></a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>

<?php if ($isSearchPage): ?>
<section class="events-section" id="events" aria-labelledby="events-heading">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Explore</p>
                <h2 id="events-heading">Events</h2>
            </div>
            <div class="events-heading-actions">
                <span class="result-count">
                    <?= $eventTotal > 0
                        ? 'Showing ' . $firstResult . '–' . $lastResult . ' of ' . $eventTotal . ' ' . ($eventTotal === 1 ? 'event' : 'events')
                        : 'Showing 0 events' ?>
                </span>
                <span class="active-filter-count" data-active-filter-count data-base-count="<?= $activeFilterBaseCount ?>"<?= $activeFilterCount > 0 ? '' : ' hidden' ?>><?= $activeFilterCount ?> active <?= $activeFilterCount === 1 ? 'filter' : 'filters' ?></span>
                <label class="saved-event-filter">
                    <input type="checkbox" data-saved-filter<?= $savedOnly ? ' checked' : '' ?>>
                    <span>Saved events only</span>
                </label>
            </div>
        </div>
        <?php if ($events): ?>
            <div class="event-grid" data-saved-event-grid>
                <?php foreach ($events as $event): ?>
                    <?php require APP_ROOT . '/app/views/events/_card.php'; ?>
                <?php endforeach; ?>
            </div>
            <p class="saved-filter-empty" data-saved-filter-empty hidden>No saved events on this page.</p>
        <?php else: ?>
            <div class="empty-state">
                <h3>No events found</h3>
                <p>Try changing your filters to find an event.</p>
                <?php if ($activeFilterCount > 0): ?>
                    <a class="button button-secondary" href="<?= e(BASE_URL) ?>/?page=events&amp;action=index">Clear all filters</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($eventPages > 1):
            $paginationParams = [
                'page' => 'events',
                'action' => 'index',
            ];
            foreach ([
                'keyword' => $keyword,
                'region_id' => $regionId,
                'category_id' => $categoryId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ] as $key => $value) {
                if ($value !== '') {
                    $paginationParams[$key] = $value;
                }
            }
            if ($savedOnly) {
                $paginationParams['saved'] = '1';
            }
            $paginationBase = BASE_URL . '/index.php?' . http_build_query($paginationParams);
        ?>
            <nav class="pagination" aria-label="Event pages">
                <?php if ($currentPage > 1): ?>
                    <a class="pagination-direction" rel="prev" href="<?= e($paginationBase . '&list_page=' . ($currentPage - 1) . '#events') ?>" aria-label="Previous page">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $eventPages; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                        <span class="active" aria-current="page" aria-label="Page <?= $i ?>"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= e($paginationBase . '&list_page=' . $i . '#events') ?>" aria-label="Page <?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($currentPage < $eventPages): ?>
                    <a class="pagination-direction" rel="next" href="<?= e($paginationBase . '&list_page=' . ($currentPage + 1) . '#events') ?>" aria-label="Next page">Next</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>
<?php else: ?>
<?php if ($nextEvent): ?>
    <?php
    $secondsUntilEvent = max(0, strtotime($nextEvent['event_date'] . ' ' . $nextEvent['start_time']) - time());
    $initialDays = intdiv($secondsUntilEvent, 86400);
    $initialHours = intdiv($secondsUntilEvent % 86400, 3600);
    $initialMinutes = intdiv($secondsUntilEvent % 3600, 60);
    $initialSeconds = $secondsUntilEvent % 60;
    ?>
    <section class="next-event-section" aria-labelledby="next-event-heading">
        <div class="container">
            <article class="next-event-card">
                <div>
                    <p class="eyebrow">Mark your calendar</p>
                    <h2 id="next-event-heading">Next event</h2>
                    <h3><?= e($nextEvent['title']) ?></h3>
                    <p class="next-event-meta"><?= e(formatDate($nextEvent['event_date'])) ?> · <?= e(formatTime($nextEvent['start_time'])) ?></p>
                    <p class="next-event-meta"><?= e($nextEvent['venue_name']) ?>, <?= e($nextEvent['region_name']) ?></p>
                </div>
                <div class="countdown" data-countdown-target="<?= e($nextEvent['event_date'] . 'T' . $nextEvent['start_time']) ?>" aria-label="Time until event starts">
                    <div class="countdown-units" data-countdown-units<?= $secondsUntilEvent > 0 ? '' : ' hidden' ?>>
                        <span><strong data-countdown-days><?= $initialDays ?></strong><small>Days</small></span>
                        <span><strong data-countdown-hours><?= str_pad((string) $initialHours, 2, '0', STR_PAD_LEFT) ?></strong><small>Hours</small></span>
                        <span><strong data-countdown-minutes><?= str_pad((string) $initialMinutes, 2, '0', STR_PAD_LEFT) ?></strong><small>Minutes</small></span>
                        <span><strong data-countdown-seconds><?= str_pad((string) $initialSeconds, 2, '0', STR_PAD_LEFT) ?></strong><small>Seconds</small></span>
                    </div>
                    <p class="countdown-starting" data-countdown-started<?= $secondsUntilEvent > 0 ? ' hidden' : '' ?>>Starting now</p>
                    <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=events&amp;action=show&amp;id=<?= (int) $nextEvent['id'] ?>">View event</a>
                </div>
            </article>
        </div>
    </section>
<?php endif; ?>
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
            <article class="step-card" data-reveal>
                <span class="step-number">1</span>
                <h3>Find an event</h3>
                <p>Search by keyword, county or category to find what matters to you.</p>
            </article>
            <article class="step-card" data-reveal>
                <span class="step-number">2</span>
                <h3>Register free</h3>
                <p>Create an account and reserve your place in a few quick steps.</p>
            </article>
            <article class="step-card" data-reveal>
                <span class="step-number">3</span>
                <h3>Show up</h3>
                <p>Meet your neighbours and contribute to something meaningful.</p>
            </article>
        </div>
    </div>
</section>

<?php if ($isHomePage): ?>
<script defer src="<?= e(BASE_URL) ?>/js/home.js"></script>
<?php endif; ?>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>