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

<div id="user-profile">
    <div class="user-avatar">
        <img src="<?= $user->avatar; ?>" />
        <?php if ($canEdit): ?>
        <div class="button alternate">
            <a href="http://www.gravatar.com/<?= Gravatar::hash($user->email) ?>">Change photo</a>
        </div>
        <div class="button alternate">
            <a href="auth/reset">Change password</a>
        </div>
            <?php if (!$isChannel): ?>
            <div class="button alternate">
                <a href="/upgrade">Upgrade account</a>
            </div>
            <?php endif; ?>
            <?php if ($isChannel): ?>
        <div class="button alternate">
            <a href="auth/reset">Payments</a>
        </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <ul class="user-details">
        <li><span><?= HTML::chars($user->username); ?></span></li>
        <?php if ($canEdit): ?>
            <?= isset($user->email) ? '<li><span>' . HTML::chars($user->email) . '</span></li>' : ''; ?>
        <?php endif; ?>
        <?php if ($isChannel): ?>
        <li>
            <?php if ($canEdit): ?>
            <a href="#" title="display_name" class="edit-button"></a>
            <?php endif; ?>
            <p id="display_name"><?= $user->display_name ? HTML::chars($user->display_name) : "<span class=\"empty\">No display name yet!</span>"; ?></p>
        </li>
        <li>
            <?php if ($canEdit): ?>
            <a href="#" title="banner_url" class="edit-button"></a>
            <?php endif; ?>
            <p id="banner_url"><?= isset($user->banner_url) ? HTML::chars($user->banner_url) : "<span class=\"empty\">No banner URL yet!</span>"; ?></p>
        </li>
        <?php endif; ?>
        <li>
            <?php if ($canEdit): ?>
            <a href="#" title="url" class="edit-button"></a>
            <?php endif; ?>
            <p id="url"><?= isset($user->url) ? HTML::chars($user->url) : "<span class=\"empty\">No URL yet!</span>"; ?></p>
        </li>
        <li>
            <?php if ($canEdit): ?>
            <a href="#" title="description" class="edit-button"></a>
            <?php endif; ?>
            <p id="description"><?= isset($user->description) ? HTML::chars($user->description) : "<span class=\"empty\">No description yet!</span>"; ?></p>
        </li>
        <!-- <li>Last Signed In: <span><?= date('F j, Y', $user->last_login) ?></span></li>-->
    </ul>
    <div class="clear"></div>
</div>