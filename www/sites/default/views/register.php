<div class="register-form" style="float: left; width: 5.2em">
<fieldset>
<form name="register" method="post" action="">
     <label for="username">Username:</label><input type="text" name="username" <?php if(isset($username)){?> value="<?=$username?>" <?}?> /><br />
     <label for="email">Email:</label><input type="text" name="email"  <?php if(isset($email)) {?> value="<?=$email?>" <?}?> /><br /> 
     <label for="password">Password:</label><input name="password" <?php if(isset($password)) {?> value="<?=$password?>" type="text" <?} else { ?>type="password"<?}?>/><br />
     <label for="confirm_password">Confirm:</label>     <input name="confirm_password" <?php if(isset($password)) {?> value="<?=$password?>" type="text" <?} else { ?>type="password"<?}?>/><br />
<input type="submit" value="Register" name="register" />

</div>


