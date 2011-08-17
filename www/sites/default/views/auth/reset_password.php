<div class="grids">
    <div class="container_16">
        <div class="form">
            <fieldset>
            <legend>Forgot your password?</legend>
            <form name="auth-reset-password" method="post" action="auth/reset">
            <label for="new">New Password:</label><br />
            <input type="password" name="new" class="input text password" /><br />
            <label for="new">New Password (again):</label><br />
            <input type="password" name="new_confirm" class="input text password" /><br />
            <?php if(isset($ticket) && $ticket): ?><input type="hidden" name="t" value="<?= HTML::chars($ticket) ?>" /><?php endif; ?>
            <input type="submit" value="Reset" />
            </form>
            </fieldset>
        </div>
    </div>
</div>
