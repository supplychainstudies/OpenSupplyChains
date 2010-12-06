Hello <? echo $user->username;?>! 

<form name="user-info" method="post">
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


<strong>User Role</strong>
<select>
<? foreach ($all_roles as $role) { ?>
<option value="<?php echo $role['name']; ?>"  <?if($roles[0]['name'] == $role['name']) { echo "selected"; } ?>><?php echo $role['name']; ?></option>
<? } ?>
</select>

