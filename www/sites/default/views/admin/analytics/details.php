<strong>Todays Highlights</strong> 
<p>Here are users that were created:
<?php $user_end = end($today_users);?>
<?php foreach($today_users as $user):?>
<a href="admin/users/<?=$user->id?>"><?=$user->username?></a>
	     <?php if($user_end->username != $user->username){?>, <?}?>
     <?php endforeach;?>
</p>

<p>Supplychains created:
<?php $supplychain_end = end($today_supplychains);?>
<?php $counter =1;?>
<?php foreach($today_supplychains as $supplychain):?>
<a href="map/view/<?=$supplychain->id?>"><?=$counter?></a>
  <?php if($supplychain_end->id != $supplychain->id){?>, <?}?>
  <?php $counter++;?>
  <?php endforeach;?>
</p>


<p>Groups created:
<?php $group_end = end($today_usergroups);?>
<?php foreach($today_usergroups as $group):?>
<a href="admin/groups/<?=$group->id?>"><?=$group->name?></a>
	     <?php if($group_end->name != $group->name){?>, <?}?>
     <?php endforeach;?>
</p>

<p>Users logged in:
<?php $user_login = end($user_logins);?>
<?php foreach($user_logins as $login):?>
<a href="admin/users/<?=$login->id?>"><?=$login->username?></a> at <?=$login->last_login?>
	     <?php if($user_login->username != $login->username){?>, <?}?>
     <?php endforeach;?>
</p>