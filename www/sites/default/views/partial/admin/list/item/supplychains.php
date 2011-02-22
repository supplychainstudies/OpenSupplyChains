<td><?= $item->id ?></td>
<td><a href="admin/supplychains/<?= $item->id ?>"><?= Html::chars($item->created) ?></a></td>
<td><?= Html::chars($item->owner) ?></td>
<form name="delete-supplychain" method="post" action="admin/supplychains/<?= $item->id?>/delete_supplychain">
<td><input type ="submit" value="delete" /></form></td>



