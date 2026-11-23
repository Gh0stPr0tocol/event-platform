<?php $title='My registrations';require APP_ROOT.'/app/views/layouts/header.php'; ?>
<section class="dashboard-section"><div class="container">
<h1>My registrations</h1>
<nav class="tabs" aria-label="Registration periods">
<a class="<?=$tab==='upcoming'?'active':''?>" href="<?=e(BASE_URL)?>/?page=dashboard&amp;action=registrations&amp;tab=upcoming">Upcoming</a>
<a class="<?=$tab==='past'?'active':''?>" href="<?=e(BASE_URL)?>/?page=dashboard&amp;action=registrations&amp;tab=past">Past</a>
</nav>
<?php if($result['items']):?>
<div class="responsive-table"><table><thead><tr><th>Event</th><th>Date &amp; time</th><th>Venue</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach($result['items'] as $row):?><tr>
<td data-label="Event"><?=e($row['title'])?></td><td data-label="Date"><?=e(formatDate($row['event_date']))?><br><?=e(formatTime($row['start_time']))?></td>
<td data-label="Venue"><?=e($row['venue_name'])?>, <?=e($row['region_name'])?></td>
<td data-label="Status"><span class="status status-<?=e($row['status'])?>"><?=e(ucfirst($row['status']))?></span></td>
<td data-label="Action"><?php if(in_array($row['status'],[STATUS_CONFIRMED,STATUS_WAITLISTED],true)&&$row['event_date']>=date('Y-m-d')):?><form method="post" action="<?=e(BASE_URL)?>/?page=registrations&amp;action=cancel" onsubmit="return confirm('Cancel this registration?')"><input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>"><input type="hidden" name="registration_id" value="<?=(int)$row['id']?>"><button class="button button-danger button-small" type="submit">Cancel</button></form><?php endif;?></td>
</tr><?php endforeach;?></tbody></table></div>
<?php else:?><div class="empty-state"><h2>No registrations yet</h2><p>Explore upcoming events and reserve a free place.</p></div><?php endif;?>
<?php $totalPages=max(1,(int)ceil($result['total']/10));if($totalPages>1):?><nav class="pagination"><?php for($i=1;$i<=$totalPages;$i++):?><a class="<?=$i==$page?'active':''?>" href="<?=e(BASE_URL)?>/?page=dashboard&amp;action=registrations&amp;tab=<?=e($tab)?>&amp;page=<?=$i?>"><?=$i?></a><?php endfor;?></nav><?php endif;?>
</div></section><?php require APP_ROOT.'/app/views/layouts/footer.php'; ?>
