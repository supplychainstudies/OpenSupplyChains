<div id="search-page">
    <div class="container">
        <h1>Results for "<?= isset($search_result->parameters['q']) ? HTML::chars($search_result->parameters['q']) : ''; ?>"</h1>

    <?php if(isset($search_result->results) && $search_result && $search_result->results): ?>
        <?= $pager->render() ?>
        <?php $count = 0; ?>
        <?php foreach($search_result->results as $i => $result): ?>
            <div id="user-map-list">
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