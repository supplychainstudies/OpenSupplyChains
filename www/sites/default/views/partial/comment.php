<li class="comment" id="comment-<?= $comment->id ?>">
<img class="user-avatar" src="<?= $comment->avatar ?>" />
Posted on <a class="user-link" href="user/<?= $comment->username ?>"><?= $comment->username ?></a> on <?= date('F d, Y', $comment->timestamp) ?> @ <?= date('H:i a', $comment->timestamp) ?>.
<p><?= HTML::chars($comment->body) ?></p>
</li>

