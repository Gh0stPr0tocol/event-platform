<?php
$title = 'Manage venues';
$activeAdmin = 'venues';
require APP_ROOT . '/app/views/layouts/admin_header.php';
$formVenue = $editVenue ?? null;
$page = $page ?? 1;
?>
<section class="admin-page">
    <div class="admin-page-heading">
        <div><p class="admin-eyebrow">Reference data</p><h1>Venues</h1></div>
    </div>

    <form class="admin-form" method="post" action="<?= e(BASE_URL) ?>/?page=admin&amp;action=<?= $formVenue ? 'venueUpdate' : 'venueStore' ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <?php if ($formVenue): ?><input type="hidden" name="venue_id" value="<?= (int) $formVenue['id'] ?>"><?php endif; ?>
        <?php if (!empty($errors['general'])): ?><div class="form-error"><?= e($errors['general']) ?></div><?php endif; ?>
        <div class="admin-form-grid">
            <div class="form-field">
                <label for="venue_name">Venue name</label>
                <input id="venue_name" name="name" required maxlength="150" value="<?= e($old['name'] ?? ($formVenue['name'] ?? '')) ?>">
                <?php if (!empty($errors['name'])): ?><span class="field-error"><?= e($errors['name']) ?></span><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="venue_address">Address</label>
                <input id="venue_address" name="address" required maxlength="255" value="<?= e($old['address'] ?? ($formVenue['address'] ?? '')) ?>">
                <?php if (!empty($errors['address'])): ?><span class="field-error"><?= e($errors['address']) ?></span><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="venue_region">County</label>
                <select id="venue_region" name="region_id" required>
                    <option value="">Select county</option>
                    <?php foreach ($regions as $region): ?>
                        <?php $selected = (string) ($old['region_id'] ?? ($formVenue['region_id'] ?? '')) === (string) $region['id']; ?>
                        <option value="<?= (int) $region['id'] ?>" <?= $selected ? 'selected' : '' ?>><?= e($region['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['region_id'])): ?><span class="field-error"><?= e($errors['region_id']) ?></span><?php endif; ?>
            </div>
        </div>
        <button class="button button-primary" type="submit"><?= $formVenue ? 'Save venue' : 'Add venue' ?></button>
        <?php if ($formVenue): ?><a class="button button-secondary" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=venues">Cancel</a><?php endif; ?>
    </form>

    <?php if ($venues): ?>
        <div class="responsive-table">
            <table>
                <thead><tr><th>Name</th><th>Address</th><th>County</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($venues as $venue): ?>
                    <tr>
                        <td data-label="Name"><?= e($venue['name']) ?></td>
                        <td data-label="Address"><?= e($venue['address']) ?></td>
                        <td data-label="County"><?= e($venue['region_name']) ?></td>
                        <td data-label="Actions">
                            <div class="table-actions">
                                <a class="button button-secondary button-small" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=venues&amp;edit=<?= (int) $venue['id'] ?>">Edit</a>
                                <form method="post" action="<?= e(BASE_URL) ?>/?page=admin&amp;action=venueDelete" onsubmit="return confirm('Delete this venue?')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="venue_id" value="<?= (int) $venue['id'] ?>">
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
        <div class="empty-state"><h2>No venues yet</h2><p>Add a venue using the form above.</p></div>
    <?php endif; ?>
    <?php $totalPages=max(1,(int)ceil(($venueTotal ?? count($venues))/10));if($totalPages>1):?>
        <nav class="pagination" aria-label="Venue pages"><?php for($i=1;$i<=$totalPages;$i++):?><a class="<?=$i===$page?'active':''?>" href="<?=e(BASE_URL)?>/?page=admin&amp;action=venues&amp;page=<?=$i?>"><?=$i?></a><?php endfor;?></nav>
    <?php endif;?>
</section>
</main></div></body></html>
