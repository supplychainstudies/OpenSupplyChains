<div id="summary">
<p>The last administrator login was on <strong><?= $admin_lastlogin?></strong>.
In the last week,
<?php if($supplychain_lastweek > 0): ?>
    <strong><?= $supplychain_lastweek?></strong> supplychains have been created,
<?php else: ?>
    <strong class="bad-news">nobody</strong> created any supplychains,
<?php endif; ?>
<?php if($user_lastweek > 0): ?>
    <strong><?= $user_lastweek?></strong> users registered (or were created) and 
<?php else: ?>
    <strong class="bad-news">no new users</strong> registered, and
<?php endif; ?>
<?php if($user_lastlogin > 0): ?>
    <strong><?= $user_lastlogin ?> users</strong> have logged in at least once.
<?php else: ?>
    <strong class="bad-news">nobody</strong> logged in at all.
<?php endif; ?>
</p>

<p>
<?php if($supplychain_id): ?>
The <a href="map/view/<?= $supplychain_id ?>">largest map</a> so far has 
    <strong><?= $stop ?></strong> stops.
<?php else: ?>
There don't seem to be any supplychains yet. <a href="map/create">Make some</a>!
<?php endif; ?>
</p>
<p>
Today,
<?php if($user_today > 0): ?>
    <strong><?= $user_today ?></strong> users have registered,
<?php else: ?> 
    <strong class="bad-news">nobody</strong> has registered,
<?php endif; ?>
and
<?php if($supplychain_today > 0): ?>
    <strong><?= $supplychain_today ?></strong> supplychains have been created.
<?php else: ?>
    <strong class="bad-news">zero</strong> supplychains have been created.
<?php endif; ?>
</p>
</div>

<dl>
<dt><a href="admin/supplychains">Supplychains</a></dt>
<dd>Browse and manage supplychains.</dd>
<dt><a href="admin/users">Users</a></dt>
<dd>Manage users.</dd>
<dt><a href="admin/groups">Groups</a></dt>
<dd>Manage groups.</dd>
<dt><a href="admin/roles">Roles</a></dt>
<dd>Manage Roles.</dd>
<dt><a href="admin/aliases">Aliases</a></dt>
<dd>Manage Aliases.</dd>
<dt><a href="admin/analytics">Analytics</a></dt>
<dd>Sourcemap Analytics.</dd>
<dt><a href="admin/announcements">Announcements</a></dt>
<dd>Sourcemap Analytics.</dd>
</dl>
