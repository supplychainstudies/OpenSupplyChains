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

<?php if(isset($worksheets, $spreadsheet_key) && $worksheets): ?>
<div class="container">
<form class="sourcemap-form" action="/tools/import/google/import" method="post" onsubmit="return beforeSubmit()">
<input type="hidden" name="k" value="<?php HTML::chars($spreadsheet_key) ?>" />
<label for="supplychain_name">Name:</label><br />
<input name="supplychain_name" type="text" value="A Sourcemap" />

<label for="stops-wsid">Stops Worksheet:</label><br />
<select name="stops-wsid"  onblur="DisableOption(this.value);" >
<option value="0"></option>
<?php foreach($worksheets as $i => $sheet): ?>
<option value="<?php HTML::chars($sheet['id']) ?>"><?php HTML::chars($sheet['title']) ?></option>
<?php endforeach; ?>
</select><br />
<label for="hops-wsid">Hops Worksheet:</label><br />
<select name="hops-wsid" onblur="DisableOption(this.value);" >
<option value="0"></option>
<?php foreach($worksheets as $i => $sheet): ?>
<option value="<?php HTML::chars($sheet['id']) ?>"><?php HTML::chars($sheet['title']) ?></option>
<?php endforeach; ?>
</select><br />
<label for="replace-into">Create a New Map or Replace an Existing one?</label><br />
<select name="replace-into">
<option value="0">Create a new map</option>
<?php foreach($user_supplychains as $sc): ?>
<option value="<?php $sc->id ?>"><?php isset($sc->title) ? HTML::chars($sc->title) : 'Replace map ('.$sc->id.') created on '.date("F j, Y, g:i a", $sc->created) ?></option>
<?php endforeach; ?>
</select><br />
<input type="checkbox" name="publish" value="yes" checked="yes" /><label for="publish">Public</label><br />
<input class="button" type="submit" value="Import" />
</form>
<?php else: ?>
<h3 class="error">No worksheets found.</h3>
<a href="/tools/google/list">Go back to the list of your spreadsheets</a>
<?php endif; ?>
</div>
