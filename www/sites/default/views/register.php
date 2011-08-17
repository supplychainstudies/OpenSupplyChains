<?php if(!isset($current_user) || !$current_user): ?>
		
<div class="container">
    <div class="register-copy">
        <h1>Join Us</h1>
        <p>Register to create sourcemaps, leave comments, save favorites, and stay informed about our work.</p><br/>
        <ul>
            <li>Already have an account? <a href="/auth">Sign in here</a>.</li>
        </ul>
    </div>
    <div class="register-box">
        <h1>&nbsp;</h1>
        <?= $form ?>
    </div>
</div>

<?php else: ?>
<h2>You&apos;re signed in as <?= $current_user->username ?>.</h2>
<?php endif; ?>
