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