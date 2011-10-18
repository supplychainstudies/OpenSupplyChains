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
    <?php if(isset($banner_url)): ?>
    <div class="channel-banner">
        <img src="<?= $banner_url?>"/>
    </div>
    <?php endif; ?>
    <div class="clear"></div>
    
    <div class="channel-featured-maps">
        <?= View::factory('partial/thumbs/slider', array('supplychains' => $featured)) ?>
        <div class="clear"></div>
        <div id="channel-featured-teaser"></div>
    </div>

    <div class="channel-other-maps">
        <div class="search-results">
            <?php if(isset($supplychains) && $supplychains): ?><br/>
                <?php foreach($supplychains as $i => $sc): ?>
                    <div class="user-map-list">
                        <?= View::factory('partial/user/map', array('supplychain' => $sc)) ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <h2 class="bad-news">No maps yet!</h2>
            <?php endif; ?>
        </div>
    </div>
    <div class="clear"></div>

</div>
