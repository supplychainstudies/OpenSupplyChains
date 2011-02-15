<fieldset>
<legend>Create a new role:</legend>
<form name="create-user" method="post" action="admin/roles/create_role">
<label for="role">Name:</label><input type="text" name="role" />
<input type="submit" value="create" name="create-role" />
</form>
</fieldset>

<?= View::factory('partial/admin/list', array('list' => $roles, 'list_type' => 'roles')) ?>
<?php echo $page_links; ?>

    
