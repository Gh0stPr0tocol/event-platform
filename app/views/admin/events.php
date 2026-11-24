<?php
$title = 'Manage events';
$activeAdmin = 'events';
require APP_ROOT . '/app/views/layouts/admin_header.php';
?>
<section class="admin-page">
    <div class="admin-page-heading">
        <div><p class="admin-eyebrow">Event management</p><h1>Events</h1></div>
        <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=eventCreate">Add event</a>
    </div>

    <form class="admin-filter-form" method="get" action="<?= e(BASE_URL) ?>/index.php">
        <input type="hidden" name="page" value="admin">
        <input type="hidden" name="action" value="events">
        <div class="form-field"><label for="search">Search title</label><input id="search" name="search" type="search" value="<?= e($filters['search']) ?>" maxlength="150"></div>
        <div class="form-field"><label for="category">Category</label><select id="category" name="category_id"><option value="">All categories</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= (string) $category['id'] === $filters['category_id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></div>
        <div class="form-field"><label for="county">County</label><select id="county" name="region_id"><option value="">All counties</option><?php foreach ($regions as $region): ?><option value="<?= (int) $region['id'] ?>" <?= (string) $region['id'] === $filters['region_id'] ? 'selected' : '' ?>><?= e($region['name']) ?></option><?php endforeach; ?></select></div>
        <div class="form-field"><label for="time">Date</label><select id="time" name="time"><option value="">All events</option><option value="upcoming" <?= $filters['time'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option><option value="past" <?= $filters['time'] === 'past' ? 'selected' : '' ?>>Past</option></select></div>
        <button class="button button-primary" type="submit">Filter</button>
    </form>

    <?php if ($result['items']): ?>
        <div class="responsive-table">
            <table>
                <thead><tr><th>Event</th><th>Date</th><th>Venue / county</th><th>Category</th><th>Seats left</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($result['items'] as $event): ?>
                    <tr>
                        <td data-label="Event"><?= e($event['title']) ?></td>
                        <td data-label="Date"><?= e(formatDate($event['event_date'])) ?><br><?= e(formatTime($event['start_time'])) ?></td>
                        <td data-label="Venue"><?= e($event['venue_name']) ?><br><?= e($event['region_name']) ?></td>
                        <td data-label="Category"><?= e($event['category_name']) ?></td>
                        <td data-label="Seats"><?= (int) $event['seats_remaining'] ?></td>
                        <td data-label="Actions">
                            <div class="table-actions">
                                <a class="button button-secondary button-small" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=eventEdit&amp;id=<?= (int) $event['id'] ?>">Edit</a>
                                <form method="post" action="<?= e(BASE_URL) ?>/?page=admin&amp;action=eventDelete" onsubmit="return confirm('Delete this event? All of its registrations will be removed.')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                                    <button class="button button-danger button-small" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state"><h2>No events found</h2><p>Try another filter or add an event.</p></div>
    <?php endif; ?>

    <?php $totalPages = max(1, (int) ceil($result['total'] / 10)); if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Event pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=events&amp;search=<?= e(rawurlencode($filters['search'])) ?>&amp;category_id=<?= e($filters['category_id']) ?>&amp;region_id=<?= e($filters['region_id']) ?>&amp;time=<?= e($filters['time']) ?>&amp;p=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>
</main>
</div>
</body>
</html>