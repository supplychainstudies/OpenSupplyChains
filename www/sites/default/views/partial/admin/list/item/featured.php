<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->id ?></a></td>
<td><?= Html::chars($item->created) ?></td>
<td><?= Html::chars($item->owner) ?></td>
<td>
    <?php if(isset($item->attributes['title'])): ?>
        <?= HTML::chars($item->attributes['title']) ?>
    <?php endif; ?>
</td>

<td>
<form name="unfeature-supplychain" method="post" action="admin/featured/<?= $item->id?>/remove">
<input type ="submit" value="unfeature" /></form>
</td>



