<form name="alias-info" method="post" action="">
<label for="site">Site:</label>
<input type="text" name="site" class="input text" value=""/>

<label for="alias">Alias:</label>
<input type="text" name="alias" class="input text" value=""/>

<label for="supplychain_id">Supplychain_id:</label>
<input type="text" name="supplychain_id" class="input text" value=""/>

<input type="submit" value="Create Alias" />
</form><br />
  
<?= View::factory('partial/admin/list', array('list' => $supplychain_alias, 'list_type' => 'alias')) ?>
<?php echo $page_links; ?>
