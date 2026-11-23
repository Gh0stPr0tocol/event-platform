<?php $title='Dashboard';require APP_ROOT.'/app/views/layouts/header.php'; ?>
<section class="dashboard-section"><div class="container">
<div class="dashboard-welcome"><p class="eyebrow">My Tukutane</p><h1>Welcome, <?=e(explode(' ',(string)$user['name'])[0])?></h1></div>
<div class="stat-grid">
<article class="stat-card"><span>Upcoming registrations</span><strong><?= (int)$upcomingCount ?></strong></article>
<article class="stat-card"><span>Attended</span><strong><?= (int)$attendedCount ?></strong></article>
<article class="stat-card"><span>Cancelled</span><strong><?= (int)$cancelledCount ?></strong></article>
</div>
<section><h2>Your next event</h2>
<?php if($next):?><article class="next-event"><span class="status status-<?=e($next['status'])?>"><?=e(ucfirst($next['status']))?></span><h3><?=e($next['title'])?></h3><p><?=e(formatDate($next['event_date']))?> · <?=e(formatTime($next['start_time']))?></p><p><?=e($next['venue_name'])?>, <?=e($next['region_name'])?></p></article>
<?php else:?><div class="empty-state"><h3>No upcoming event</h3><p>Explore an event and register for it.</p></div><?php endif;?></section>
<section class="local-events"><h2>Events in your county</h2>
<?php if($localEvents):?><div class="event-grid"><?php foreach($localEvents as $event){require APP_ROOT.'/app/views/events/_card.php';}?></div>
<?php else:?><div class="empty-state"><h3>No events in your county</h3><p>Check other counties in Explore events.</p></div><?php endif;?>
</section></div></section>
<?php require APP_ROOT.'/app/views/layouts/footer.php'; ?>
