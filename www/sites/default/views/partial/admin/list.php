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

<?php if(isset($list, $list_type)): ?>
    <?php if($list): ?>
        <table class="admin-list">
        <?php $items = array_values(is_array($list) ? $list : $list->as_array()) ?>
        <?php $headings = array_keys(is_array($items[0]) ? $items[0] : (is_callable(array($items[0], 'as_array')) ? $items[0]->as_array() : (array)$items[0])) ?>
        <?php try {
                $partial = View::factory('partial/admin/list/head/'.$list_type, array('headings' => $headings));
            } catch(Exception $e) {
                $partial = false;
            }
        ?>
        <?php if($partial): ?>
            <?= $partial ?>
        <?php else: ?>
        <thead>
            <tr>
                <?php foreach($headings as $hi => $h): ?>
                <th><?= Html::chars($h) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <?php endif; ?>
        <tbody>
        <?php try {
                $partial = View::factory('partial/admin/list/item/'.$list_type);
            } catch(Exception $e) {
                $partial = false;
            }
        ?>
        <?php $icount = 0; foreach($list as $lk => $item): ?>
            <tr class="<?= $icount++ % 2 ? 'odd' : 'even' ?>">
                <?php if($partial): ?>
                    <?php $partial->item = is_object($item) ? $item : (object)$item; ?>
                    <?= $partial ?>
                <?php else: ?>
                    <?php foreach($item as $ik => $iv): ?>
                        <td><?= Html::chars($iv); ?></td>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
    <?php endif; ?>
<?php else: ?>
<h3 class="bad-news">There's nothing here.</h3>
<?php endif; ?>
