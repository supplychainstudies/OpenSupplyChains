<?php if(isset($old_user_id) && $old_user_id && isset($new_user_id) && $new_user_id): ?>
<fieldset><legend>Migrate User Maps: CONFIRM</legend>
<form name="migrate_user" action="admin/migrate/" method="POST">
<h4>Sourcemap.ORG User ID: <?= HTML::chars($old_user_id) ?></h4>
<?= Form::input('old_user_id', $old_user_id, array('type' => 'hidden')) ?>
<h4>Sourcemap.COM User ID: <?= HTML::chars($new_user_id) ?> (<?= HTML::chars(ORM::factory('user', $new_user_id)->username) ?>)</h4>
<?= Form::input('new_user_id', $new_user_id, array('type' => 'hidden')) ?>
<label for="confirm">Are you sure?</label><input type="checkbox" name="confirm" /><br />
<input type="submit" value="Migrate Maps" />
</form>
</fieldset>
<?php else: ?>
<form method="GET" name="migrate_user">
<label for="uid">Enter a Sourcemap.ORG user ID or username:</label>
<input type="text" name="uid" />
<input type="submit" value="Search" />
</form>
<?php endif; ?>
