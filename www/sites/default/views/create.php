<div class="grids">
    <div class="container_16">
        <div class="form">
            <fieldset>
            <legend>Create a New Sourcemap</legend>
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

                <?php if(isset($taxonomy) && $taxonomy): ?>
                    <label for="category">Category:</label>
                    <select id="category" name="category">
                        <option value="0" selected>None</option>
                        <?php foreach($taxonomy->children as $ti => $t): ?>
                        <option value="<?= $t->data->id ?>"><?= HTML::chars($t->data->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                
                <input type="submit" class="submit" value="Go!"/>
            </form>
            </fieldset>
            <div><span class="highlighted">*</span> denotes required field</div>
        </div>
    </div>
</div>
