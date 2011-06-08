<div class="grids">
    <div class="container_16">
<?php if(isset($email_sent) && $email_sent): ?>
    <a href="auth/reset_password?t=<?= HTML::chars($ticket) ?>">reset your password</a>
<?php else: ?>
        <div class="form">
            <fieldset>
            <legend>Forgot your password?</legend>
            <form name="create" method="post" action="auth/forgot_password">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" class="required" value="" />
                <div class="clear"></div>
                
                <input type="submit" class="submit" value="Go!"/>
            </form>
            </fieldset>
            <div><span class="highlighted">*</span> denotes required field</div>
        </div>
<?php endif; ?>
    </div>
</div>
