<td><?= $item->id ?></td>
<td><?= $item->message ?></td>
<td><?= date('F d, Y @ H:ia', $item->timestamp) ?></td>
<td>
<form name="delete-announcement-entry" method="post" action="admin/announcements/delete">
    <input name="user_event_id" type="hidden" value="<?= $item->id ?>" />
    <input type ="submit" value="delete" />
</form>
</td>

