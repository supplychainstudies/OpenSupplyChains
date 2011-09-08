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
<ul id="featured-slider">
<?php foreach($supplychains as $i => $item): ?>
    <li class="featured-item">
        <div class="featured-content">
            <a href="view/<?php print $item->id; ?>">
                <img class="featured-preview-map large" src="/map/static/<?php print $item->id; ?>.l.png" alt="" />
            </a>
        </div>
        <div id="featured-description-<?= $i ?>" class="featured-description">
            <h2 class="featured-title-leader">The Supply Chain of:</h2>
            <h1 class="featured-title"><a href="/view/<?php print $item->id; ?>"><?= Text::limit_chars(HTML::chars(isset($item->attributes->title) ? $item->attributes->title : "An Unnamed Supply Chain"),35) ?></a></h1>
            <? if(isset($item->attributes->description)) { ?><h3 class="featured-teaser"><?= Text::limit_chars(HTML::chars($item->attributes->description),70); ?></h3><? } ?>
            <h4 class="featured-info">
                 <a href="user/<?= $item->owner->id; ?>">
                    <?= HTML::chars($item->owner->name) ?></a>,   
                <?php print date("F j, Y",$item->created);?>
            </h4>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<?php endif; ?>
