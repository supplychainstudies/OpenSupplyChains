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
                <a href="browse/<?= isset($category_name) ? $category_name : ""; ?>">
                    <?= $category ? $category : "Everything"; ?>
                </a>&nbsp;
            </div> 
            <?php //Build category list ?>
            <div class="category-dropdown">
                <select name="BrowseSelect" onChange="location.href='browse/'+this.value;">
                <option>Select a <?= $category ? "different " : ""; ?> category:</option>
                <option value="">Everything</option>
                <?php for($i=0; $i<count($taxonomy->children); $i++): ?>
                    <?php $t = $taxonomy->children[$i]; ?>
                    <option value="<?= $t->data->name ?>"><?= HTML::chars($t->data->title) ?></a>
                <?php endfor; ?>
                <option value="uncategorized"><a href="/uncategorized">Uncategorized</a></option>
                </select>
            </div>
            <?php //Build pager for sub-pages only (disable in bar) ?>
            <?php if (false): ?>
            <div class="category-pager">
                <?= $pager->render() ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php endif; ?>
<div class="clear"></div>
<?php if (count($searches) == 1): ?>
<div id="category-pagination">
    <?= $pager->render() ?>
</div>
<?php endif; ?>
<div class="clear"></div>
<?php // TODO: split the following into two separate files ?>
<?php // If we're at the top level, display a list of all categories ?>
<?php if (count($searches) > 1){ ?>
    <?php // recent ?>
        <div class="category-view medium container wide">
            <h3 class="section-title"><a href="/browse/recent">Recent</a></h3>
            <?php echo View::factory('partial/thumbs/carousel', array('supplychains' => $recent->results, 'category' => 'recent', 'limit' => 15)) ?>
        </div><!-- .container -->
        <hr class="spacer" />
    <?php foreach($searches as $i=> $search){?>
        <?php // first 2 categories get to be big ?>
        <?php if ($i<2): ?>
        <div class="category-view medium container wide">
            <h2 class="section-title"><a href="/browse/<?= $search->parameters['c'] ?>"><?= $search->cat_title ?></a> <span class="category-quantity">(<?= count($search->results);?>)</span></h2>
            <?php echo View::factory('partial/thumbs/carousel', array('supplychains' => $search->results, 'category' => $search->parameters['c'], 'limit' => 10)) ?>
        </div>
        <hr class="spacer" />

        <?php // next 3 categories are small ?>
        <?php else: ?>
            <?php if ($i == 2): ?>
            <?php endif; ?>
                <?php if(count($search->results)>0): ?>
        <div class="category-view small container wide">
            <h3 class="section-title"><a href="/browse/<?= $search->parameters['c'] ?>"><?= $search->cat_title ?></a> <span class="category-quantity">(<?= count($search->results);?>)</span></h3>
            <?php echo View::factory('partial/thumbs/carousel-small', array('supplychains' => $search->results, 'category' => $search->parameters['c'], 'limit' => 15)) ?>
        </div>
                <?php endif ?>
        <?php endif ?>
        <?php if ($i > 3){ break; } ?>
    <?php }?>
    <?php // uncategorized ?>
        <div class="category-view small container wide">
            <h3 class="section-title"><a href="/browse/uncategorized">Uncategorized</a> <span class="category-quantity">(<?= count($uncategorized->results);?>)</span></h3>
            <?php echo View::factory('partial/thumbs/carousel-small', array('supplychains' => $uncategorized->results, 'category' => 'uncategorized', 'limit' => 15)) ?>
        </div><!-- .container -->

<?php } else { ?>
        <div class="container">
            <div class="category-view medium">
                <?php echo View::factory('partial/thumbs/browse', array('supplychains' => $searches->results)) ?>
            </div>
            <div class="clear"></div>
        </div>

<div class="clear"></div>
<?php } ?>
<?php if (count($searches) == 1): ?>
<div id="category-pagination">
    <?= $pager->render() ?>
</div>
<?php endif; ?>
