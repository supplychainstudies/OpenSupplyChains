<?php if(isset($exclude) && is_array($exclude) && isset($tree->data->id) && in_array($tree->data->id, $exclude)): ?>
<?php else: ?>
<option <?php if(isset($selected, $tree->data->id) && $selected == $tree->data->id): ?>selected <?php endif; ?>value="<?= HTML::chars(isset($tree->data->id) ? $tree->data->id : -1) ?>"><?= str_pad('', $indent, '-') ?><?= HTML::chars($tree->data->title) ?></option>
<?php if($tree->children): ?>
<?php foreach($tree->children as $i => $child): ?>
<?= View::factory('partial/admin/taxonomy/options', array(
    'tree' => $child, 'indent' => $indent+1, 'selected' => isset($selected) ? $selected : false, 
    'exclude' => isset($exclude) && is_array($exclude) ? $exclude : false
)) ?>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>
