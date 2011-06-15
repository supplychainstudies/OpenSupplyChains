<div class="search-results">
    <div class="gradient-background">
        <div class="container_16">
            <div class="grid_16">
                <div class="spacer"></div>
                <h2>Search Results for "<?php if(isset($search_result->parameters['q'])) : HTML::chars($search_result->parameters['q']); endif; ?>"</h2>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="container_16">
    </div>
    <?php if(isset($search_result->results) && $search_result && $search_result->results): ?>
        <!--div class="container_16 pager">
            <?= $pager->render() ?>
        </div-->
        <?php foreach($search_result->results as $i => $result): ?>
            <div class="container_16 map-item">
                <?= View::factory('partial/search/result', array('result' => $result)) ?>
            </div>
        <?php endforeach; ?>
        <div class="container_16 pager">
            <?= $pager->render() ?>
        </div>
    <?php else: ?>
        <h3 class="bad-news">No results.</h3>
    <?php endif; ?>
</div>
