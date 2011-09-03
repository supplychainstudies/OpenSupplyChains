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