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

<form name="alias-info" method="post" action="">
<label for="site">Site:</label>
<input type="text" name="site" class="input text" value=""/>

<label for="alias">Alias:</label>
<input type="text" name="alias" class="input text" value=""/>

<label for="supplychain_id">Supplychain_id:</label>
<input type="text" name="supplychain_id" class="input text" value=""/>

<input type="submit" value="Create Alias" />
</form><br />
  
<?php View::factory('partial/admin/list', array('list' => $supplychain_alias, 'list_type' => 'alias')) ?>
<?php echo $page_links; ?>