<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/ 
?>

<fieldset>
<legend>Create a new group:</legend>
<form name="create-group" method="post" action="admin/groups/create_group">
<label for="username">Username:</label><input type="text" name="username" />&nbsp;<label for="groupname">Groupname:</label><input type="text" name="groupname" />
<input type="submit" value="create" name="create-group" />
</form>
</fieldset>

<?= View::factory('partial/admin/list', array('list' => $groups, 'list_type' => 'groups')) ?>
<?php echo $page_links; ?>

    
