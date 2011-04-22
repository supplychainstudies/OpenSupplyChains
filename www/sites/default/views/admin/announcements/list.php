<form name="announcement" method="post" action="admin/announcements/announce">
<fieldset><legend>Make an announcement:</legend>
<textarea name="announcement_message"></textarea><br />

<label for="confirm1">Are you sure?</label><br/>
<input name="confirm1" type="checkbox" /><br />
<label for="confirm2">Are you completely sure?</label><br/>
<input name="confirm2" type="checkbox" /><br />
<label for="confirm3">Seriously. You're sending an announcement to everybody. Are you sure?</label><br/>
<input name="confirm3" type="checkbox" /><br />

<input type="submit" value="Announce" />
</fieldset>
</form>
  
<?= View::factory('partial/admin/list', array('list' => $announcements, 'list_type' => 'announcements')) ?>
<?php echo $page_links; ?>
