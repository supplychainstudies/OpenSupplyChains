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
                <div class="category-current">
                    Currently browsing&nbsp; <a href="browse/">Everything &nbsp;</a>
                </div> 
                <?php //Build category list ?>
                <div class="category-dropdown">
                    <select>
                    <option>Select a category:</option>
                    <?php for($i=0; $i<count($taxonomy->children); $i++): ?>
                    <?php $t = $taxonomy->children[$i]; ?>
                    <option value="<?= $t->data->name ?>"><?= HTML::chars($t->data->title) ?></a>
                    <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="clear"></div>
    </div>
</div>
<?php endif; ?>
<div class="clear"></div>

<div class="container">
    <?php $counter = 0; ?>
    <?php foreach($categories as $category){?>
    <?php // first 3 categoies get to be big ?>
    <?php if ($counter<2): ?>
    <div class="category-view medium">
        <h2><?= $category->title ?>: <span class="category-quantity">99</span></h2>
        <?php echo View::factory('partial/thumbs/carousel', array('supplychains' => $primary->results)) ?>
    </div>
    <?php // remaining are small ?>
    <?php else: ?>
    <div class="category-view small">
        <h3><?= $category->title ?>: <span class="category-quantity">99</span></h3>
        <?php echo View::factory('partial/thumbs/carousel-small', array('supplychains' => $primary->results)) ?>
    </div>
    <?php endif ?>
    <?php $counter++; ?>
    <?php }?>
    <div id="sidebar">
        <ul>
            <li>
                <h2>Interesting Sourcemaps</h2>
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $interesting->results)) ?>
            </li>
           
            <!--
            <li>
                <h2>New:</h2>
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $recent->results)) ?>
            </li>
            
            <li>
                <h2>Starred:</h2>
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $favorited)) ?>
            </li>
            
            <li>
                <h2>Discussed:</h2>
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $discussed)) ?>
            </li>
            -->
        </ul>
    </div>
    <div class="clear"></div>
</div><!-- .container -->
