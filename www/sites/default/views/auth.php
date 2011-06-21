<?php if(!isset($current_user) || !$current_user): ?>
<div class="container_16">
<div class="grid_9">
<div class="sourcemap-form">
  <fieldset> 
    <legend>Log in</legend> 
    <form name="auth-login" class="form" method="post" action="auth/login">
        <label for="username">Username</label> 
        <div class="sourcemap-form-textbox"> 
            <input name="username" type="text" class="username" id="username" value="username" /> 
        </div> 
        <label for="password">Password</label> 
        <div class="sourcemap-form-textbox"> 
            <input name="password" type="password" class="password" id="password" value="password" /> 
        </div> 
        <?php if (isset($_GET['next'])): ?>
            <input type="hidden" name="next" value="<?= $_GET['next']; ?>" /> 
        <?php endif; ?>
        <div class="sourcemap-form-button-div"> 
            <input name="Submit" type="submit" value="Submit" class="buttons" /> 
        </div> 
    </form>
    </fieldset>
</div>
</div>
</div>

<?php else: ?>
<h2>You&apos;re logged in as <?= $current_user->username ?>.</h2>
<?php endif; ?>
