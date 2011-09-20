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
            <a href="view/<?php $supplychain->id ?>">
                <img class="user-map-preview small" src="map/static/<?php $supplychain->id ?>.s.png" />
            </a>
        </div>
        <div class="map-description">
            <a href="view/<?php $supplychain->id ?>">
                <h2 class="map-title"><?php HTML::chars(isset($supplychain->attributes->title) ? $supplychain->attributes->title : "An Unnamed Sourcemap") ?></h2>
            </a>
            <h4 class="map-details">
                <?php date('F j, Y', $supplychain->created) ?>
            </h4>
            <div class="clear"></div>
            
            <?php if(isset($supplychain->attributes->description) && $supplychain->attributes->description): ?>
                <span class="map-teaser"><?php HTML::chars($supplychain->attributes->description) ?></span>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
<?php endif; ?>
