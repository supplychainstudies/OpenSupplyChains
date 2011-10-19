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
    <div class="dashboard-top">
        <div class="dashboard-top-left">
            <h2 class="user-name"><?= isset($user->display_name) ? HTML::chars($user->display_name) : HTML::chars($user->username) ?></h2>       
            <div id="user-profile">
                <div class="user-gravatar">
                    <img src="<?= Gravatar::avatar($user->email, 128) ?>" />
                </div>
                <ul class="user-details">
                    <li><span><?= HTML::chars($user->username); ?></span></li>
                    <li><span><?= HTML::chars($user->email) ?></span></li>
                    <?php if ($isChannel): ?>
                    <li>
                        <a href="#" title="display_name" class="edit-button"></a>
                        <p id="display_name"><?= $user->display_name ? HTML::chars($user->display_name) : "<span class=\"empty\">No display name yet!</span>"; ?></p>
                    </li>
                    <li>
                        <a href="#" title="banner_url" class="edit-button"></a>
                        <p id="banner_url"><?= isset($user->banner_url) ? HTML::chars($user->banner_url) : "<span class=\"empty\">No banner URL yet!</span>"; ?></p>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="#" title="url" class="edit-button"></a>
                        <p id="url"><?= isset($user->url) ? HTML::chars($user->url) : "<span class=\"empty\">No URL yet!</span>"; ?></p>
                    </li>
                    <li>
                        <a href="#" title="description" class="edit-button"></a>
                        <p id="description"><?= isset($user->description) ? HTML::chars($user->description) : "<span class=\"empty\">No description yet!</span>"; ?></p> 
                    </li>
                    <li>Last Signed In: <span><?= date('F j, Y', $user->last_login) ?></span></li>
                </ul>
            </div>
            <div class="clear"></div>
            <div class="change-info">
                <div class="button">
                    <a href="http://www.gravatar.com/<?= Gravatar::hash($user->email) ?>">Change photo</a>
                </div> 
                <div class="button">
                    <a href="auth/reset">Change Password</a>
                </div>
            </div>
        </div>
        <div class="dashboard-top-right">
            <h2>Recent Activity</h2>
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
<div class="clear" style="height: 20px"></div>
<div class="container"> 
<div class="account-alert"> 
<p> 
Did you make any maps on sourcemap.org? Email <a href="mailto:account-migration@sourcemap.com">account-migration@sourcemap.com</a> to import them into your new dashboard.
</p> 
</div> 
</div> 
<div class="clear">&nbsp;</div> 

<div class="search-results container">
    <?php if(isset($supplychains) && $supplychains): ?>
        <h2>Your Sourcemaps</h2>
        <?php foreach($supplychains as $i => $sc): ?>
            <div class="user-map-list">
                <?= View::factory('partial/home/map', array('supplychain' => $sc , 'user_id'=> $user_profile )) ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h2 class="bad-news">You haven't made any maps yet. <a href="create">Get started</a></h2>
    <?php endif; ?>
    <?php if(isset($favorites) && $favorites): ?>
        <h2>Favorite</h2>
        <?php foreach($favorites as $i => $sc): ?>
            <div class="user-map-list">
                <?= View::factory('partial/home/map', array('supplychain' => $sc , 'user_id'=> $user_profile )) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
