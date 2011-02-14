<?php
$username = isset($username) ? HTML::chars($username) : '';
?><form name="login" method="post">
    <label for="username">Username or Email:</label><br />
    <input type="text" class="input-text username" name="username" value=/><br />
    <label for="password">Password:</label><br />
    <input type="password" class="input-text input-password password" name="password" /><br />
    <input type="submit" value="login" />
</form>
