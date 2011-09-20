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

<li class="comment" id="comment-<?= $comment->id ?>">
<img class="user-avatar" src="<?= $comment->avatar ?>" />
Posted by <a class="user-link" href="user/<?= $comment->username ?>"><?= $comment->username ?></a> on <?= date('F d, Y', $comment->timestamp) ?> @ <?= date('H:i a', $comment->timestamp) ?>.
<p><?= HTML::chars($comment->body) ?></p>
</li>