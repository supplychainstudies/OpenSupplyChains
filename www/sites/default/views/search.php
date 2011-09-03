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

<div id="search-page">
    <div class="container">
        <h1>Results for "<?= isset($search_result->parameters['q']) ? HTML::chars($search_result->parameters['q']) : ''; ?>"</h1>

    <?php if(isset($search_result->results) && $search_result && $search_result->results): ?>
        <?= $pager->render() ?>
        <?php $count = 0; ?>
        <?php foreach($search_result->results as $i => $result): ?>
            <div class="user-map-list">
                <?= View::factory('partial/search/result', array('supplychain' => $result)) ?>
            </div>
            <?php $count++; ?>
        <?php endforeach; ?>
        <div class="container pager">
            <?= $pager->render() ?>
        </div>
    <?php else: ?>
        <h3 class="bad-news">No results.  Please try broadening your search terms.</h3>
    <?php endif; ?>
    </div>
</div>