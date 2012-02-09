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
<?php if($passcode_match===False): ?>
<div id="passcode-input">
<form class="passcode-input" action"/" method="get">
<?php if(!isset($passcode)): ?>
<label for="passcode"> This map is protected. Please enter the password:</label>
<?php else: ?>
<label for="passcode"> Your password is wrong. Please re-enter the password:</label>
<?php endif; ?>
<input name="passcode" type="text"></input>
<input id="passcode-submit" type="submit"/>
</form>
</div>
<?php else: ?>

<div class="container preview" style="background: url('/static/<?= $supplychain_id ?>.l.png') top center no-repeat; background-size: auto 100%; position:absolute; top:0; bottom: 0;">
    <h1><?= HTML::chars($supplychain_name) ?></h1>
   
    <div id="bottom">
        <p class="description"><?= HTML::chars($supplychain_desc) ?></p>
        
        <?php if (isset($supplychain_youtube_id)): ?>
        <div class="description-video">
            <iframe class="youtube-player" type="text/html" width="240px" height="180px" src="http://www.youtube.com/embed/<?= $supplychain_youtube_id ?>?showinfo=0" frameborder="0"></iframe> 
        </div>
        <?php endif; ?>
        
        <a href="/mobile/<?= $supplychain_id ?><?php if($passcode_match==True){ ?>?passcode=<?=$passcode ?><?php } ?>" class="full-site-button">View Interactive Map</a>

    </div>
    
    <div class="clear"></div>
    
    <img id="watermark" src="assets/images/watermark.png" />
</div><!-- .container.preview -->
<?php endif; ?>
