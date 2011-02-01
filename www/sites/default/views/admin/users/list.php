<fieldset>
<legend>Create a new user:</legend>
<form name="create-user" method="post" action="admin/users/create">
<label for="email">Email:</label><input type="text" name="email" />&nbsp;<label for="username">Username:</label><input type="text" name="username" />
<input type="submit" value="create" name="create-user" />
</form>
</fieldset>
<?= View::factory('partial/admin/list', array('list' => $users, 'list_type' => 'users')) ?>
<?php echo $page_links; ?>

    
