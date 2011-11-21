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

<div id="slider">
    <?= View::factory('partial/thumbs/slider', array('supplychains' => $featured)) ?>
</div>

<div class="container">
    <div id="sidebar" class="welcome">
        <div class="container"> 
    		<h2 class="section-title">Popular</h2>  
            <div class="preview-map-section">
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $popular)) ?>
            </div>
            <div class="clear"></div>
        </div>           

        <div class="container"> 
    		<h2 class="section-title">Recent</h2> 
            <div class="preview-map-section">
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $recent)) ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div id="main" class="sidebar">
        <div class="container primary">
            <h1 id="site-tagline">Find out where things come from.</h1>
            <p id="site-blurb">Sourcemap is the crowdsourced directory of product supply chains and carbon footprints. <a href="http://www.sourcemap.com/register/">Join us</a> to learn more and contribute.</p>  

            <hr class="spacer" />
            
            <h2 class="section-title">Featured</h2>
            <?= View::factory('partial/thumbs/featured', array('supplychains' => $morefeatured)) ?>
            <div class="clear"></div>
            <hr class="spacer" />
            
        </div>
        <?php //News Section ?>
        <?php if(isset($news) && $news && isset($news->posts)): ?>
        <div class="container secondary"> 
            <hr class="spacer" />
            <ul>
                <?php $i = 0; ?>
                <?php foreach($news->posts as $i => $news_item): ?>
                <li class="news-item">
                    <h5 class="title"><a href="http://blog.sourcemap.com"><?= HTML::chars($news_item->title_plain) ?></a></h5>
                    <p>
                        <span class="truncate">
                        <?= substr(HTML::chars($news_item->excerpt), 0, 255) ?>&hellip; 
                        </span>
                        <a class="readmore" href="<?= $news_item->url ?>">More &raquo;</a>
                    </p>
                </li>
                <?php if (++$i == 4) break; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
</div>
