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

<div id="popular-maps">
<?php if(isset($supplychains) && $supplychains): ?>
    <?php foreach($supplychains as $i => $item):?>
        <div class="preview-map-item medium<?= $i % 2 ? " last" : ""; ?>">
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>"><img class="preview-map" src="static/<?= $item->id ?>.m.png" alt="" /></a>
            </div>
            <h3 class="preview-title featured">
                <a class="truncate" href="view/<?= $item->id; ?>">
                    <?= HTML::chars(isset($item->attributes->title) ? $item->attributes->title : "An Unnamed Sourcemap") ?>                   
                </a></h3>
            <h4 class="preview-author">
                <a href="user/<?= $item->owner->name; ?>"><?=  isset($item->owner->display_name)?  Text::limit_chars(HTML::chars($item->owner->display_name), 17) : Text::limit_chars(HTML::chars($item->owner->name), 17) ?></a>,
                <?= View::factory('partial/thumbs/date', array('date' => $item->created)) ?>
            </h4>
            <?= View::factory('partial/thumbs/icons', array('item' => $item)) ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
<?php endif; ?>
</div>
