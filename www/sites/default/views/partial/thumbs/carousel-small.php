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

<ul class="category-map-view <?= count($supplychains) > 3 ? "carousel jcarousel-skin-sourcemap" : "nocarousel"; ?>">
<?php if(isset($supplychains) && $supplychains): ?>
    <?php foreach($supplychains as $i => $item):?>
        <li class="preview-map-item small wide">
            <div class="preview-badge">
                <a href="view/<?php print $item->id; ?>"><img class="preview-map small wide" src="static/<?= $item->id ?>.s.png" alt="" /></a>
            </div>
            <h4 class="preview-title">
                <a class="truncate" href="view/<?= $item->id; ?>">
                    <?= Text::limit_chars(HTML::chars(isset($item->attributes->title) ? $item->attributes->title : "An Unnamed Sourcemap"), 40) ?>
                </a></h4>
            <h4 class="preview-author">
                <a href="user/<?= $item->owner->id; ?>"><?=  Text::limit_chars(HTML::chars($item->owner->name), 17) ?></a>,
                <?= View::factory('partial/thumbs/date', array('date' => $item->created)) ?>
            </h4>
            <?= View::factory('partial/thumbs/icons', array('item' => $item)) ?>
        </li>
        <?php if ($i > $limit): ?>
            <li><h2 class="section-title"><a href="/browse/<?= $category ?>">See more...</a></h2></li>
            <?php break; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
<?php endif; ?>
<div class="clear"></div>
</ul>

