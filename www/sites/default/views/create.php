<div class="container_16">
<div class="grid_9">
<div class="sourcemap-form"> 
    <fieldset> 
        <legend>Create a New Sourcemap</legend> 
        <form action="/create" method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded"> 
            <label for="title">Title</label>
            <div class="sourcemap-form-textbox">
                <input type="text" name="title" value="test" class="required textbox" /> 
            </div>
            <label for="teaser">Short Description:</label>
            <div class="sourcemap-form-textbox">
                <input type="text" name="teaser" value="tea" class="required textbox" /> 
            </div>
            <label for="tags">Tags:</label>
            <div class="sourcemap-form-textbox">
                <input type="text" name="tags" value="tea" class="tags textbox" /> 
            </div>
            <label for="category">Category:</label><select name="category"> 
            <option value="0" selected="selected">None</option> 
            <option value="1">food</option> 
            <option value="2">apparel</option> 
            </select> 
            <div class="sourcemap-form-button-div">
                <input type="submit" name="create" value="Create" class="buttons" /> 
            </div> 
        </form>    
    </fieldset> 
</div> 
</div> 
</div> 

