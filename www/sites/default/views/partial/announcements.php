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


<?php $announcements = Sourcemap_User_Event::get_announcements(); ?>
<?php if(isset($announcements)): ?>

<div class="container full">
    <div class="messages">
        <div class="status-wrap">
            <ul class="status-messages announcements">
                <?php foreach($announcements as $announcement): ?>
                <li class="status-message announcement">
                    <?php $message = json_decode($announcement['data']) ?>
                    <div class="status-wrap"><?= $message->message ?></div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>
