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

<script>
    Sourcemap.passcode_exist = "<?= isset($exist_passcode) ? $exist_passcode : '0' ?>";
</script>
<div id="list-overview">
	<div class="overview">
    <h1><?= HTML::chars($supplychain_name) ?></h1>
    <p class="description"><?= HTML::chars($supplychain_desc) ?></p>
	</div>

    <div class="author">
         <a href="user/<?= HTML::chars($supplychain_ownerid) ?>"><img src="<?= $supplychain_avatar ?>" alt="Avatar"></img></a>
    </div>
	<div class="clear"></div>
</div>
<div id="list-container">    

</div>

<div class="clear"></div>
