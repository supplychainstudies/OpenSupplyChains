<form name="new-featured" method="post" action="admin/featured/add">

<label for="supplychain_id">Supplychain ID:</label>
<input type="text" name="supplychain_id" class="input text" value=""/>

<input type="submit" value="Feature" />
</form><br />
 
<?= View::factory('partial/admin/list', array('list' => $list, 'list_type' => 'featured')) ?>

<?php echo $page_links; ?>
    
