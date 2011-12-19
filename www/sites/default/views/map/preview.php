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
<div class="container preview">
    <div class="channel-banner">
       <img src="<?= $supplychain_banner_url?>"/>
    </div>
    <div class="clear" style="height: 20px"></div>
</div><!-- .container.preview -->
<?php endif; ?>

<div class="container preview">
    <h1><?= HTML::chars($supplychain_name) ?></h1>
    
    <div class="map-preview">
        <img src="/static/<?= $supplychain_id ?>.l.png"/>
	    <div class="preview-footer"><a href="/"><img src="assets/images/mobile/logo.png" /></a></div>

    </div>
	
    <a href="/mobile/<?= $supplychain_id ?>" class="full-site-button">View Interactive Map</a>
    
    <?php if (isset($supplychain_youtube_id)): ?>
    <div class="description-video">
        <iframe class="youtube-player" type="text/html" width="240px" height="180px" src="http://www.youtube.com/embed/<?= $supplychain_youtube_id ?>&showinfo=0" frameborder="0"></iframe> 
    </div>
    <?php endif; ?>

    <p class="description"><?= HTML::chars($supplychain_desc) ?></p>
  	

    <div class="clear"></div>
</div><!-- .container.preview -->
