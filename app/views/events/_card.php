<article class="event-card">
    <a class="event-card-image" href="<?= e(BASE_URL) ?>/?page=events&amp;action=show&amp;id=<?= (int) $event['id'] ?>" aria-label="View <?= e($event['title']) ?>">
        <?php if (!empty($event['image'])): ?>
            <img src="<?= e(UPLOAD_URL . '/' . $event['image']) ?>" alt="">
        <?php else: ?>
            <div class="image-placeholder" aria-hidden="true"><span><?= e(substr($event['category_name'], 0, 1)) ?></span></div>
        <?php endif; ?>
    </a>
    <div class="event-card-body">
        <div class="card-badges">
            <span class="category-tag"><?= e($event['category_name']) ?></span>
            <span class="free-tag">FREE</span>
        </div>
        <h3><a href="<?= e(BASE_URL) ?>/?page=events&amp;action=show&amp;id=<?= (int) $event['id'] ?>"><?= e($event['title']) ?></a></h3>
        <p class="event-meta"><?= e(formatDate($event['event_date'])) ?> · <?= e(formatTime($event['start_time'])) ?></p>
        <p class="event-location"><?= e($event['venue_name']) ?>, <?= e($event['region_name']) ?></p>
        <div class="event-card-footer">
            <span><?= (int) $event['seats_remaining'] ?> seats left</span>
            <a class="text-link" href="<?= e(BASE_URL) ?>/?page=events&amp;action=show&amp;id=<?= (int) $event['id'] ?>">View event →</a>
        </div>
    </div>
</article>
