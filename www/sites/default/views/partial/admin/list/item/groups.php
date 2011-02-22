<td><?= $item->id ?></td>
<td><a href="admin/groups/<?= $item->id ?>"><?= Html::chars($item->name) ?></a></td>
<td><?= Html::chars($item->owner) ?></td>
<form name="delete-group" method="post" action="admin/groups/<?= $item->id?>/delete_group">
<td><input type ="submit" value="delete" /></form></td>

