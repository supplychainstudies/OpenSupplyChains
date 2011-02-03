<?php if (isset($reset) && ($reset == true)): ?>
      Password is reset successfully!<br />
	  <?php endif;?>

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

      <?php if(isset($roles) && count($roles)>0):?>
  <strong>User Role</strong><br />
    <?php foreach ($roles as $i => $k) { ?>
    <form name="user-roles" method="post" action="admin/users/<?= $user->id?>/delete"><?php echo $roles[$i]['name'];?> <input type="hidden" name="role" value="<?=$roles[$i]['name']?>"><input type="submit" value="delete"/></form> 
       <? }?><br />
      <?php endif;?>


       
<?php if(count($roles) != count($all_roles)) { ?>
   Change or add the user role:
<form name="change-role" method="post" action="admin/users/<?= $user->id?>/add">
<select name="addrole">
<?php foreach ($all_roles as $role) { ?>			
    <?php $skip = false; 
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

<? } ?>

<?php if(!empty($groups)): ?>
   Group Information: 
<?php foreach ($groups as $group) { ?>
     <?= $group['name']; 
     }?>
     <?php endif; ?>
