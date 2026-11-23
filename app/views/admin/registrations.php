<?php
$title = 'Manage registrations';
$activeAdmin = 'registrations';
require APP_ROOT . '/app/views/layouts/admin_header.php';
$statuses = REGISTRATION_STATUSES;
?>
<section class="admin-page">
    <div class="admin-page-heading">
        <div><p class="admin-eyebrow">Registration management</p><h1>Registrations</h1></div>
        <div class="filter-counts">
            <span><strong><?= (int) $result['confirmed'] ?></strong> confirmed</span>
            <span><strong><?= (int) $result['waitlisted'] ?></strong> waitlisted</span>
        </div>
    </div>

    <form class="admin-filter-form registration-filter-form" method="get" action="<?= e(BASE_URL) ?>/index.php">
        <input type="hidden" name="page" value="admin">
        <input type="hidden" name="action" value="registrations">
        <div class="form-field">
            <label for="registration_search">Search attendee</label>
            <input id="registration_search" name="search" type="search" maxlength="150" value="<?= e($filters['search']) ?>" placeholder="Name or email">
        </div>
        <div class="form-field">
            <label for="registration_event">Event</label>
            <select id="registration_event" name="event_id">
                <option value="">All events</option>
                <?php foreach ($eventList as $event): ?>
                    <option value="<?= (int) $event['id'] ?>" <?= (string) $event['id'] === $filters['event_id'] ? 'selected' : '' ?>><?= e($event['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="registration_status">Status</label>
            <select id="registration_status" name="status">
                <option value="">All statuses</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= $status === $filters['status'] ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button button-primary" type="submit">Filter</button>
    </form>

    <?php if ($result['items']): ?>
        <div class="responsive-table">
            <table>
                <thead><tr><th>Attendee</th><th>Event</th><th>County</th><th>Registered</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($result['items'] as $row): ?>
                    <tr>
                        <td data-label="Attendee"><?= e($row['user_name']) ?><br><small><?= e($row['email']) ?></small></td>
                        <td data-label="Event"><?= e($row['event_title']) ?><br><small><?= e(formatDate($row['event_date'])) ?></small></td>
                        <td data-label="County"><?= e($row['region_name']) ?></td>
                        <td data-label="Registered"><?= e(formatDate(substr($row['created_at'], 0, 10))) ?></td>
                        <td data-label="Status">
                            <form class="inline-status-form" method="post" action="<?= e(BASE_URL) ?>/?page=admin&amp;action=registrationStatus">
                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="registration_id" value="<?= (int) $row['id'] ?>">
                                <label class="visually-hidden" for="status_<?= (int) $row['id'] ?>">Status for <?= e($row['user_name']) ?></label>
                                <select id="status_<?= (int) $row['id'] ?>" name="status">
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?= e($status) ?>" <?= $status === $row['status'] ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="button button-secondary button-small" type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state"><h2>No registrations found</h2><p>Try another search or filter.</p></div>
    <?php endif; ?>

    <?php $totalPages = max(1, (int) ceil($result['total'] / 10)); if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Registration pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=registrations&amp;event_id=<?= e($filters['event_id']) ?>&amp;status=<?= e($filters['status']) ?>&amp;search=<?= e(rawurlencode($filters['search'])) ?>&amp;page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>
</main></div></body></html>
