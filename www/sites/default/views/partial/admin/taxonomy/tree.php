<div class="taxonomy-tree">
<dl>
    <dt><?= HTML::chars($tree->data->title) ?></dt>
</dl>
<?php if(isset($tree->data->id) && $tree->data->id): ?>
<form method="post" action="admin/taxonomy/rm">
<input type="hidden" name="taxonomy_id" value="<?= HTML::chars($tree->data->id) ?>" />
<input type="submit" value="remove" name="rm" />
</form>
<?php endif; ?>
<?php if($tree->children): ?>
<ul class="children">
    <?php foreach($tree->children as $i => $child): ?>
    <li><?= View::factory('partial/admin/taxonomy/tree', array('tree' => $child)) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
