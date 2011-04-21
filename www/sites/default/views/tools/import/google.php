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
  

