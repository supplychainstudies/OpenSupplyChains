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

<div class="container form-page">
    <div class="copy-section">
	    <h1>Create a new map</h1>
		<p>Create a Supply Chain: map the locations of suppliers, add descriptions, photos and videos, calculate the carbon footprint, and embed or link to the map to share it with the world!</p>
		<? if($can_import) { ?>
		<ul>
			<li><a href="tools/import/csv">Import from CSV File</a></li>
			<li><a href="tools/import/google">Import from Google Spreadsheets</a></li>
		</ul>
		<? } ?>
    </div>
    <div class="box-section">
        <div class="sourcemap-form">
	        <?= $create_form ?>
	    </div>
    </div>
	<div class="clear"></div>
</div>
