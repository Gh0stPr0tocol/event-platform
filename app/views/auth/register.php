<?php
$title = 'Create account';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<section class="auth-section">
    <div class="container auth-container">
        <div class="auth-card">
            <p class="eyebrow">Join Tukutane</p>
            <h1>Create your account</h1>
            <p class="auth-intro">Register free for community events across Kenya.</p>
            <?php if (!empty($errors['general'])): ?><div class="form-error"><?= e($errors['general']) ?></div><?php endif; ?>
            <?php if (!empty($errors['csrf'])): ?><div class="form-error"><?= e($errors['csrf']) ?></div><?php endif; ?>
            <form method="post" action="<?= e(BASE_URL) ?>/?page=auth&amp;action=register" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <div class="form-field">
                    <label for="name">Full name</label>
                    <input id="name" name="name" value="<?= e($old['name'] ?? '') ?>" required>
                    <?php if (!empty($errors['name'])): ?><span class="field-error"><?= e($errors['name']) ?></span><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" required>
                    <?php if (!empty($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="phone">Phone number</label>
                    <input id="phone" name="phone" type="tel" value="<?= e($old['phone'] ?? '') ?>" required>
                    <?php if (!empty($errors['phone'])): ?><span class="field-error"><?= e($errors['phone']) ?></span><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="region">County</label>
                    <select id="region" name="region_id" required>
                        <option value="">Select your county</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= (int) $region['id'] ?>" <?= (string) ($old['region_id'] ?? '') === (string) $region['id'] ? 'selected' : '' ?>><?= e($region['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['region'])): ?><span class="field-error"><?= e($errors['region']) ?></span><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" minlength="8" required>
                    <?php if (!empty($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
                    <?php if (!empty($errors['password_confirmation'])): ?><span class="field-error"><?= e($errors['password_confirmation']) ?></span><?php endif; ?>
                </div>
                <button class="button button-primary button-full" type="submit">Create account</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="<?= e(BASE_URL) ?>/?page=auth&amp;action=login">Log in</a></p>
        </div>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>