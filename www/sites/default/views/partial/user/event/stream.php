<?php if(isset($stream)): ?>
<div class="user-event-stream"><!-- user/event/stream -->
<?php $lastdate = ''; ?>
<?php foreach($stream as $i => $event): ?>
<?php if(isset($event['tag'])): ?>
<div class="user-event <?= $event['tag'] ?>">
<p>
    <?php 
        $thisdate = date('F d, Y', $event['timestamp']);
        if ($thisdate != $lastdate){
            ?><h3 class="date"><?php
            print date('F d, Y', $event['timestamp']); 
            $lastdate = $thisdate; 
            ?></h3><?php
        }
    ?> 
</p>
<p>
    <span class="event"><?= View::factory('partial/user/event/'.$event['tag'], (array)$event['data']) ?></span>
</p>
</div>
<?php endif; ?>
<?php endforeach; ?>
</div><!-- end user/event/stream -->
<?php else: ?>
<p>No events.</p>
<?php endif; ?>
