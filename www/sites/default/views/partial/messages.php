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
