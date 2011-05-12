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
