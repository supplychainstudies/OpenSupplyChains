<td><?= HTML::chars($item->body) ?></td>
<td><?= HTML::anchor('admin/users/'.$item->user_id, HTML::chars($item->author)) ?></td>
<td><?= $item->posted ?></td>
<td><?= HTML::anchor('admin/supplychains/'.$item->supplychain_id, HTML::chars($item->map_title))  ?></td>
<td><?= HTML::anchor('view/'.$item->supplychain_id.'#comment-'.$item->id, 'view') ?></td>
<td>
<form name="flag-comment-entry" method="post" action="admin/comments/<?= $item->id ?>/flag">
    <?= Form::input('list_url', 'admin/comments'.URL::query(), array('type' => 'hidden')) ?> 
    <input name="flag_nm" type="hidden" value="abuse" />
    <?php if($item->flags & Sourcemap::ABUSE): ?>
    <input name="unflag" type="hidden" value="1" />
    <input type ="submit" value="unflag abuse" />
    <?php else: ?>
    <input name="flag" type="hidden" value="1" />
    <input type ="submit" value="flag abuse" />
    <?php endif; ?>

</form>
</td>
<td>
<form name="flag-comment-entry" method="post" action="admin/comments/<?= $item->id ?>/flag">
    <?= Form::input('list_url', 'admin/comments'.URL::query(), array('type' => 'hidden')) ?> 
    <input name="flag_nm" type="hidden" value="hidden" />
    <?php if($item->flags & Sourcemap::HIDDEN): ?>
    <input name="unflag" type="hidden" value="1" />
    <input type ="submit" value="unhide" />
    <?php else: ?>
    <input name="flag" type="hidden" value="1" />
    <input type ="submit" value="hide" />
    <?php endif; ?>
</form>
</td>

