<form name="csv-import" method="post" enctype="multipart/form-data">
<label for="supplychain_name">Title:</label><br />
<input name="supplychain_name" type="text" value="A Sourcemap" /><br />
<label for="stop_file">Stop File:</label><br />
<input type="file" name="stop_file" /><br />
<label for="hop_file">Hop File:</label><br />
<input type="file" name="hop_file" /><br />
<input type="checkbox" name="publish" value="yes" checked="yes" /><label for="publish">Publish?</label><br />
<input type="submit" value="Import" />
</form>
