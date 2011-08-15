<td><?= $item->id ?></td>
<td><a href="admin/users/<?= $item->id ?>"><?= Html::chars($item->username) ?></a></td>
<td><?= Html::chars($item->email) ?></td>
<td><img width="32px" height="32px" src="<?= Gravatar::avatar($item->email, 32) ?>" /></td>
<form name="delete-users" method="post" action="admin/users/<?= $item->id?>/delete_user">
<td><input type ="submit" value="delete" /></form></td>
