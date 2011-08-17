<?php if(!isset($current_user) || !$current_user): ?>
<div class="container">
    <div class="login-copy">
        <h1>Sign in to Sourcemap</h1>
        <p>Sign in to create sourcemaps, leave comments, save favorites, and stay informed about our work.</p><br/>
            <ul>
            <li><a href="/auth/forgot">Forgot your password?</a></li>
            <li><a href="/register">Register a new account</a></li>
        </ul>
    </div>
    <div class="login-box">
        <h1>&nbsp;</h1>
        <div class="sourcemap-form">
          <fieldset> 
            <form name="auth-login" class="form" method="post" action="auth/login">
                <label for="username">Username</label> 
                <div class="sourcemap-form-textbox username"> 
                    <input name="username" type="text" class="username" id="username" placeholder="Username" /> 
                </div> 
                <label for="password">Password</label> 
                <div class="sourcemap-form-textbox password"> 
                    <input name="password" type="password" class="password" id="password" placeholder="Password" /> 
                </div> 
                <?php if (isset($_GET['next'])): ?>
                    <input type="hidden" name="next" value="<?= $_GET['next']; ?>" /> 
                <?php endif; ?>
                <input name="Submit" type="submit" value="Login" class="form-button button" /> 
            </form>
            </fieldset>
        </div>
    </div>
</div>
<div class="clear"></div>
<div class="container">
<?php else: ?>
<h2>You&apos;re signed in as <?= $current_user->username ?>.</h2>
<?php endif; ?>
</div>
