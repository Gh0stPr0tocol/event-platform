<?php
$title = $event ? 'Edit event' : 'Add event';
$activeAdmin = 'events';
require APP_ROOT . '/app/views/layouts/admin_header.php';
$values = $old ?? [];
$selectedVenueId = (string) ($values['venue_id'] ?? $event['venue_id'] ?? '');
$selectedRegionId = '';
foreach ($venues as $venue) {
    if ((string) $venue['id'] === $selectedVenueId) {
        $selectedRegionId = (string) $venue['region_id'];
        break;
    }
}
$venueData = array_map(
    static fn (array $venue): array => [
        'id' => (int) $venue['id'],
        'name' => $venue['name'],
        'region_id' => (int) $venue['region_id'],
        'region_name' => $venue['region_name'],
    ],
    $venues
);
?>
<section class="admin-page">
    <div class="admin-page-heading">
        <div><p class="admin-eyebrow">Event details</p><h1><?= $event ? 'Edit event' : 'Add event' ?></h1></div>
        <a class="button button-secondary" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=events">Back to events</a>
    </div>
    <form class="admin-form" method="post" enctype="multipart/form-data" action="<?= e(BASE_URL) ?>/?page=admin&amp;action=<?= $event ? 'eventUpdate' : 'eventStore' ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <?php if ($event): ?><input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>"><?php endif; ?>
        <?php if (!empty($errors['general'])): ?><div class="form-error"><?= e($errors['general']) ?></div><?php endif; ?>
        <div class="form-field">
            <label for="title">Title</label>
            <input id="title" name="title" required maxlength="150" value="<?= e($values['title'] ?? '') ?>">
            <?php if (!empty($errors['title'])): ?><span class="field-error"><?= e($errors['title']) ?></span><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="description">Description</label>
            <textarea id="description" name="description" required maxlength="5000"><?= e($values['description'] ?? '') ?></textarea>
            <?php if (!empty($errors['description'])): ?><span class="field-error"><?= e($errors['description']) ?></span><?php endif; ?>
        </div>
        <div class="admin-form-grid">
            <div class="form-field">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= (string) $category['id'] === (string) ($values['category_id'] ?? '') ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?>
                </select>
                <?php if (!empty($errors['category_id'])): ?><span class="field-error"><?= e($errors['category_id']) ?></span><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="venue_region">County</label>
                <select id="venue_region" data-venue-region>
                    <option value="">All counties</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= (int) $region['id'] ?>" <?= (string) $region['id'] === $selectedRegionId ? 'selected' : '' ?>><?= e($region['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="venue_id">Venue</label>
                <select id="venue_id" name="venue_id" required data-venue-select data-venues="<?= e(json_encode($venueData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?: '[]') ?>">
                    <option value="">Select venue</option>
                    <?php foreach ($venues as $venue): ?><option value="<?= (int) $venue['id'] ?>" <?= (string) $venue['id'] === (string) ($values['venue_id'] ?? '') ? 'selected' : '' ?>><?= e($venue['name']) ?> - <?= e($venue['region_name']) ?></option><?php endforeach; ?>
                </select>
                <p class="field-hint" data-venue-hint aria-live="polite" hidden></p>
                <?php if (!empty($errors['venue_id'])): ?><span class="field-error"><?= e($errors['venue_id']) ?></span><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="event_date">Date</label>
                <input id="event_date" name="event_date" type="date" required <?= $event ? '' : 'min="' . date('Y-m-d') . '"' ?> value="<?= e($values['event_date'] ?? '') ?>">
                <?php if (!empty($errors['event_date'])): ?><span class="field-error"><?= e($errors['event_date']) ?></span><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="start_time">Start time</label>
                <input id="start_time" name="start_time" type="time" required value="<?= e($values['start_time'] ?? '') ?>">
                <?php if (!empty($errors['start_time'])): ?><span class="field-error"><?= e($errors['start_time']) ?></span><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="capacity">Capacity</label>
                <input id="capacity" name="capacity" type="number" required min="1" max="10000" value="<?= e($values['capacity'] ?? '') ?>">
                <?php if (!empty($errors['capacity'])): ?><span class="field-error"><?= e($errors['capacity']) ?></span><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="image">Event image (JPG, PNG or WebP, max 2 MB)</label>
                <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <?php if (!empty($errors['image'])): ?><span class="field-error"><?= e($errors['image']) ?></span><?php endif; ?>
                <?php if (!empty($event['image'])): ?><small>Current: <?= e($event['image']) ?></small><?php endif; ?>
            </div>
        </div>
        <button class="button button-primary" type="submit"><?= $event ? 'Save changes' : 'Create event' ?></button>
    </form>
</section>
</main>
</div>
<script defer src="<?= e(BASE_URL) ?>/js/filters.js?v=<?= (int) filemtime(APP_ROOT . '/public/js/filters.js') ?>"></script>
</body>
</html>
