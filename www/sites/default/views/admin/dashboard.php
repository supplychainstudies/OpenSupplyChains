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

<div id="summary">
The last administrator login was on <strong><?php $admin_lastlogin?></strong>.
In the last week,
<?php if($supplychain_lastweek > 0): ?>
    <strong><?php $supplychain_lastweek?></strong> supplychains have been created,
<?php else: ?>
    <strong class="bad-news">nobody</strong> created any supplychains,
<?php endif; ?>
<?php if($user_lastweek > 0): ?>
    <strong><?php $user_lastweek?></strong> user<?php $user_lastweek > 1 ? 's' : '' ?> registered (or <?php $user_lastweek > 1 ? 'were' : 'was' ?> created) and 
<?php else: ?>
    <strong class="bad-news">no new users</strong> registered, and
<?php endif; ?>
<?php if($user_lastlogin > 0): ?>
    <strong><?php $user_lastlogin ?> user<?php $user_lastlogin > 1 ? 's' : '' ?></strong> <?php $user_lastlogin > 1 ? 'have' : 'has' ?> signed in at least once.
<?php else: ?>
    <strong class="bad-news">nobody</strong> signed in at all.
<?php endif; ?>

<?php if($supplychain_id): ?>
The <a href="view/<?php $supplychain_id ?>">largest map</a> so far has 
    <strong><?php $stop ?></strong> stops.
<?php else: ?>
There don't seem to be any supplychains yet. <a href="map/create">Make some</a>!
<?php endif; ?>

Today,
<?php if($user_today > 0): ?>
    <strong><?php $user_today ?></strong> <?php $user_today > 1 ? 'users have' : 'user has' ?> registered,
<?php else: ?> 
    <strong class="bad-news">nobody</strong> has registered,
<?php endif; ?>
and
<?php if($supplychain_today > 0): ?>
    <strong><?php $supplychain_today ?></strong> <?php $supplychain_today > 1 ? 'supplychains have' : 'supplychain has' ?> been created.
<?php else: ?>
    <strong class="bad-news">zero</strong> supplychains have been created.
<?php endif; ?>
</div>

<h3>Import</h3>
<dl>
<dt><a href="tools/import/csv">CSV Import</a></dt>
<dd>Import data from uploaded comma-delimited files.</dd>
<dt><a href="tools/import/google">Google Spreadsheet Import</a></dt>
<dd>Import data from Google Spreadsheets.</dd>
</dl>

<h3>Management</h3>
<dl>
<dt><a href="admin/aliases">Aliases</a></dt>
<dd>Manage Aliases.</dd>
<dt><a href="admin/analytics">Analytics</a></dt>
<dd>Sourcemap Analytics.</dd>
<dt><a href="admin/announcements">Announcements</a></dt>
<dd>System-wide announcements.</dd>
<dt><a href="admin/taxonomy">Categories</a></dt>
<dd>Manage map categories.</dd>
<dt><a href="admin/comments">Comments</a></dt>
<dd>Moderate and manage comments.</dd>
<dt><a href="admin/featured">Featured Supplychains</a></dt>
<dd>Choose which maps are "featured".</dd>
<dt><a href="admin/groups">Groups</a></dt>
<dd>Manage groups.</dd>
<dt><a href="admin/migrate">Migration</a></dt>
<dd>Migrate users from sourcemap.org to sourcemap.com.</dd>
<dt><a href="admin/roles">Roles</a></dt>
<dd>Manage Roles.</dd>
<dt><a href="admin/supplychains">Supplychains</a></dt>
<dd>Browse and manage supplychains.</dd>
<dt><a href="admin/users">Users</a></dt>
<dd>Manage users.</dd>
</dl>
