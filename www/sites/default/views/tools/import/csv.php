<div class="container_16">
    <div class="grid_16">
        <form name="csv-import" method="post" enctype="multipart/form-data">
        <label for="supplychain_name">Title:</label><br />
        <input name="supplychain_name" type="text" value="A Sourcemap" /><br />
        <label for="stop_file">Stop File:</label><br />
        <input type="file" name="stop_file" /><br />
        <label for="hop_file">Hop File:</label><br />
        <input type="file" name="hop_file" /><br />
        <?php if(isset($user_supplychains) && $user_supplychains): ?>
        <br />
        <label for="replace-into">Create a New Map or Replace an Existing one?</label><br />
        <select name="replace_into">
        <option value="0">Create a new map</option>
        <?php foreach($user_supplychains as $sc): ?>
        <option value="<?= $sc->id ?>"><?= isset($sc->attributes->title) ? HTML::chars($sc->attributes->title) : '#'.$sc->id.', '.strftime('%c', $sc->created) ?></option>
        <?php endforeach; ?>
        </select><br />
        <?php endif; ?>
        <input type="checkbox" name="publish" value="yes" checked="yes" /><label for="publish">Public</label><br />
        <input type="submit" value="Import" />
        </form>
    </div>
</div>
