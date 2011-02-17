<?php if(isset($messages) && is_array($messages) && $messages): ?>
<ul class="status-messages">
<?php foreach($messages as $i => $msg): ?>
<li class="status-message <?= isset($msg->level) ? HTML::chars($msg->level).' ' : '' ?><?= $i % 2 ? 'odd' : 'even' ?>"><?= isset($msg->message) ? HTML::chars($msg->message) : '' ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
