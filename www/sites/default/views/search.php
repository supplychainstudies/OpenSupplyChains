<div id="search-results">
    <div class="container">
        <div class="spacer"></div>
        <h1>Results for "<?= isset($search_result->parameters['q']) ? HTML::chars($search_result->parameters['q']) : ''; ?>"</h1>
    </div>
    <div class="clear"></div>
    <?php if(isset($search_result->results) && $search_result && $search_result->results): ?>
        <?= $pager->render() ?>
        <?php $count = 0; ?>
        <?php foreach($search_result->results as $i => $result): ?>
            <div class="container <?php if ($count % 2 == 0 ) { echo " alt"; } ?>">
                <?= View::factory('partial/search/result', array('result' => $result)) ?>
            </div>
            <?php $count++; ?>
            <?php endforeach; ?>
        <div class="container pager">
            <?= $pager->render() ?>
        </div>
    <?php else: ?>
        <div class="container">
            <h3 class="bad-news">No results.  Please try broadening your search terms.</h3>
        </div>
    <?php endif; ?>
</div>
