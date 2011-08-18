<div class="container">
	<div class="login-copy">
        <h1>Password Request</h1>
        <p>Forgot your password? Enter the email address you used to register for Sourcemap and we'll send you a link to reset your password.</p><br/>
        <ul>
            <li><a href="/auth">Sign in to Sourcemap</a></li>
            <li><a href="/register">Register a new account</a></li>
        </ul>
    </div>
    <div class="login-box">
	    <h1>&nbsp;</h1>
	    <div class="sourcemap-form">
	        <fieldset>
	        <form name="create" method="post" action="auth/forgot">
		    	<label for="email">Email:</label>    
				<div class="sourcemap-form-textbox">
		            <input type="text" id="email" name="email" class="required" value="" />
				</div><br/>
	            <input class="button" type="submit" class="submit" value="Request"/>
	        </form>
	        </fieldset>
	    </div>
	</div>
</div>
