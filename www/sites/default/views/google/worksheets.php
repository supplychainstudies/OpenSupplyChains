<?php if(isset($worksheets, $spreadsheet_key) && $worksheets): ?>
<form action="/tools/google/import" method="post">
<input type="hidden" name="k" value="<?= HTML::chars($spreadsheet_key) ?>" />
<label for="stops-wsid">Stops Worksheet:</label><br />
<select name="stops-wsid">
<option value="0"></option>
<?php foreach($worksheets as $i => $sheet): ?>
<option value="<?= HTML::chars($sheet['id']) ?>"><?= HTML::chars($sheet['title']) ?></option>
<?php endforeach; ?>
</select><br />
<label for="hops-wsid">Hops Worksheet:</label><br />
<select name="hops-wsid">
<option value="0"></option>
<?php foreach($worksheets as $i => $sheet): ?>
<option value="<?= HTML::chars($sheet['id']) ?>"><?= HTML::chars($sheet['title']) ?></option>
<?php endforeach; ?>
</select><br />
<label for="replace-into">Create a New Map or Replace an Existing one?</label><br />
<select name="replace-into">
<option value="0">Create a new map</option>
<?php foreach($user_supplychains as $sc): ?>
<option value="<?= $sc->id ?>"><?= isset($sc->attributes->title) ? HTML::chars($sc->attributes->title) : '#'.$sc->id.', '.strftime('%c', $sc->created) ?></option>
<?php endforeach; ?>
</select><br />
<input type="submit" value="Import" />
</form>
<?php else: ?>
<h3 class="error">No worksheets found.</h3>
<a href="/tools/google/list">Go back to the list of your spreadsheets</a>
<?php endif; ?>
