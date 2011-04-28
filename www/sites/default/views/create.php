<head>
<?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : Sourcemap_CSS::link_tags(
        'sites/default/assets/styles/reset.css', 
        'assets/styles/general.less'
    ) ?>
</head>

<div class="form">
    <fieldset>
    <form name="create" method="post" action="create">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" class="required" value="" />
        <div class="clear"></div>

        <label for="description">Description:</label>
        <input type="text" id="desc" name="desc" class="required" value="" />
        <div class="clear"></div>
        
        <label for="tags">Tags:</label>
        <input type="text" id="tags" name="tags" value="" />
        <div class="clear"></div>
        
        <input type="submit" class="submit" value="Go!"/>
    </form>
    </fieldset>
    <div><span class="highlighted">&ast;</span> denotes required field</div>
</div>
  

