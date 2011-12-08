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
                <h2 class="map-title"><?= isset($supplychain->attributes->title) ? HTML::chars($supplychain->attributes->title) : "An Unnamed Sourcemap" ?></h2>
            </a>
            <h4 class="map-details">
                <?= date('F j, Y', $supplychain->created) ?>
            </h4>
            <div class="clear"></div>
            
            <?php if(isset($supplychain->attributes->description) && $supplychain->attributes->description): ?>
                <span class="map-teaser"><?= HTML::chars($supplychain->attributes->description) ?></span>
            <?php endif; ?>
        </div>
        <div class="map-controls" value="<?= $supplychain->id ?>">
            <?php if($supplychain->user_id==$user_id): ?>
            <div class="map-controls-delete">
                <a class="red" href="delete/<?= $supplychain->id ?>">Delete</a>
            </div>
            <div class="map-controls-edit">
                <a href="edit/<?= $supplychain->id ?>">Edit</a>
            </div>
            <?php endif; ?>
            <?php
                $is_channel=false;
                $user = ORM::factory('user', Auth::instance()->get_user());
                $channel = ORM::factory('role')
                    ->where('name', '=', 'channel')->find();
                if($user->has('roles', $channel)) $is_channel = true;
                $user_featured = ($supplychain->user_featured) ; ?>
            <?php if($supplychain->user_id==$user_id): ?>
            <?php if($is_channel) { ?>
            <div class="map-controls-featured channel">
                <!-- <input id="map-featured-checkbox" type="checkbox" name="featured" onclick="window.location='edit/featured/<?= $supplychain->id ?>?featured=<?= $user_featured ? "no" : "yes"?>'; return true;"<?= $user_featured ? "checked" : "" ?>/> -->
                <input id="map-featured-checkbox" type="checkbox" name="featured" <?= $user_featured ? "checked" : "" ?> />
                <a id="map-publish-link">Featured</a>
            </div> <? } ?>
            <?php $public = ($supplychain->other_perms & Sourcemap::READ) > 0; ?>
            <div class="map-controls-publish <?php if($is_channel): ?> channel <?php endif; ?>">
                <!-- <input id="map-publish-checkbox" type="checkbox" name="publish" onclick="window.location='edit/visibility/<?= $supplychain->id ?>?publish=<?= $public ? "no" : "yes"?>'; return true;"<?= $public ? "checked" : "" ?>/> -->
                <input id="map-publish-checkbox" type="checkbox" name="publish" <?= $public ? "checked" : "" ?>/>
                <a id="map-publish-link">Public</a>
            </div>
            <div class="clear"></div>

            <span class="map-controls-status" hidden></span>

            <?php if($is_channel) { ?>
            <?php $passcode_isset = isset($supplychain->attributes->passcode); ?>
            <div class="map-controls-passcode">
                <input id="map-passcode-checkbox" type="checkbox" name="passcode" value="<?= $supplychain->id ?>" <?php if($passcode_isset): ?>checked<?php endif; ?> >
                <a id="map-passcode-link">Passcode</a>
                <input id="map-passcode-input" type="text" value="<?= $passcode_isset ? $supplychain->attributes->passcode : "" ?>"/>
            </div>
            <?php } ?>
            <?php endif; //end userid== ?>
        </div>        
        <div class="clear"></div>

<?php endif; ?>
