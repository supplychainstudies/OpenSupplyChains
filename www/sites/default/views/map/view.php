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
        Sourcemap.passcode_exist = "<?= isset($exist_passcode) ? $exist_passcode : '0' ?>";
    </script>

<div id="map-container">    
    <div id="map">
        <div id="sourcemap-map-view"></div>
    </div>    
</div>

<div id="map-secondary" class="container">
    <div id="sidebar" class="map-view">
		<div class="container">
            <div class="editable-options">
            	<h2 class="section-title">Map Options</h2>
				
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
                <div class="impact-box">
                    <input type="checkbox" <?= $supplychain_water; ?> id="impact-use-water" />
                    <label for="impact-use-water">Show Water Footprint (H2O)</label>   
                    <div class="clear"></div>
                </div>
                <div class="impact-box">
                    <input type="checkbox" <?= $supplychain_energy; ?> id="impact-use-energy" />
                    <label for="impact-use-energy">Show Energy Footprint (kWh)</label>   
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
				
                <div class="map-replace">
                    <div style="clear: both; float:left;">
                        <form class="sourcemap-form" name="xls-import" action="/create" method="post" enctype="multipart/form-data">
                            <a class="tooltip pro" style="float: left; margin-top: 8px; margin-right: 10px; padding-bottom: 3px">New!</a>
                            <h3 class="blue" style="clear: right; float: left;">Upload a Spreadsheet</h3>
                            <input type="file" name="file" style="visibility: hidden; width: 0px; height: 0px;" />
							<input class="hidden" name="title" type="text" value="<?= $supplychain_name ?>" />
                            <input type="button" name="file_front" value="Choose a File..." class="button alternate" style="clear: none; float: left; margin-right: 10px; width: 150px; height: 30px; padding: 8px" />
                            <input class="button" id="comment-submit" type="submit" value="Replace" style="width: 90px; height: 30px; padding: 8px"/>
                            <select class="hidden"  name="replace_into">
                                <option value="<?= $supplychain_id ?>"></option> 
                            </select>
                        </form>
                    </div>
				</div>
				
            </div>

            <h2 class="section-title">Share this Sourcemap</h2>
            <div id="qrcode-badge">
                <a href="<?= $scaled_qrcode_url ?>"><img class="qrcode" src="<?= $qrcode_url ?>" /></a><br/>
            </div>
            <div class="pseudo-form">
                <input class="link" value="<?= $short_link; ?>" readonly="readonly" onclick="select()"></input>
            </div>
            <div class="pseudo-form">
                <input class="embed" value='<iframe width="640px" height="480px" frameborder="0" src="<?= URL::site(NULL, TRUE) ?>embed/<?= $supplychain_id ?>"></iframe>' onclick="select()" readonly="readonly"></input>
            </div>
            <div class="share-pseudo">
                <div class="linkbox earth"><a href="<?= URL::site(NULL, TRUE) ?>services/supplychains/<?= $supplychain_id ?>?f=kml">Download for Google Earth</a></div>
            </div>
			
            <div class="clear"></div>
            <?= View::factory('partial/social', array('supplychain_id' => $supplychain_id)); ?>
        </div>
    </div>
    
    <div id="main" class="sidebar">
        <h1 class="map-title">
            <span class="supplychain_name">
            <?php if(!$exist_passcode):?>
                <?= HTML::chars($supplychain_name) ?> 
            <?php endif; ?>
            </span>
        </h1>
        <?php if($can_edit): ?>
            <a id="map-edit-button" class="button alternate" href="edit/<?= $supplychain_id; ?>">Edit Description</a>
        <?php endif; ?>
        <div class="clear"></div>
        <p class="description"><?= HTML::chars($supplychain_desc) ?></p>
        <?php if (isset($supplychain_youtube_id)): ?>
        <div class="description-video">
            <iframe class="youtube-player" type="text/html" width="480" height="280" src="http://www.youtube.com/embed/<?= $supplychain_youtube_id ?>" frameborder="0"></iframe> 
        </div>
        <?php endif; ?>
        <p class="author">
            <img src="<?= HTML::chars($supplychain_avatar) ?>" alt="Avatar"></img>
            <a class="author-link" href="user/<?= $supplychain_owner ?>"><?= isset($supplychain_display_name)? $supplychain_display_name : $supplychain_owner ?></a>, <?= $supplychain_date ?>
            <br />
            <?php $first = true; foreach($supplychain_taxonomy as $cat) {
                if($first) { 
                    print 'in <a href="browse/' . HTML::chars($cat->name) . '">' . HTML::chars($cat->title) . '</a>';
                    $first = false; 
                } else { 
                    print ', <a href="browse/' . HTML::chars($cat->name) . '">' . HTML::chars($cat->title) . '</a>';
                }
            } ?>
        </p>

        <div id="discussion-section">
            <h2 class="section-title">Discussion</h2>
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
            <?php endif; ?>
        </div><!-- #discussion-section -->
        <div class="clear"></div>
    </div><!-- #main -->
    
</div><!-- .container -->
<div class="clear"></div>
