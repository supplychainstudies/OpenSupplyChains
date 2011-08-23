<div class="container form-page">
    <div class="copy-section">
	    <h1>Create a new map</h1>
		<p>Tell the story behind a product or a service: map the locations of suppliers, add descriptions, photos and videos, calculate the carbon footprint, and embed or link to the map to share it with the world!</p>
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
