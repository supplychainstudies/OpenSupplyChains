<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->id ?></a></td>
<td><?= Html::chars($item->created) ?></td>
<td><?= Html::chars($item->owner) ?></td>
  <td>  <?foreach($item->title as $title) {?>
     <?if($title['key'] == "name" || "title") {?>
        <?=HTML::chars($title['value'])?></td>
       <?}?>
    <?}?>

<form name="delete-supplychain" method="post" action="admin/supplychains/<?= $item->id?>/delete_supplychain">
<td><input type ="submit" value="delete" /></form></td>



