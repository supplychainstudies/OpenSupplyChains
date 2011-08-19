<div class="container">
	<div class="login-copy">
        <h1>Password Reset</h1>
        <p>Enter your new password below.</p><br/>
        <ul>
            <li><a href="/auth">Sign in to Sourcemap</a></li>
            <li><a href="/register">Register a new account</a></li>
        </ul>
    </div>
    <div class="login-box">
	    <h1>&nbsp;</h1>
	    <div class="sourcemap-form">
            <fieldset>
            <form name="auth-reset-password" method="post" action="auth/reset">
	            <label for="new">New Password:</label>
				<div class="sourcemap-form-textbox">	
	            	<input type="password" name="new" class="input text password" />
				</div><br />
	            <label for="new_confirm">New Password (Repeat):</label>
				<div class="sourcemap-form-textbox">	
	            	<input type="password" name="new_confirm" class="input text password" />
				</div><br />
	            <?php if(isset($ticket) && $ticket): ?><input type="hidden" name="t" value="<?= HTML::chars($ticket) ?>" /><?php endif; ?>
	            <input class="button" type="submit" class="submit" value="Reset" />
            </form>
            </fieldset>
	    </div>
	</div>
</div>