<?php if(isset($current_user) || $current_user): ?>
<form name="auth-reset-password" method="post" action="auth/reset_password">
<label for="old">Old Password:</label><br />
<input type="password" name="old" class="input text password" /><br />
<label for="new">New Password:</label><br />
<input type="password" name="new" class="input text password" /><br />
<input type="submit" value="Send Password" />
</form>
<?php else: ?>
  <?php $this->request->redirect('auth/login'); ?>
<?php endif; ?>

