<?php
$title = $event['title'];
$capacity = max(0, (int) $event['capacity']);
$confirmedCount = max(0, (int) $event['confirmed_count']);
$seatsRemaining = max(0, (int) $event['seats_remaining']);
$seatsFillPercent = $capacity > 0 ? min(100, (int) round($confirmedCount / $capacity * 100)) : 0;
$seatsState = $seatsRemaining === 0
    ? 'error'
    : ($capacity > 0 && $seatsRemaining / $capacity < 0.2 ? 'warning' : 'success');
$registrationStatus = $seatsRemaining > 0 ? STATUS_CONFIRMED : STATUS_WAITLISTED;
require APP_ROOT . '/app/views/layouts/header.php';
?>
<section class="event-detail">
    <div class="container">
        <a class="text-link back-link" href="<?= e(BASE_URL) ?>/?page=events&amp;action=index">← Back to events</a>
        <div class="event-detail-grid">
            <div class="event-detail-content">
                <span class="category-tag"><?= e($event['category_name']) ?></span>
                <h1><?= e($event['title']) ?></h1>
                <button class="save-event-button save-event-detail" type="button" data-save-event data-event-id="<?= (int) $event['id'] ?>" data-event-title="<?= e($event['title']) ?>" aria-pressed="false" aria-label="Save <?= e($event['title']) ?> for later">
                    <span class="save-event-heart" aria-hidden="true">♡</span>
                    <span>Save for later</span>
                </button>
                <p class="event-detail-description"><?= nl2br(e($event['description'])) ?></p>
                <dl class="event-facts">
                    <div><dt>Date</dt><dd><?= e(formatDate($event['event_date'])) ?></dd></div>
                    <div><dt>Time</dt><dd><?= e(formatTime($event['start_time'])) ?></dd></div>
                    <div><dt>Venue</dt><dd><?= e($event['venue_name']) ?>, <?= e($event['address']) ?></dd></div>
                    <div><dt>County</dt><dd><?= e($event['region_name']) ?></dd></div>
                </dl>
            </div>
            <aside class="event-register-card">
                <section class="seats-panel"
                    data-seats-panel
                    data-capacity="<?= $capacity ?>"
                    data-confirmed="<?= $confirmedCount ?>"
                    data-remaining="<?= $seatsRemaining ?>">
                    <h2>Seat availability</h2>
                    <div class="seats-progress seats-progress-<?= e($seatsState) ?>"
                        data-seats-progress
                        role="progressbar"
                        aria-label="Confirmed seats"
                        aria-valuemin="0"
                        aria-valuemax="<?= $capacity ?>"
                        aria-valuenow="<?= min($capacity, $confirmedCount) ?>">
                        <span data-seats-fill style="width: <?= $seatsFillPercent ?>%"></span>
                    </div>
                    <p class="seats-counts">
                        <span><strong data-seats-confirmed><?= $confirmedCount ?></strong> confirmed</span>
                        <span><strong data-seats-remaining><?= $seatsRemaining ?></strong> remaining</span>
                        <span><strong data-seats-capacity><?= $capacity ?></strong> total capacity</span>
                    </p>
                </section>
                <section class="registration-summary" aria-labelledby="registration-summary-heading">
                    <h2 id="registration-summary-heading">Your registration</h2>
                    <dl>
                        <div><dt>Event</dt><dd><?= e($event['title']) ?></dd></div>
                        <div><dt>Date</dt><dd><?= e(formatDate($event['event_date'])) ?></dd></div>
                        <div><dt>Time</dt><dd><?= e(formatTime($event['start_time'])) ?></dd></div>
                        <div><dt>Venue</dt><dd><?= e($event['venue_name']) ?></dd></div>
                        <div><dt>County</dt><dd><?= e($event['region_name']) ?></dd></div>
                        <div>
                            <dt>Status if you register</dt>
                            <dd><span class="status status-<?= e($registrationStatus) ?>" data-registration-status><?= e(ucfirst($registrationStatus)) ?></span></dd>
                        </div>
                    </dl>
                </section>
                <?php if (isLoggedIn()): ?>
                    <form method="post" action="<?= e(BASE_URL) ?>/?page=registrations&amp;action=store" data-registration-form>
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                        <button class="button button-primary button-full" type="submit">Register for free</button>
                    </form>
                <?php else: ?>
                    <a class="button button-primary button-full" href="<?= e(BASE_URL) ?>/?page=auth&amp;action=login">Log in to register</a>
                <?php endif; ?>
            </aside>
        </div>
        <?php if (isLoggedIn()): ?>
            <dialog class="registration-confirm-dialog" data-registration-dialog aria-labelledby="registration-confirm-title" aria-describedby="registration-confirm-description">
                <h2 id="registration-confirm-title">Confirm your registration</h2>
                <p id="registration-confirm-description">Register for <?= e($event['title']) ?>? You can review your registration in your dashboard.</p>
                <form method="dialog">
                    <button class="button button-secondary" value="cancel">Go back</button>
                    <button class="button button-primary" value="confirm">Confirm registration</button>
                </form>
            </dialog>
        <?php endif; ?>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>