<?php if(!isset($current_user) || !$current_user): ?>
<form name="auth-login" method="post" action="auth/login">
<label for="username">username:</label><br />
<input type="text" name="username" class="input text" /><br />
<label for="password">password:</label><br />
<input type="password" name="password" class="input text password" /><br />
<input type="hidden" name="next" value="<?= $_GET['next']; ?>"/>
<input type="submit" value="sign in" />
</form>
<?php else: ?>
<h2>You&apos;re logged in as <?= $current_user->username ?>.</h2>
<?php endif; ?>
