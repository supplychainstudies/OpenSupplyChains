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

<div class="righty-tighty">
<fieldset><legend>Add a Category</legend>
<form name="add-taxonomy" method="post" action="admin/taxonomy/add">
<label for="parent">Parent:</label>
<select name="parent">
<?= View::factory('partial/admin/taxonomy/options', array('tree' => $tree, 'indent' => 0)) ?>
</select><br />
<label for="title">Title:</label><br />
<input type="text" name="title" /><br />
<label for="name">Label (for urls, etc.):</label><br />
<input type="text" name="name" /><br />
<label for="description">Description:</label><br />
<textarea cols="40" rows="4" name="description"></textarea><br />
<input type="submit" value="add" />
</form>
</fieldset>
</div>
<?= View::factory('partial/admin/taxonomy/tree', array('tree' => $tree)) ?>
