<td><?= $item->id ?></td>
<td><?= Html::chars($item->name) ?></td>
<td><?= Html::chars($item->description) ?></td>
<form name="delete-role-entry" method="post" action="admin/roles/<?= $item->id?>/delete_role_entry">
<td><input type ="submit" value="delete" /></form></td>

