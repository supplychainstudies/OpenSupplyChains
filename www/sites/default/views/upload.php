<head>
<?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : Sourcemap_CSS::link_tags(
        'sites/default/assets/styles/reset.css', 
        'assets/styles/general.less'
    ) ?>


</head>

<div class="upload-form">
     <label for="key">Google Spreadsheet:</label><input type="text" id="key" name="key" value="" />  
<input type="submit" id="submit" value="Get JSON"/>

</div>
  
<?php /*
<script type="text/javascript">
     var a = "";
     var myFunc = function(resp){
	 a = resp;

     };


</script>

<script type="text/javascript" src="https://spreadsheets.google.com/feeds/cells/0Aqwz6ZHrexb7dFk1VWF3RUJuaGZLRmt5anNobmY3Q2c/od6/public/basic?alt=json&callback=myFunc"></script>
      */?>


