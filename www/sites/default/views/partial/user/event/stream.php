<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/ 
?>

<?php if(isset($stream)): ?>
<div class="user-event-stream"><!-- user/event/stream -->
<?php $lastdate = ''; ?>
<?php if(count($stream) == 0){ ?>
    <div class="user-event">
        <p>
            No activity yet!  Start by <a href="/create">creating</a> a map.
        </p>
    </div>
<?php } else {?>
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
<?php } ?>
</div><!-- end user/event/stream -->
<?php else: ?>
<p>No events.</p>
<?php endif; ?>
