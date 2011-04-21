The last administrator login was on <strong><?= $admin_lastlogin?></strong><br /><br />

<?if($supplychain_lastweek != 0  || $user_lastweek != 0 || $user_lastlogin){?><strong>In the last week</strong> <?if($supplychain_lastweek != 0) {?><strong><?= $supplychain_lastweek?></strong> supplychains have been created, <?}?><?if($user_lastweek) {?><strong><?= $user_lastweek?></strong> users have been created  and <?}?><?if($user_lastlogin){ ?>the number of user(s) log in is <strong><?= $user_lastlogin?></strong><?} }?><br /><br />

The largest map so far has <strong><?= $stop?></strong> stops(<a href="map/view/<?= $supplychain_id?>">view map</a>).<br /><br />


    <?if($user_today != 0 || $supplychain_today !=0) {?><strong>Today we have</strong> <?if($user_today != 0) {?><strong><?= $user_today?></strong> users creating their accounts<?}?> <?if($supplychain_today != 0){?><strong><?= $supplychain_today?></strong> supplychains have been created today.<?}}?>

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
<dt><a href="admin/analytics">Analytics</a></dt>
<dd>Sourcemap Analytics.</dd>
<dt><a href="admin/announcements">Announcements</a></dt>
<dd>Sourcemap Analytics.</dd>
</dl>
