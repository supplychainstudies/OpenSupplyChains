<form name="group-info" method="post" action="admin/groups/<?= $group_id?>/add_member">
     <label for="username">username(s):</label><br />
<input type="text" name="username" class="input text" value=""/>
<input type="submit" value="Add" />
</form><br />

Groupname: <?=$group_name;?><br />
Owner: <?=$owner; ?><br />
<?php if(!empty($members)): ?>
Group Members: 
       <?php $last_member = end($members);?>
<?php foreach ($members as $member) { ?>
     <?= $member['username']; ?> 
   <?php if ($member != $last_member) {?>, <?}?>
 <?php     }?><br />
 <?php endif; ?><br />