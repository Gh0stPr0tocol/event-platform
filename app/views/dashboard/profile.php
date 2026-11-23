<?php
$title='Profile';require APP_ROOT.'/app/views/layouts/header.php';
$profileOld=$_SESSION['old_input']??$user;unset($_SESSION['old_input']);
?>
<section class="dashboard-section"><div class="container profile-grid">
<form class="form-card" method="post" action="<?=e(BASE_URL)?>/?page=dashboard&amp;action=updateProfile">
<h1>Edit profile</h1><input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>">
<?php if(!empty($errors['general'])):?><div class="form-error"><?=e($errors['general'])?></div><?php endif;?>
<div class="form-field"><label for="name">Name</label><input id="name" name="name" required maxlength="100" value="<?=e($profileOld['name']??'')?>"><?php if(!empty($errors['name'])):?><span class="field-error"><?=e($errors['name'])?></span><?php endif;?></div>
<div class="form-field"><label for="email">Email</label><input id="email" value="<?=e($user['email'])?>" readonly></div>
<div class="form-field"><label for="phone">Phone</label><input id="phone" name="phone" type="tel" required maxlength="20" value="<?=e($profileOld['phone']??'')?>"><?php if(!empty($errors['phone'])):?><span class="field-error"><?=e($errors['phone'])?></span><?php endif;?></div>
<div class="form-field"><label for="region">Home county</label><select id="region" name="region_id" required><option value="">Select county</option><?php foreach($regions as $region):?><option value="<?=(int)$region['id']?>" <?=(string)$region['id']===(string)($profileOld['region_id']??'')?'selected':''?>><?=e($region['name'])?></option><?php endforeach;?></select><?php if(!empty($errors['region'])):?><span class="field-error"><?=e($errors['region'])?></span><?php endif;?></div>
<button class="button button-primary" type="submit">Save changes</button></form>
<form class="form-card" method="post" action="<?=e(BASE_URL)?>/?page=dashboard&amp;action=changePassword">
<h2>Change password</h2><input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>">
<?php if(!empty($passwordErrors['general'])):?><div class="form-error"><?=e($passwordErrors['general'])?></div><?php endif;?>
<div class="form-field"><label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" required><?php if(!empty($passwordErrors['current_password'])):?><span class="field-error"><?=e($passwordErrors['current_password'])?></span><?php endif;?></div>
<div class="form-field"><label for="new_password">New password</label><input id="new_password" name="new_password" type="password" minlength="8" required><?php if(!empty($passwordErrors['password'])):?><span class="field-error"><?=e($passwordErrors['password'])?></span><?php endif;?></div>
<div class="form-field"><label for="new_password_confirmation">Confirm new password</label><input id="new_password_confirmation" name="new_password_confirmation" type="password" minlength="8" required><?php if(!empty($passwordErrors['new_password_confirmation'])):?><span class="field-error"><?=e($passwordErrors['new_password_confirmation'])?></span><?php endif;?></div>
<button class="button button-primary" type="submit">Change password</button></form>
</div></section><?php require APP_ROOT.'/app/views/layouts/footer.php'; ?>
