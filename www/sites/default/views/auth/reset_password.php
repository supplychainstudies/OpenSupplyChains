<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/ 
?>


<div id="page-title">
    <div class="container">
        <h1>Reset password for <?= $current_user ?></h1>
    </div>
</div>

<div class="container form-page">
    <div class="copy-section">
        <p>Enter your new password to the right:</p>
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
