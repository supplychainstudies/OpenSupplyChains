<form name="group-info" method="post" action="admin/groups/<?= $group_id?>/add_member">
     <label for="username">username(s):</label><br />
<input type="text" name="username" class="input text" value=""/>
<input type="submit" value="Add" />
</form><br />

<strong>Groupname:</strong> <?=$group_name;?><br />
<strong>Owner:</strong> <?=$owner; ?><br />
<?php if(!empty($members)): ?>
<strong>Group Members:</strong> 
<?php foreach ($members as $member) { ?>
     <form name="delete-member" method="post" action="admin/groups/<?= $group_id?>/delete_member">
	 <?=$member['username'];?>
     <input type="hidden" name="username" value="<?= $member['username'];?>"><input type="submit" value="delete"/></form> 
 <?php     }?><br />
 <?php endif; ?><br />