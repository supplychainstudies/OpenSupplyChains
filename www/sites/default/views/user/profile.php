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
    <?= View::factory('partial/user/badge', array('user' => $user, 'isChannel' => false, 'canEdit' => false)) ?>
</div>
<div class="clear"></div>
<div class="user-profile-view">
<div class="search-results container">
    <?php if(isset($supplychains) && $supplychains): ?><br/>
        <h2><?= ucwords(HTML::chars($user->username)) ?>'s Sourcemaps</h2>
        <div class="container pager">
            <!-- ?= $pager->render() ?> -->
        </div>
        <?php foreach($supplychains as $i => $sc): ?>
            <div class="user-map-list">
                <?= View::factory('partial/user/map', array('supplychain' => $sc)) ?>
            </div>
        <?php endforeach; ?>
        <div class="container pager">
            <!--?= $pager->render() ?>-->
        </div>
    <?php else: ?>
        <h2 class="bad-news">No maps yet!</h2>
    <?php endif; ?>

</div>
</div>

