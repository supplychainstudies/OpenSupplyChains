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