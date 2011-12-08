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
            <h2 class="featured-title-leader">The Sourcemap of:</h2>
            <h1 class="featured-title">
                <a href="/view/<?php print $item->id; ?>"><?= Text::limit_chars(HTML::chars(isset($item->attributes->title) ? $item->attributes->title : "An Unnamed Sourcemap"),35) ?></a>
            </h1>
                <? if(isset($item->attributes->description)) { ?>
                    <?php
                    $supplychain_desc = $item->attributes->description;
                    $regex = "/\\[youtube:([^]]+)]/";
                    if (preg_match($regex, $supplychain_desc, $regs)) {
                        $supplychain_youtube_id = $regs[1];
                        $supplychain_desc = str_replace($regs[0], '', $supplychain_desc);
                    }                                                                                                                                                        
                    ?>
                    <h3 class="featured-teaser"><?= HTML::chars($supplychain_desc); ?></h3><? } ?>
            <h4 class="featured-info">
                <a href="user/<?= isset($item->owner->username) ? $item->owner->username : $item->owner->id ?>">
                <?= isset($item->owner->display_name) ? HTML::chars($item->owner->display_name) : HTML::chars($item->owner->username)  ?></a>,   
                <?= View::factory('partial/thumbs/date', array('date' => $item->created)) ?>
            </h4>
            <?= View::factory('partial/thumbs/icons', array('item' => $item)) ?>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
