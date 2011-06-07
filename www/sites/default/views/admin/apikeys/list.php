<?php if($apikeys): ?>
    <?= View::factory('partial/admin/list', array('list' => $apikeys, 'list_type' => 'apikeys')) ?>
    <?php echo $page_links; ?>
<?php else: ?>
<h3>There appear to be no API keys.</h3>
<?php endif; ?>
