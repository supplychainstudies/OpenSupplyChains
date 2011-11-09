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
    <?php if (isset($supplychain_banner_url) && $supplychain_banner_url): ?>
        <div class="container">
            <div class="channel-banner">
               <img src="<?= $supplychain_banner_url?>"/>
            </div>
            <div class="clear" style="height: 20px"></div>
        </div>
    <?php endif; ?>

<div id="exist-passcode" style="display:none" value="<?= $exist_passcode ?>"></div>
<div id="tree-container">    
    <div id="tree">
        <div id="sourcemap-tree-view"></div>
    </div>    
</div>

<div id="map-secondary" class="container">
    <div id="sidebar" class="map-view">
        <div class="container">
            <h2 class="section-title">Tree options</h2>
        </div>
    	<div class="clear"></div>
        <?= View::factory('partial/social', array('supplychain_id' => $supplychain_id)); ?>
    </div>
    

    <h1>
    	<?= HTML::chars($supplychain_name) ?> 
    	<?php if($can_edit): ?>
            <a id="map-edit-button" class="button" href="edit/<?= $supplychain_id; ?>">Edit Description</a>
        <?php endif; ?>
    </h1>
    <p class="description"><?= HTML::chars($supplychain_desc) ?></p>
    <?php if (isset($supplychain_youtube_id)): ?>
    <div class="description-video">
        <iframe class="youtube-player" type="text/html" width="480" height="280" src="http://www.youtube.com/embed/<?= $supplychain_youtube_id ?>" frameborder="0"></iframe> 
    </div>
    <?php endif; ?>
    <p class="author">
        <img src="<?= HTML::chars($supplychain_avatar) ?>" alt="Avatar"></img>
        <a class="author-link" href="user/<?= $supplychain_ownerid ?>"><?= isset($supplychain_display_name)? $supplychain_display_name : $supplychain_owner ?></a>, <?= $supplychain_date ?>
    	<? $first = true; foreach($supplychain_taxonomy as $cat) { ?>
    		<? if($first) { ?>in <a href="browse/<?= HTML::chars($cat->name); ?>"><?= HTML::chars($cat->title); ?></a>
    		<? $first = false; } else { ?>, <a href="browse/<?= $cat->name; ?>"><?= HTML::chars($cat->title); ?></a> <? } ?>
    	<? } ?>
    </p>
    
</div><!-- .container -->
<div class="clear"></div>
