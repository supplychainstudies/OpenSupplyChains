<form name="group-info" method="post" action="admin/groups/<?= $group->id?>/add_member">
     <label for="username">username(s):</label><br />
<input type="text" name="username" class="input text" value=""/>
<input type="submit" value="Add" />
</form><br />

<strong>Groupname:</strong> <?=HTML::chars($group->name)?><br />
     <strong>Owner:</strong> <a href="admin/users/<?=$owner['id'];?>"><?=HTML::chars($owner) ?></a><br />

<?php if(!empty($members)): ?>
<strong>Group Members:</strong> 
<table>
 <?php foreach ($members as $member): ?>
 <tr>
     <form name="delete-member" method="post" action="admin/groups/<?= $group->id?>/delete_member">
   <td><a href="admin/users/<?=$member['id'];?>"><?=HTML::chars($member['username'])?></a></td>
   <td><input type="hidden" name="username" value="<?= $member['username'];?>"><input type="submit" value="delete"/></form></td> 
 </tr>
 <?php endforeach;?><br />
</table>
 <?php endif; ?><br />