<?php
$capacity = (int) ($event['capacity'] ?? 0);
$seatsRemaining = max(0, (int) ($event['seats_remaining'] ?? 0));
$isLowSeatCount = $seatsRemaining > 0
    && ($seatsRemaining <= 3 || ($capacity > 0 && $seatsRemaining / $capacity <= 0.1));
?>
<article class="event-card" data-reveal>
    <a class="event-card-image" href="<?= e(BASE_URL) ?>/?page=events&amp;action=show&amp;id=<?= (int) $event['id'] ?>" aria-label="View <?= e($event['title']) ?>">
        <?php
        $imageFile = !empty($event['image'])
            ? APP_ROOT . '/public/images/events/' . basename((string) $event['image'])
            : '';
        ?>
        <?php if ($imageFile !== '' && is_file($imageFile)): ?>
            <img src="<?= e(UPLOAD_URL . '/' . rawurlencode(basename((string) $event['image']))) ?>" alt="">
        <?php else: ?>
            <div class="image-placeholder" aria-hidden="true"><span><?= e(substr($event['category_name'], 0, 1)) ?></span></div>
        <?php endif; ?>
    </a>
    <button class="save-event-button" type="button" data-save-event data-event-id="<?= (int) $event['id'] ?>" data-event-title="<?= e($event['title']) ?>" aria-pressed="false" aria-label="Save <?= e($event['title']) ?> for later">
        <span class="save-event-heart" aria-hidden="true">♡</span>
    </button>
    <div class="event-card-body">
        <div class="card-badges">
            <span class="category-tag"><?= e($event['category_name']) ?></span>
            <span class="free-tag">FREE</span>
            <?php if ($seatsRemaining === 0): ?>
                <span class="full-tag">Full · Join waitlist</span>
            <?php elseif ($isLowSeatCount): ?>
                <span class="low-seat-tag">Limited seats</span>
            <?php endif; ?>
        </div>
        <h3><a href="<?= e(BASE_URL) ?>/?page=events&amp;action=show&amp;id=<?= (int) $event['id'] ?>"><?= e($event['title']) ?></a></h3>
        <p class="event-meta"><?= e(formatDate($event['event_date'])) ?> · <?= e(formatTime($event['start_time'])) ?></p>
        <p class="event-location"><?= e($event['venue_name']) ?>, <?= e($event['region_name']) ?></p>
        <div class="event-card-footer">
            <?php if ($seatsRemaining === 0): ?>
                <span class="seat-count-full">Waitlist available</span>
            <?php else: ?>
                <span class="<?= $isLowSeatCount ? 'seat-count-low' : '' ?>"><?= $isLowSeatCount ? 'Only ' : '' ?><?= $seatsRemaining ?> seats left</span>
            <?php endif; ?>
            <a class="text-link" href="<?= e(BASE_URL) ?>/?page=events&amp;action=show&amp;id=<?= (int) $event['id'] ?>">View event →</a>
        </div>
    </div>
</article>
