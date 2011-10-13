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
                Currently browsing&nbsp; 
                <a href="browse/">
                    <?= $category ? $category : "Everything"; ?>
                </a>&nbsp;
            </div> 
            <?php //Build category list ?>
            <div class="category-dropdown">
                <select>
                <option>Select a <?= $category ? "different " : ""; ?> category:</option>
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

<?php // TODO: split the following into two separate files ?>
<?php // If we're at the top level, display a list of all categories ?>
<?php if (count($searches) > 1){ ?>
    <?php foreach($searches as $i=> $search){?>
        
        <?php // first 3 categories get to be big ?>
        <?php if ($i<2): ?>
        <div class="category-view medium">
            <h2><a href="/browse/<?= $search->parameters['c'] ?>"><?= $search->parameters['c'] ?></a> <span class="category-quantity">(<?= count($search->results);?>)</span></h2>
            <?php echo View::factory('partial/thumbs/carousel', array('supplychains' => $search->results)) ?>
        </div>

        <?php // remaining categories are small ?>
        <?php else: ?>
            <?php if ($i == 2): ?>
        <div class="container">
            <div id="sidebar-left"> 
            <?php endif; ?>
                <div class="category-view small">
                    <h3><a href="/browse/<?= $search->parameters['c'] ?>"><?= $search->parameters['c'] ?></a> <span class="category-quantity">(<?= count($search->results);?>)</span></h3>
                    <?php echo View::factory('partial/thumbs/carousel-small', array('supplychains' => $search->results)) ?>
                </div>
        <?php endif ?>
    <?php }?>
            </div><!-- .left -->
            <div id="sidebar" class="nomargin skinny">
                <ul>
                    <li>
                        <h2>Interesting Sourcemaps</h2>
                        <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $interesting->results)) ?>
                    </li>
                </ul>
            </div>
            <div class="clear"></div>
        </div><!-- .container -->

<?php } else { ?>
        <div class="container">
            <h2>There are <?= count($searches->results);?> Sourcemaps in this category:</h2>
            <div class="category-view medium">
                <?php // TODO: improve the names of partial views ?>
                <?php echo View::factory('partial/thumbs/featured', array('supplychains' => $searches->results)) ?>
            </div>
        </div>

<div class="clear"></div>
<?php } ?>
