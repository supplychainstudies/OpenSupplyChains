<td><?= $item->id ?></td>
<td><a href="admin/users/<?= $item->id ?>"><?= Html::chars($item->username) ?></a></td>
<td><?= Html::chars($item->email) ?></td>
<td><img src="<?= Gravatar::avatar($item->email, null, "mm") ?>" /></td>
<form name="delete-users" method="post" action="admin/users/<?= $item->id?>/delete_user">
<td><input type ="submit" value="delete" /></form></td>
