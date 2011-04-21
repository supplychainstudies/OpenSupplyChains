<?php if(isset($stream)): ?>
<div class="user-event-stream">
<?php foreach($stream as $i => $event): ?>
<?php if(isset($event['tag'])): ?>
<div class="user-event <?= $event['tag'] ?>">
<p>
    <span class="time"><?= date('F d, Y @ H:i:s', $event['timestamp']) ?></span>
</p>
<?= View::factory('partial/user/event/'.$event['tag'], (array)$event['data']) ?>
</div>
<?php endif; ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No events.</p>
<?php endif; ?>
