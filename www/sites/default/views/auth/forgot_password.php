<div class="container form-page">
    <div class="copy-section">
        <h1>Password Request</h1>
        <p>Forgot your password? Enter the email address you used to register for Sourcemap and we'll send you a link to reset your password.</p>
        <ul>
            <li><a href="/auth">Sign in to Sourcemap</a></li>
            <li><a href="/register">Register a new account</a></li>
        </ul>
    </div>
    <div class="box-section">
        <div class="sourcemap-form">
	        <fieldset>
	        <form name="forgot" method="post" action="auth/forgot">
		    	<label for="email">Email:</label>    
		        <input type="text" id="email" name="email" class="required" value="" />
	            <input class="button" type="submit" value="Request"/>
	        </form>
	        </fieldset>
	    </div>
    </div>
	<div class="clear"></div>
</div>