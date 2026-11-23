<?php
$title = 'Manage categories';
$activeAdmin = 'categories';
require APP_ROOT . '/app/views/layouts/admin_header.php';
$formCategory = $editCategory ?? null;
$page = $page ?? 1;
?>
<section class="admin-page">
    <div class="admin-page-heading">
        <div><p class="admin-eyebrow">Reference data</p><h1>Categories</h1></div>
    </div>

    <form class="admin-form category-form" method="post" action="<?= e(BASE_URL) ?>/?page=admin&amp;action=<?= $formCategory ? 'categoryUpdate' : 'categoryStore' ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <?php if ($formCategory): ?><input type="hidden" name="category_id" value="<?= (int) $formCategory['id'] ?>"><?php endif; ?>
        <?php if (!empty($errors['general'])): ?><div class="form-error"><?= e($errors['general']) ?></div><?php endif; ?>
        <div class="form-field">
            <label for="category_name"><?= $formCategory ? 'Edit category name' : 'New category name' ?></label>
            <input id="category_name" name="name" required maxlength="100" value="<?= e($old['name'] ?? ($formCategory['name'] ?? '')) ?>">
            <?php if (!empty($errors['name'])): ?><span class="field-error"><?= e($errors['name']) ?></span><?php endif; ?>
        </div>
        <button class="button button-primary" type="submit"><?= $formCategory ? 'Save category' : 'Add category' ?></button>
        <?php if ($formCategory): ?><a class="button button-secondary" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=categories">Cancel</a><?php endif; ?>
    </form>

    <?php if ($categories): ?>
        <div class="responsive-table">
            <table>
                <thead><tr><th>Name</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td data-label="Name"><?= e($category['name']) ?></td>
                        <td data-label="Actions">
                            <div class="table-actions">
                                <a class="button button-secondary button-small" href="<?= e(BASE_URL) ?>/?page=admin&amp;action=categories&amp;edit=<?= (int) $category['id'] ?>">Edit</a>
                                <form method="post" action="<?= e(BASE_URL) ?>/?page=admin&amp;action=categoryDelete" onsubmit="return confirm('Delete this category?')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>">
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
        <div class="empty-state"><h2>No categories yet</h2><p>Add a category using the form above.</p></div>
    <?php endif; ?>
    <?php $totalPages=max(1,(int)ceil(($categoryTotal ?? count($categories))/10));if($totalPages>1):?>
        <nav class="pagination" aria-label="Category pages"><?php for($i=1;$i<=$totalPages;$i++):?><a class="<?=$i===$page?'active':''?>" href="<?=e(BASE_URL)?>/?page=admin&amp;action=categories&amp;page=<?=$i?>"><?=$i?></a><?php endfor;?></nav>
    <?php endif;?>
</section>
</main></div></body></html>
