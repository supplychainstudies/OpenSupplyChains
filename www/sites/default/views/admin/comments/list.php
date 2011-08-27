<?php echo $pager; ?>
<?= View::factory('partial/admin/list', array('list' => $comments, 'list_type' => 'comments')) ?>
<?php echo $pager; ?>

    
