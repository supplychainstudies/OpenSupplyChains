<?php foreach($messages as $i => $message): ?>
<div class="flash-message <?= HTML::chars($message->level)?>">
<?= HTML::chars($message->message) ?>
</div>
<?php endforeach; ?>
