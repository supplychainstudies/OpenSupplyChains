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

<?php if($taxonomy): ?>
<div id="category-list">
    <div class="container">
            <div id="category-list-content">
                <a href="browse/">Everything &nbsp;</a>
                <?php for($i=0; $i<count($taxonomy->children); $i++): ?>
                <?php $t = $taxonomy->children[$i]; ?>
                <a href="browse/<?php Sourcemap_Taxonomy::slugify($t->data->name) ?>"<?php if(count($taxonomy->children)-1 == $i): ?> class="last"<?php endif;?>><?php HTML::chars($t->data->title) ?></a>
                <?php endfor; ?>
            </div>
            <div class="clear"></div>
    </div>
</div>
<?php endif; ?>
<div class="clear"></div>

<div id="browse-featured" class="container">
    <div>
        <?php if($category): ?>
           <h2>Browsing category "<?php HTML::chars($category->title) ?>"</h2>
        <?php else: ?>
            <h2>Viewing all categories</h2>
        <?php endif; ?>
    </div>
    <div>
    <?php $pager ?><br />
    </div>
    <?php View::factory('partial/thumbs/featured', array('supplychains' => $primary->results)) ?>
</div><!-- .container -->

<div class="clear"></div>
<ul id="browse-list" class="container">
    <li>
        <h2>Interesting:</h2>
        <?php View::factory('partial/thumbs/featured-vertical', array('supplychains' => $interesting->results)) ?>
    </li>
    
    <li>
        <h2>New:</h2>
        <?php View::factory('partial/thumbs/featured-vertical', array('supplychains' => $recent->results)) ?>
    </li>
    
    <li>
        <h2>Starred:</h2>
        <?php View::factory('partial/thumbs/featured-vertical', array('supplychains' => $favorited)) ?>
    </li>
    
    <li>
        <h2>Discussed:</h2>
        <?php View::factory('partial/thumbs/featured-vertical', array('supplychains' => $discussed)) ?>
    </li>
    <div class="clear"></div>
</ul><!-- .container -->
