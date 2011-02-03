<fieldset>
<legend>Create a new group:</legend>
<form name="create-user" method="post" action="">
<label for="username">Username:</label><input type="text" name="username" />&nbsp;<label for="groupname">Groupname:</label><input type="text" name="groupname" />
<input type="submit" value="create" name="create-group" />
</form>
</fieldset>
<?= View::factory('partial/admin/list', array('list' => $groups, 'list_type' => 'groups')) ?>
<?php echo $page_links; ?>

    
