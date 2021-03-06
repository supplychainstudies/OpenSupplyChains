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
        <div class="preview-map-item medium">
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>"><img class="preview-map medium" src="static/<?= $item->id ?>.m.png" alt="" /></a>
            </div>
            <h3 class="preview-title">
                <a href="view/<?= $item->id; ?>">
                    <?= Text::limit_chars(HTML::chars(isset($item->attributes->title) ? $item->attributes->title : "An Unnamed Supply Chain"), 23) ?>                   
                </a></h3>
            <h4 class="preview-author"><a href="user/<?= $item->owner->id; ?>"><?=  Text::limit_chars(HTML::chars($item->owner->name), 17) ?></a>,
            <?= date("M j, Y", $item->created) ?></h4>
        </div>
    <?php endforeach; ?>
<?php else: ?>
<?php endif; ?>
</div>
