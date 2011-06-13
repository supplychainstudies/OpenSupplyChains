<div class="comment-meta" id="comment-<?= $comment->id ?>">
<img class="user-avatar" src="<?= $comment->avatar ?>" />
<p class="comment-meta-text">
Added by <a class="user-link" href=""><?= $comment->username ?></a> on <?= date('F d, Y', $comment->timestamp) ?> @ <?= date('H:i a', $comment->timestamp) ?>.
</p>
<div class="clear"></div>                        
</div>
<div class="comment-body">
<p><?= HTML::chars($comment->body) ?></p>
</div>

