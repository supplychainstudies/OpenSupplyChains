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

<form name="group-info" method="post" action="admin/groups/<?php $group->id?>/add_member">
     <label for="username">username(s):</label><br />
<input type="text" name="username" class="input text" value=""/>
<input type="submit" value="Add" />
</form><br />

<strong>Groupname:</strong> <?phpHTML::chars($group->name)?><br />
     <strong>Owner:</strong> <a href="admin/users/<?php$owner['id'];?>"><?phpHTML::chars($owner) ?></a><br />

<?php if(!empty($members)): ?>
<strong>Group Members:</strong> 
<table>
 <?php foreach ($members as $member): ?>
 <tr>
     <form name="delete-member" method="post" action="admin/groups/<?php $group->id?>/delete_member">
   <td><a href="admin/users/<?php$member['id'];?>"><?phpHTML::chars($member['username'])?></a></td>
   <td><input type="hidden" name="username" value="<?php $member['username'];?>"><input type="submit" value="delete"/></form></td> 
 </tr>
 <?php endforeach;?><br />
</table>
 <?php endif; ?><br />