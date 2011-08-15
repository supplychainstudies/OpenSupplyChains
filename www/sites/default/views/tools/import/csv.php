<div class="container">
	<form class="sourcemap-form" name="csv-import" method="post" enctype="multipart/form-data">
	<label for="supplychain_name">Name:</label><br />
	<div class="sourcemap-form-textbox">
		<input name="supplychain_name" type="text" value="A Sourcemap" />
	</div>
	<br />
	<label for="stop_file">Stop File:</label><br /><br/>
	<input type="file" name="stop_file" /><br />
	<label for="hop_file">Hop File:</label><br /><br/>
	<input type="file" name="hop_file" /><br />
	<?php if(isset($user_supplychains) && $user_supplychains): ?>
	<br />
	<label for="replace-into">Create a New Map or Replace an Existing one?</label><br />
	<select name="replace_into">
	<option value="0">Create a new map</option>
	<?php foreach($user_supplychains as $sc): ?>
	<option value="<?= $sc->id ?>"><?= isset($sc->title) ? HTML::chars($sc->title) : 'Replace map ('.$sc->id.') created on '.date("F j, Y, g:i a", $sc->created) ?></option>
	<?php endforeach; ?>
	</select><br />
	<?php endif; ?>
	<input type="checkbox" name="publish" value="yes" checked="yes" /><label for="publish">Public</label><br />
	<input class="button" type="submit" value="Import" />
	</form>
</div>