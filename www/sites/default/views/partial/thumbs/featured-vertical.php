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

<?php if(isset($supplychains) && $supplychains): ?>
    <?php foreach($supplychains as $i => $item): ?>
        <div class="preview-map-item <?= $i % 2 ? "last" : ""; ?>">
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>">
                <img class="preview-map small" src="static/<?php print $item->id; ?>.s.png" alt="" />
            </a>
            </div>
            <h3 class="preview-title vertical">
                <a href="view/<?php print $item->id; ?>">
                <?= HTML::chars($item->attributes->title) ?>
                </a>
            </h3>
            <div>
                <h4 class="preview-author">
                    <a href="user/<?php print $item->owner->id; ?>">
                        <?= Text::limit_chars(HTML::chars($item->owner->name), 17) ?></a>, 
                    <?php print date("M j, Y",$item->created);?>
                </h4>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    --
<?php endif; ?>
