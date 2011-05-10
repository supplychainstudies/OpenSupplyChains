<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->id ?></a></td>
<td><?= Html::chars($item->created) ?></td>
<td><?= Html::chars($item->owner) ?></td>
<td>
    <?php foreach($item->title as $title): ?>
    <?php if($title['key'] == "name" || "title"): ?>
        <?=HTML::chars($title['value'])?></td>
    <?php else: ?>
        n/a
    <?php endif; ?>
    <?php endforeach; ?>
</td>

<td>
<form name="unfeature-supplychain" method="post" action="admin/featured/<?= $item->id?>/remove">
<input type ="submit" value="unfeature" /></form>
</td>



