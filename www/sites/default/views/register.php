<div class="register-form" style="float: left; width: 5.2em">
<fieldset>
<form name="register" method="post" action="register">
     <label for="username">Username:</label><input type="text" name="username" <?php if(isset($username)){?> value="<?=$username?>" <?}?> /><br />
     <label for="email">Email:</label><input type="text" name="email"  <?php if(isset($email)) {?> value="<?=$email?>" <?}?> /><br /> 
     <label for="password">Password:</label><input name="password" <?php if(isset($password)) {?> value="<?=$password?>" type="text" <?} else { ?>type="password"<?}?>/><br />
     <label for="confirm_password">Confirm:</label><input name="confirm_password" <?php if(isset($password)) {?> value="<?=$password?>" type="text" <?} else { ?>type="password"<?}?>/><br />
     <?if (isset($identifier)) {?><input type="hidden" name="identifier" value="<?=$identifier?>"/><br /><?}?>
<input type="submit" value="Register" name="register" /></form>
<br />

</div>
     <div class="social-signin" style="float: right";>     
<iframe src="http://sourcemap1.rpxnow.com/openid/embed?token_url=http%3A%2F%2Falpha.sourcemap.org%2Fregister%2Floginopenid"  scrolling="no"  frameBorder="no"  allowtransparency="true"  style="width:400px;height:240px"></iframe>  

</div>
