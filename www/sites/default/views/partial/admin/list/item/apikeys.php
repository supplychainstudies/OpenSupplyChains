<td><?= $item->apikey ?></td>
<td><?= $item->apisecret ?></td>
<td><?= date('r', $item->created) ?></td>
<td><?= $item->id ?></td>
<td><?= $item->requests ?></td>
<td><a href="admin/users/<?= $item->user_id ?>"><?= Html::chars($item->owner->username) ?></a></td>
