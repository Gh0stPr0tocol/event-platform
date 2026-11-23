<?php
$title = 'Log in';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<section class="auth-section">
    <div class="container auth-container">
        <div class="auth-card">
            <p class="eyebrow">Welcome back</p>
            <h1>Log in to Tukutane</h1>
            <?php if (!empty($errors['general'])): ?><div class="form-error"><?= e($errors['general']) ?></div><?php endif; ?>
            <?php if (!empty($errors['csrf'])): ?><div class="form-error"><?= e($errors['csrf']) ?></div><?php endif; ?>
            <form method="post" action="<?= e(BASE_URL) ?>/?page=auth&amp;action=login">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <div class="form-field">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" required>
                    <?php if (!empty($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>
                    <?php if (!empty($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
                </div>
                <button class="button button-primary button-full" type="submit">Log in</button>
            </form>
            <p class="auth-switch">New to Tukutane? <a href="<?= e(BASE_URL) ?>/?page=auth&amp;action=register">Create an account</a></p>
        </div>
    </div>
</section>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>