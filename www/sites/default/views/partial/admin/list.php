<?php if(isset($list)): ?>
    <?php if($list): ?>
        <table class="admin-list">
        <?php $items = array_values($list) ?>
        <?php $headings = array_keys((array)$items[0]) ?>
        <?php try {
                $partial = View::factory('partial/admin/list/head/supplychains', array('headings' => $headings));
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
                $partial = View::factory('partial/admin/list/item/supplychains');
            } catch(Exception $e) {
                $partial = false;
            }
        ?>
        <?php foreach($list as $lk => $item): ?>
            <tr>
                <?php if($partial): ?>
                    <?php $partial->item = $item; ?>
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
