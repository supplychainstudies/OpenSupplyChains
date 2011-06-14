<?php if(isset($messages) && is_array($messages) && $messages): ?>
<div class="status-wrap">
<ul class="status-messages">
<?php foreach($messages as $i => $msg): ?>
<li class="status-message <?= isset($msg->level) ? HTML::chars($msg->level).' ' : '' ?><?= $i % 2 ? 'odd' : 'even' ?>">
<div class="status-wrap">
<?= isset($msg->message) ? HTML::chars($msg->message) : '' ?>
</div>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
