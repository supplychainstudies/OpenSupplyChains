<?php if (isset($reset) && ($reset == true)): ?>
      Password is reset successfully!<br />
	  <?php endif;?>

<?= HTML::chars($user->username)?> last logged in on <?= $last_login?><br />

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
	 <form name="user-roles" method="post" action="admin/users/<?= $user->id?>/delete_role"><?= HTML::chars($roles[$i]['name'])?> <input type="hidden" name="role" value="<?=$roles[$i]['name']?>"><input type="submit" value="delete"/></form> 
       <? }?><br />
      <?php endif;?>


       
<?php if(count($roles) != count($all_roles)) { ?>
  <strong>Change or add the user role:</strong>
<form name="change-role" method="post" action="admin/users/<?= $user->id?>/add_role">
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
      }?>
<br />
					    
 <option value="<?php echo $role->name; ?>"><?= Html::chars($role->name) ?></option>

   <?php }?>
   </select><input type="submit" value="add role"/></form>

<? } ?><br />

<?php if(!empty($members)): ?>
<strong>Group Membership:</strong>
       <?php $last_member = end($members);?>
<?php foreach ($members as $member) { ?>
    <a href="admin/groups/<?=$member['id'];?>"><?=HTML::chars($member['name']) ?></a>
   <?php if ($member != $last_member) {?>, <?}?>
 <?php     }?><br />
 <?php endif; ?>


<?php if(!empty($owners)): ?>
  <strong>Group Ownership:</strong>
<?php $last_owner = end($owners);?>
<?php foreach ($owners as $owner) { ?>
    <a href="admin/groups/<?=$owner['id'];?>"><?=HTML::chars($owner['name']) ?></a>
 <?php if($owner != $last_owner) {?>, <?}?>
 <?php    }?>
     <?php endif; ?>
