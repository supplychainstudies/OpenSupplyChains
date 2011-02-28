<h2>Management Dashboard</h2>

<strong>Updates since last week:</strong> <strong><?= $supplychain_lastweek?></strong> supplychains have been created, <strong><?= $user_lastweek?></strong> users have been created  and <strong><?= $user_lastlogin?></strong> users have logged in since last week.<br />
The administrator last login was on <strong><?= $admin_lastlogin?></strong><br /><br />
The maximum number of stops entered so far are <strong><?= $stop?></strong> (<a href="map/view/<?= $supplychain_id?>">view map</a>)<br />
The maximum number of hops entered so far are <strong><?= $hop?></strong> (<a href="map/view/<?= $supplychain_hop_id?>">view map</a>)<br /><br />

<strong>Today's Updates:</strong> <strong><?= $user_today?></strong> users have been created today and <strong><?= $supplychain_today?></strong> supplychains have been created today.

<dl>
<dt><a href="admin/supplychains">Supplychains</a></dt>
<dd>Browse and manage supplychains.</dd>
<dt><a href="admin/users">Users</a></dt>
<dd>Manage users.</dd>
<dt><a href="admin/groups">Groups</a></dt>
<dd>Manage groups.</dd>
<dt><a href="admin/roles">Roles</a></dt>
<dd>Manage Roles.</dd>
<dt><a href="admin/alias">Aliases</a></dt>
<dd>Manage Aliases.</dd>
</dl>
