<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/ 
?>

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
