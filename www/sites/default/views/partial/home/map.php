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

<?php if(isset($supplychain) && $supplychain): ?>
        <div class="map-item small">
            <a href="view/<?= $supplychain->id ?>">
                <img class="user-map-preview small" src="map/static/<?= $supplychain->id ?>.s.png" />
            </a>
        </div>
        <div class="map-description">
            <a href="view/<?= $supplychain->id ?>">
                <h2 class="map-title"><?= isset($supplychain->attributes->title) ? HTML::chars($supplychain->attributes->title) : "An Unnamed Supply Chain" ?></h2>
            </a>
            <h4 class="map-details">
            	<?= date('F j, Y', $supplychain->created) ?>
            </h4>
            <div class="clear"></div>
            
            <?php if(isset($supplychain->attributes->description) && $supplychain->attributes->description): ?>
                <span class="map-teaser"><?= HTML::chars($supplychain->attributes->description) ?></span>
            <?php endif; ?>
        </div>
        <div class="map-controls">
            <?php $public = ($supplychain->other_perms & Sourcemap::READ) > 0; ?>
            <div class="map-controls-publish">
                <input id="map-publish-checkbox" type="checkbox" name="publish" onclick="window.location='edit/visibility/<?= $supplychain->id ?>?publish=<?= $public ? "no" : "yes"?>'; return true;"<?= $public ? "checked" : "" ?>/>
                <a id="map-publish-link">Public</a>
            </div>
            <div class="map-controls-edit">
                <a href="edit/<?= $supplychain->id ?>">Edit</a>
            </div>
            <div class="map-controls-delete">
                <a class="red" href="delete/<?= $supplychain->id ?>">Delete</a>
            </div>
        </div>
        <div class="clear"></div>
<?php endif; ?>
