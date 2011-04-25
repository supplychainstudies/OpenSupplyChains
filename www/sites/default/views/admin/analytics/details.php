<h3>Today on Sourcemap</h3>
<p>
    <?php if(isset($today_users) && $today_users): ?>&raquo;<a href="#new-users">New Users</a><?php endif; ?>
    <?php if(isset($today_supplychains) && $today_supplychains): ?>&raquo;<a href="#new-supplychains">New Supplychains</a><?php endif; ?>
    <?php if(isset($today_usergroups) && $today_usergroups): ?>&raquo;<a href="#new-groups">New Groups</a><?php endif; ?>
    <?php if(isset($user_logins) && $user_logins): ?>&raquo;<a href="#logins">Logins</a><?php endif; ?>
</p>

<?php if(isset($user_logins) && $user_logins): ?>
<h4><span class="good-news"><?= count($user_logins) ?></a> User Login<?php count($user_logins) > 1 ? 's' : '' ?></h4>
<ol>
<?php foreach($user_logins as $login):?>
    <li><a href="admin/users/<?= $login->id ?>"><?= HTML::chars($login->username) ?></a> at <?= $login->last_login ?></li>
 <?php endforeach;?>
</ol>
<?php else: ?>
<h4 class="bad-news">No user logins.</h4>
<?php endif; ?>


<?php if(isset($today_users) && $today_users): ?>
<h4 id="new-users"><span class="good-news"><?= count($today_users) ?></span> New User Registration<?= count($today_users) > 1 ? 's' : '' ?></h4>
<ol>
<?php foreach($today_users as $user): ?>
<li><a href="admin/users/<?= $user->id ?>"><?= HTML::chars($user->username) ?></a> - <?= HTML::chars($user->email) ?></li>
<?php endforeach;?>
</ol>
<?php else: ?>
<h4 class="bad-news">No new users.</h4>
<?php endif; ?>

<?php if(isset($today_supplychains) && $today_supplychains): ?>
<h4><span class="good-news"><?= count($today_supplychains) ?></span> New Supplychain<?= count($today_supplychains) > 1 ? 's' : '' ?></h4>
<ol>
<?php foreach($today_supplychains as $supplychain):?>
    <li><a href="map/view/<?=$supplychain->id?>">
        <?= isset($supplychain->attributes->name) ? HTML::chars($supplychain->attributes->name) : 
            (isset($supplychain->attributes->title) ? HTML::chars($supplychain->attributes->title) : '-unnamed-')
        ?></a> at <?= date('h:i a', $supplychain->created) ?></li>
<?php endforeach;?>
</ol>
<?php else: ?>
<h4 class="bad-news">No new supplychains.</h4>
<?php endif; ?>

<?php if(isset($today_usergroups) && $today_usergroups): ?>
<h4><span class="good-news"><?= count($today_usergroups) ?></span> New User Group<?= count($today_usergroups) > 1 ? 's' : '' ?></h4>
<ol>
<?php foreach($today_usergroups as $group):?>
    <li><a href="admin/groups/<?=$group->id?>"><?= HTML::chars($group->name) ?></a></li>
 <?php endforeach;?>
<?php else: ?>
<h4 class="bad-news">No new user groups.</h4>
<?php endif; ?>


