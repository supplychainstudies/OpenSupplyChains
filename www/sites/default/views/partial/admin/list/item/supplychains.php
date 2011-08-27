<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->id ?></a></td>
<td><?= HTML::chars($item->created) ?></td>
<td><?= HTML::chars($item->owner) ?></td>
<td><?= HTML::chars(isset($item->attributes['title']) ? $item->attributes['title'] : '') ?></td>
<td><a href="static/<?= $item->id ?>.l.png" target="map_preview"><img class="medium" src="static/<?= $item->id ?>.m.png" /></a></td>

<form name="refresh-supplychain" method="post" action="admin/supplychains/<?= $item->id?>/refresh_supplychain">
<td><input type ="submit" value="refresh" /></form></td>



