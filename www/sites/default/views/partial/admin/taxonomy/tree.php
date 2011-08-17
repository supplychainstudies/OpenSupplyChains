<div class="taxonomy-tree">
<dl>
    <dt><?php if(isset($tree->data->id)): ?><a href="admin/taxonomy/<?= $tree->data->id ?>/edit"><?php endif; ?>
        <?= HTML::chars($tree->data->title) ?>
        <?php if(isset($tree->data->id)): ?></a><?php endif; ?>
    </dt>
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
