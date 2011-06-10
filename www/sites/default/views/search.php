<div class="grid search-results">
    <?php if(isset($search_result->results) && $search_result && $search_result->results): ?>
        <div class="container_16 pager">
            <?= $pager->render() ?>
        </div>
        <?php foreach($search_result->results as $i => $result): ?>
            <div class="container_16 search-result">
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
