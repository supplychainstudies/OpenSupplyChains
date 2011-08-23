<div class="container form-page">
    <div class="copy-section">
        <h1>Password Reset</h1>
        <p>Enter your new password below.</p>
        <ul>
            <li><a href="/auth">Sign in to Sourcemap</a></li>
            <li><a href="/register">Register a new account</a></li>
        </ul>
    </div>
    <div class="box-section">
        <div class="sourcemap-form">
	        <fieldset>
            <form name="auth-reset-password" method="post" action="auth/reset">
	            <label for="new">New Password:</label>
	           	<input type="password" name="new" class="input text password" />
	            
				<label for="new_confirm">New Password (Repeat):</label>
	            <input type="password" name="new_confirm" class="input text password" />
	            <?php if(isset($ticket) && $ticket): ?><input type="hidden" name="t" value="<?= HTML::chars($ticket) ?>" /><?php endif; ?>
	            <input class="button" type="submit" value="Reset" />
            </form>
            </fieldset>
	    </div>
    </div>
	<div class="clear"></div>
</div>
