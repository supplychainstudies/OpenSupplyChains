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

<div class="taxonomy-tree">
<dl>
    <dt>
		<?php if(isset($tree->data->id) && $tree->data->id): ?>
		<form method="post" class="delete-taxonomy" action="admin/taxonomy/rm">
		<input type="hidden" name="taxonomy_id" value="<?= HTML::chars($tree->data->id) ?>" />
		<input type="submit" value="" name="rm" />
		</form>
		<?php endif; ?>
		<?php if(isset($tree->data->id)): ?><a href="admin/taxonomy/<?= $tree->data->id ?>/edit"><?php endif; ?>
        <?= HTML::chars($tree->data->title) ?>
        <?php if(isset($tree->data->id)): ?></a><?php endif; ?>
    </dt>
</dl>

<?php if($tree->children): ?>
<ul class="children">
    <?php foreach($tree->children as $i => $child): ?>
    <li><?= View::factory('partial/admin/taxonomy/tree', array('tree' => $child)) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
