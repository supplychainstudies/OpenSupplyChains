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

<div class="container">
    <div id="header">
        <div id="logo">
            <a name="home" href="">        
                <h1 id="site-title">Sourcemap</h1>
                <img src="/assets/images/logo.png" alt="sourcemap" />
            </a>
        </div>
        <ul id="navigation">
            <li> <a href="browse">Browse</a> </li>
            <li> <a href="create">Create</a> </li>
            <?php if($current_user = Auth::instance()->get_user()): ?>
            <?php // logic belongs elsewhere
                $isChannel = false;
                $channel_role = ORM::factory('role')->where('name', '=', 'channel')->find();
                if($current_user->has('roles', $channel_role))
                    $isChannel = true;
            ?> 
            <li>
                <div> <a href="/home">Dashboard</a></div> 
                <a id="existing-login" href="auth/logout">Sign out</a>
            </li>
            <?php else: ?>
            <li class="register last">
                <a class="button" href="/register">Register</a>
                <a id="existing-login" href="auth">Sign in</a>
            </li>
            <?php endif; ?>
        </ul>
        <form action="/search">
            <div id="search-div" class="pseudo-form">
                <input id="search" name="sq" placeholder="Search" value="<?= isset($_GET['sq']) ? $_GET['sq'] : "";?>" results="0" />
            </div>
        </form>
        <div id="search-results">
            <ul id="live-search-results">
                <script type="text/html" id="search-result-template"> 
                    <![CDATA[
                        <li>
                            <div class="preview-map-item vertical">
                                <a class="search-link" href="view/<%= this.id %>"></a>
                                <div class="preview-badge">
                                    <img class="preview-map thumb" src="static/<%= this.id %>.s.png" alt="" />
                                </div>
                                <h3 class="preview-title list">
                                    <a><%= this.title %></a>
                                </h3>
                                <div>
                                    <h4 class="preview-author">
                                        <a href="user/<%= this.author %>">
                                            <%= this.author %>, <%= this.date %>
                                     </h4>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </li>
                    ]]>
                </script>
            </ul>
        </div>
        <div class="clear"></div>
    </div>    
</div>    
