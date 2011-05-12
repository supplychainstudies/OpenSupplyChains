<option value="<?= HTML::chars(isset($tree->data->id) ? $tree->data->id : -1) ?>"><?= str_pad('', $indent, '-') ?><?= HTML::chars($tree->data->title) ?></option>
<?php if($tree->children): ?>
<?php foreach($tree->children as $i => $child): ?>
<?= View::factory('partial/admin/taxonomy/options', array('tree' => $child, 'indent' => $indent+1)) ?>
<?php endforeach; ?>
<?php endif; ?>
