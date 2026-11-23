<?php
$title = 'Admin dashboard';
$activeAdmin = 'dashboard';
require APP_ROOT . '/app/views/layouts/admin_header.php';
?>
<section class="admin-page">
    <div class="admin-page-heading">
        <div><p class="admin-eyebrow">Overview</p><h1>Admin dashboard</h1></div>
        <a class="button button-primary" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=eventCreate">Add event</a>
    </div>

    <div class="admin-stat-grid">
        <article class="admin-stat"><span>Total events</span><strong><?= (int) $eventStats['total'] ?></strong></article>
        <article class="admin-stat"><span>Upcoming events</span><strong><?= (int) $eventStats['upcoming'] ?></strong></article>
        <article class="admin-stat"><span>Total registrations</span><strong><?= (int) $registrationStats['total'] ?></strong></article>
        <article class="admin-stat"><span>Upcoming seats filled</span><strong><?= (int) $eventStats['percent'] ?>%</strong></article>
        <article class="admin-stat"><span>Waitlisted</span><strong><?= (int) $registrationStats['waitlisted'] ?></strong></article>
    </div>

    <div class="admin-dashboard-grid">
        <section class="admin-panel">
            <h2>Registrations per event</h2>
            <?php if ($busiest): ?>
                <div class="bar-chart" role="img" aria-label="Busiest upcoming events">
                    <?php foreach ($busiest as $row):
                        $count = (int) $row['confirmed_count'];
                        $capacity = max(1, (int) $row['capacity']);
                        $width = min(100, round($count / $capacity * 100));
                    ?>
                        <div class="bar-row">
                            <span class="bar-label"><?= e($row['title']) ?></span>
                            <div class="bar-track"><span class="bar-fill" style="width: <?= $width ?>%"></span></div>
                            <strong><?= $count ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state"><p>No upcoming event registrations.</p></div>
            <?php endif; ?>
        </section>

        <section class="admin-panel">
            <h2>Status breakdown</h2>
            <div class="status-breakdown">
                <?php foreach ($statusCounts as $status => $count): ?>
                    <div><span class="status status-<?= e($status) ?>"><?= e(ucfirst($status)) ?></span><strong><?= (int) $count ?></strong></div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <section class="admin-panel">
        <h2>Recent activity</h2>
        <?php if ($recent): ?>
            <div class="responsive-table">
                <table>
                    <thead><tr><th>Attendee</th><th>Event</th><th>Status</th><th>Registered</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent as $row): ?>
                        <tr>
                            <td data-label="Attendee"><?= e($row['user_name']) ?></td>
                            <td data-label="Event"><?= e($row['event_title']) ?></td>
                            <td data-label="Status"><span class="status status-<?= e($row['status']) ?>"><?= e(ucfirst($row['status'])) ?></span></td>
                            <td data-label="Registered"><?= e(formatDate(substr($row['created_at'], 0, 10))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state"><p>No recent registrations.</p></div>
        <?php endif; ?>
    </section>
</section>
</main>
</div>
</body>
</html>