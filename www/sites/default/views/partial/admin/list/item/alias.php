<td><?= $item->id ?></td>
<td><?= Html::chars($item->site) ?></a></td>
<td><?= Html::chars($item->alias) ?></td>
<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->supplychain_id?></td>
<form name="delete-alias" method="post" action="admin/aliases/<?= $item->id?>/delete_supplychain_alias">
<td><input type ="submit" value="delete" /></form></td>

