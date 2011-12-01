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
    var username = '<?= $user->username ?>';
    var is_channel = <?= $isChannel ?>;
</script>
<div class="container">
    <div class="dashboard-top">
        <div class="left">
            <div class="container">
                <h2 class="user-name section-title">
                    <?= isset($user->display_name) ? HTML::chars($user->display_name) : HTML::chars($user->username) ?>
                    <?= $isChannel ? '<span class="secondary">Channel Account</span>' : '' ?>
                </h2>       
                <?= View::factory('partial/user/badge', array('user' => $user, 'isChannel' => $isChannel, 'canEdit' => true)) ?>
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
<div class="account-alert question"> 
    <p> 
        Did you make any maps on sourcemap.org? Email <a href="mailto:account-migration@sourcemap.com">account-migration@sourcemap.com</a> to import them into your new dashboard.
    </p> 
</div> 

<?php if ($isChannel){ ?>
<div class="account-alert notice"> 
    <p> 
        Congratulations!  You're one of our first Channel Accounts.  You now have access to new modes, options, and features.
        <br />We're working to improve our premium services.   Please email <a href="mailto:channels@sourcemap.com">channels@sourcemap.com</a> if you have any questions or feedback.
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
