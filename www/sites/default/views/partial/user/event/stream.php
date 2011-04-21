<?php if(isset($stream)): ?>
<div class="user-event-stream"><!-- user/event/stream -->
<?php foreach($stream as $i => $event): ?>
<?php if(isset($event['tag'])): ?>
<div class="user-event <?= $event['tag'] ?>">
<p>
    <span class="time"><?= date('F d, Y @ H:ia', $event['timestamp']) ?></span>
</p>
<?= View::factory('partial/user/event/'.$event['tag'], (array)$event['data']) ?>
</div>
<?php endif; ?>
<?php endforeach; ?>
</div><!-- end user/event/stream -->
<?php else: ?>
<p>No events.</p>
<?php endif; ?>
