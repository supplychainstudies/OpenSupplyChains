<?php if(!isset($current_user) || !$current_user): ?>
<div class="container_16">
    <div class="grid_5">
        <h2>Log in</h2>
        <form name="auth-login" method="post" action="auth/login">
        <label for="username">Username:</label><br />
        <input type="text" name="username" class="input text" /><br />
        <label for="password">Password:</label><br />
        <input type="password" name="password" class="input text password" /><br />
        <a href="auth/forgot_password">Forgot your password?</a><br />
        <?php if (isset($_GET['next'])): ?>
        <input type="hidden" name="next" value="<?= $_GET['next']; ?>" /> 
        <?php endif; ?>
        <input type="submit" value="Sign in" />
        </form>
        <?php else: ?>
        <h2>You&apos;re logged in as <?= $current_user->username ?>.</h2>
        <?php endif; ?>
    </div>
</div>
