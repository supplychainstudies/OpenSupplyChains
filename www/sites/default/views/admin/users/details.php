<?php if (isset($reset) && ($reset == true)) { ?>
    Password is reset successfully!<br />
    <?}?>

<form name="user-info" method="post" action="">
<label for="username">username:</label><br />
<input type="text" name="username" class="input text" value="<? echo $user->username?>"/><br />
<label for="email">email:</label><br />
<input type="text" name="email" class="input text" value="<? echo $user->email?>"/><br />
<label for="password">password:</label><br />
<input type="password" name="password" class="input text password" /><br />
<label for="confirmpassword">confirm password:</label><br />
<input type="password" name="confirmpassword" class="input text password" /><br />
<input type="submit" value="reset" />
</form><br />

    <?if(isset($roles) && count($roles)>0 && !empty($all_roles)) {?>
  <strong>User Role</strong><br />
    <?php foreach ($roles as $i => $k) { ?>
    <form name="user-roles" method="post" action="admin/users/<?= $user->id?>/delete"><?php echo $roles[$i]['name'];?> <input type="hidden" name="role" value="<?=$roles[$i]['name']?>"><input type="submit" value="delete"/></form> 
<? }?><br />
 <? }?>

<?php if (isset($all_roles) && count($all_roles)>0): ?>

   Change the user role:
<form name="change-role" method="post" action="admin/users/<?= $user->id?>/add">
<select name="addrole">
<? foreach ($all_roles as $role) { ?>			
    <? $skip = false; 
       foreach($roles as $roler) {
         if($roler['name'] == $role->name) {
	   $skip = true; 
	   break; 
	 }
      } 
      if($skip) {
	continue;
      }
?><br />
					    
   <option value="<?php echo $role->name; ?>"><?php echo $role->name; ?></option>

   <?php }?>
   </select><input type="submit" value="add role"/></form>
    <?php endif; ?>

   Group Information: 
<? foreach ($groups as $group) { ?>
     <?= $group['name']; 
     }?>
    
