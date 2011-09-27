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

<?
    $edit_mode = isset($_GET["edit"])&&$can_edit ? isset($_GET["edit"]) : 0;
?>
<div id="map-container">    
    <div id="map">
        <div id="sourcemap-map-view"></div>
    </div>    
</div>

<div id="map-secondary" class="container">
    <div id="sidebar" class="map-view">
        <?php if($edit_mode): ?>
    		<h3>Map Options</h3>
            <hr/>
    		<div class="impact-box">
                <input type="checkbox" <?= $supplychain_weight; ?> id="impact-use-weight" /> 
    			<label for="impact-use-weight">Show Weight</label>
    			<div class="clear"></div>
    		</div>
    		<div class="impact-box">
                <input type="checkbox" <?= $supplychain_co2e; ?> id="impact-use-co2e" />
    			<label for="impact-use-co2e">Show Carbon Footprint (CO2e)</label>   
    			<div class="clear"></div>
    		</div>
    		<div class="sourcemap-form">
            <select id="tileset-select" name="tileset-select">	
        		<option <? if($supplychain_tileset == "cloudmade") { ?>selected<? } ?> value="cloudmade">
    				Show Default Map
    			</option>
        		<option <? if($supplychain_tileset == "satellite") { ?>selected<? } ?> value="satellite">
    				Show Satellite Map
    			</option>
        		<option <? if($supplychain_tileset == "terrain") { ?>selected<? } ?> value="terrain">
    				Show Terrain Map
    			</option>
    		</select>
    		</div>
    		<br/>
            <hr />
        <?php endif; ?>

    	<h3>Share this Sourcemap</h3>
        <hr/>
        <div id="qrcode-badge" class="container">
            <a href="<?= $scaled_qrcode_url ?>"><img class="qrcode" src="<?= $qrcode_url ?>" /></a><br/>
        </div>
        <div class="container links">
            <p>Link to this Sourcemap</p>
            <div>
                <input value="<?= $short_link; ?>" readonly="readonly" onclick="select()"></input>
            </div>
            <p>Embed this Sourcemap</p>
            <div>
                <input value='<iframe width="640px" height="480px" frameborder="0" src="<?= URL::site(NULL, TRUE) ?>embed/<?= $supplychain_id ?>"></iframe>' onclick="select()" readonly="readonly"></input>
            </div>
        </div>
    	<div class="clear"></div>
        <hr />
                
        <?= View::factory('partial/social', array('supplychain_id' => $supplychain_id)); ?>
    </div>
    

    <h1>
    	<?= HTML::chars($supplychain_name) ?> 
    	<?php if($can_edit): ?>
            <a id="map-edit-button" class="button" href="edit/<?= $supplychain_id; ?>">Edit</a>
        <?php endif; ?>
    </h1>
    <p class="description"><?= HTML::chars($supplychain_desc) ?></p>
    <hr />
    <p class="author">
        <img src="<?= HTML::chars($supplychain_avatar) ?>" alt="Avatar"></img>
        <a class="author-link" href="user/<?= $supplychain_ownerid ?>"><?= $supplychain_owner ?></a>, <?= $supplychain_date ?>
    	<? $first = true; foreach($supplychain_taxonomy as $cat) { ?>
    		<? if($first) { ?>in <a href="browse/<?= HTML::chars($cat->name); ?>"><?= HTML::chars($cat->title); ?></a>
    		<? $first = false; } else { ?>, <a href="browse/<?= $cat->name; ?>"><?= HTML::chars($cat->title); ?></a> <? } ?>
    	<? } ?>
    </p>

    <div id="discussion-section">
        <h2>Discussion</h2>
        <?php if($can_comment): ?>
        <div id="comment-form" class="sourcemap-form">
            <fieldset>
            <form method="post" action="map/comment/<?= $supplychain_id ?>">
                <div id="desc-counter">&nbsp;</div>
                <textarea placeholder="Type your comment..." name="body" id="comment-area" maxlength="255"></textarea>
 
                <input class="button" id="comment-submit" type="submit" text="Comment"/>
                <div class="clear"></div>
            </form>
            </fieldset>
        </div>
       
        <?php else: ?>
        <p><a href="/auth">Sign in</a> or <a href="/register">register</a> to add to the discussion</p> 
        <?php endif; ?>
        <?php if($comments): ?>
        <ul id="comments">
            <?php foreach($comments as $i => $comment): ?>
                <?= View::factory('partial/comment', array('comment' => $comment)) ?>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <h4 class="bad-news">Nobody's commented on this map yet.</h4>
        <?php endif; ?>
    </div><!-- #discussion-section -->
    
</div><!-- .container -->
<div class="clear"></div>
