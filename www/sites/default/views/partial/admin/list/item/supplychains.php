<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->id ?></a></td>
<td><?= HTML::chars($item->created) ?></td>
<td><?= HTML::chars($item->owner) ?></td>
<td><?= HTML::chars(isset($item->attributes['title']) ? $item->attributes['title'] : '') ?></td>
<td><a href="static/<?= $item->id ?>.l.png" target="map_preview"><img src="static/<?= $item->id ?>.m.png" /></a></td>

<form name="delete-supplychain" method="post" action="admin/supplychains/<?= $item->id?>/delete_supplychain">
<td><input type ="submit" value="delete" /></form></td>



