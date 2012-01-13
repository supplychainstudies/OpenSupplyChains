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
<script>
    Sourcemap.username = '<?= $user->username ?>';
    Sourcemap.is_channel = '<?= $isChannel ?>';
</script>
<div class="container">
    <div class="dashboard-top">
        <div class="left">
            <div class="container">
                <h2 class="user-name section-title">
                    <?= isset($user->display_name) ? HTML::chars($user->display_name) : HTML::chars($user->username) ?>
                    <?= $isChannel ? '<span class="secondary">Pro Account</span>' : '' ?>
                    <a class="preview-link" href="user/<?= $user->username ?>?preview">See how this looks to other users</a>
                </h2>       
                <?= View::factory('partial/user/badge', array('user' => $user, 'avatar_url' => $avatar_url, 'isChannel' => $isChannel, 'canEdit' => true)) ?>
                <hr class="spacer" />
            </div>
            <div class="clear"></div>
        </div>
        <div class="right">
            <h2 class="section-title">Recent Activity</h2>
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

<?php if ($isChannel){ ?>
<div class="account-alert notice"> 
    <p> 
        Congratulations!  You're one of our first Pro Accounts.  You now have access to new modes, options, and features.
        <br />We're working to improve our premium services.   Please email <a href="mailto:proaccounts@sourcemap.com">proaccounts@sourcemap.com</a> if you have any questions or feedback.
    </p> 
</div> 
<?php } ?>

</div> 
<div class="clear">&nbsp;</div> 

<div class="search-results container">
    <?php if(isset($supplychains) && $supplychains): ?>
        <h2 class="section-title">Your Sourcemaps</h2>
        <?php foreach($supplychains as $i => $sc): ?>
            <div class="user-map-list">
                <?= View::factory('partial/home/map', array('supplychain' => $sc , 'user_id'=> $user->id )) ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h2 class="bad-news">You haven't made any maps yet. <a href="create">Get started</a></h2>
    <?php endif; ?>
    <?php if(isset($favorites) && $favorites): ?>
        <h2 class="section-title">Favorite</h2>
        <?php foreach($favorites as $i => $sc): ?>
            <div class="user-map-list">
                <?= View::factory('partial/home/map', array('supplychain' => $sc , 'user_id'=> $user->id )) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
