<td><?= $item->id ?></td>
<td><?= Html::chars($item->created) ?></td>
<td><?= Html::chars($item->owner) ?></td>
<td><a href="admin/supplychains/<?= $item->id ?>"><?= isset($item->attributes['name']) ? Html::chars($item->key) : '-unnamed-' ?></a></td>


