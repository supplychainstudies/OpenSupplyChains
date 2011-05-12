<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->id ?></a></td>
<td><?= HTML::chars($item->created) ?></td>
<td><?= HTML::chars($item->owner) ?></td>
<td><?= HTML::chars(isset($item->attributes['title']) ? $item->attributes['title'] : '') ?></td>

<form name="delete-supplychain" method="post" action="admin/supplychains/<?= $item->id?>/delete_supplychain">
<td><input type ="submit" value="delete" /></form></td>



