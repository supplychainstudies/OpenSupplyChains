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

<div id="edit-map" class="container">
    <h1><?= isset($supplychain->attributes->itle) ? HTML::chars($supplychain->attributes->title) : "Untitled Sourcemap" ?></h1>
    <div class="edit-map-form">
        <?= $form ?>
    </div>

    <div class="edit-map-details">
        <div class="edit-map-thumbnail">
            <div class="preview-map-item medium">
                <div class="preview-badge">
                <a href="view/<?php print $supplychain->id; ?>"><img class="preview-map medium" src="static/<?= $supplychain->id ?>.m.png" alt="" /></a>
                </div>
                <h3 class="preview-title">
                    <a href="view/<?= $supplychain->id; ?>">
                        <?= isset($supplychain->attributes->title) ? HTML::chars($supplychain->attributes->title) : "Untitled Sourcemap" ?>                    
                    </a></h3>
                <h4 class="preview-author"><a href="user/<?= $supplychain->owner->id; ?>"><?= HTML::chars($supplychain->owner->name) ?></a>,
                <?= date("F j, Y", $supplychain->created) ?></h4>
                <div class="user-map-list">
                    <div class="map-controls">
                        <div class="map-controls-delete">
                            <a class="red" href="delete/<?= $supplychain->id ?>">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div> 

