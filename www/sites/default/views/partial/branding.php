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

<div id="masthead" class="container">
    <div id="header">
        <div id="logo">
            <a name="home" href="">        
                <h1 id="site-title">Open Supply Chains</h1>
                <img src="/assets/images/logo.png" alt="Open Supply Chains" />
            </a>
        </div>
        <ul id="navigation">
            <li> <a href="browse">Browse</a> </li>
            <li> <a href="create">Create</a> </li>
            <?php if($current_user = Auth::instance()->get_user()): ?>
            <li>
				<div> <a href="/home">Dashboard</a> </div>
                <a class="existing-login" href="auth/logout">Sign out</a>
            </li>
            <?php else: ?>
            <li class="register">
                <div class="button"> <a href="/register">Register</a> </div>
                <a class="existing-login" href="auth">Sign in</a>
            </li>
        	<?php endif; ?>
        </ul>
        <form method="post" action="/search/">
            <div id="search-div">
                <input id="search" name="q" placeholder="Search" results="0" />
            </div>
        </form>
        <div id="search-results"></div>
        <div class="clear"></div>
    </div>    
</div> <!-- #masthead -->
