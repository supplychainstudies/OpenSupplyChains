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
    <h1 class="dashboard-title">Your profile</h1>
    <div class="dashboard-top">
        <div class="dashboard-top-left">
            <div>
                <h2 class="user-name"><?= HTML::chars($user->username) ?></h2>       
            </div>
            <hr />
            <div id="user-profile">
                <div class="user-gravatar">
                    <img src="<?= Gravatar::avatar($user->email, 128) ?>" />
                </div>
                <ul class="user-details">
                    <li>Username: <span><?= HTML::chars($user->username) ?></span><li>
                    <li>Email: <span><?= HTML::chars($user->email) ?></span><li>    
                    <li>Last Signed In: <span><?= date('F j, Y', $user->last_login) ?></span><li>
                </ul>
            </div>
            <div class="clear"></div>
            <div>
                <div class="upload-photo button"><a href="http://www.gravatar.com/<?= Gravatar::hash($user->email) ?>">Change photo</a></div> <div class="reset-password button"><a href="auth/reset">Change Password</a></div>
            </div>
        </div>
        <div class="dashboard-top-right">
            <div>
                <h2>Recent Activity</h2>
            </div>
            <hr />
            <div id="user-stream">
                <?php if(isset($user_event_stream)): ?>
                <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>

<div class="search-results container">
    <?php if(isset($supplychains) && $supplychains): ?>
        <h2>Your Supply Chains</h2>
        <?php foreach($supplychains as $i => $sc): ?>
            <div class="user-map-list">
                <?= View::factory('partial/home/map', array('supplychain' => $sc)) ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h2 class="bad-news">You haven't made any maps yet. <a href="create">Get started</a></h2>
    <?php endif; ?>
</div>
