<form name="announcement" method="post" action="admin/announcements/announce">
<fieldset><legend>Make an announcement:</legend>
<textarea name="announcement_message" cols="80" rows="3" placeholder="Must be between 8 and 256 characters."></textarea><br />

<label for="confirm1">Are you sure?</label>
<input name="confirm1" type="checkbox" />
<div class="clear"></div>

<label for="confirm2">Are you completely sure?</label>
<input name="confirm2" type="checkbox" />
<div class="clear"></div>

<label for="confirm3">Seriously. You're sending an announcement to everybody. Are you sure?</label>
<input name="confirm3" type="checkbox" />
<div class="clear"></div>

<input type="submit" value="Announce" />
</fieldset>
</form>
  
<?= View::factory('partial/admin/list', array('list' => $announcements, 'list_type' => 'announcements')) ?>
<?php echo $page_links; ?>
