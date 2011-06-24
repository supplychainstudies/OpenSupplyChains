<div class="user-details<?= $user->has_flag(Sourcemap::VERIFIED) ? ' verified' : '' ?>">
<?= HTML::chars($user->username)?> last logged in on <?= $last_login?><br />
<fieldset><legend>Flags</legend>
<form name="user-verification" method="post" action="admin/users/<?= $user->id ?>/flags">
<label for="verfified">Verified</label>
<input type="checkbox" name="verified" <?= $user->has_flag(Sourcemap::VERIFIED) ? 'checked' : '' ?> />
<input type="submit" value="update" />
</form>
</fieldset>

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

<?php if(isset($roles) && $roles):?>
  <strong>User Role</strong><br />
    <?php foreach ($roles as $i => $k): ?>
     <form name="user-roles" method="post" action="admin/users/<?= $user->id?>/delete_role">
        <?= HTML::chars($roles[$i]['name'])?> 
        <input type="hidden" name="role" value="<?= HTML::chars($roles[$i]['name']) ?>">
        <input type="submit" value="delete"/></form> 
    <? endforeach; ?><br />
<?php endif;?>


       
<?php if(count($roles) != count($all_roles)): ?>
    <strong>Change or add the user role:</strong>
    <form name="change-role" method="post" action="admin/users/<?= $user->id?>/add_role">
    <select name="addrole">
    <?php foreach ($all_roles as $role): ?>
        <?php $has_role = false; ?>
        <?php foreach($roles as $roler): ?>
             <?php if($roler['name'] == $role->name) { $has_role = true; break; } ?>
        <?php endforeach; ?>
        <?php if(!$has_role): ?>
             <option value="<?= HTML::chars($role->name); ?>"><?= HTML::chars($role->name) ?></option>
         <?php endif; ?>
    <?php endforeach; ?>
    </select>
    <input type="submit" value="add role"/></form>
<?php endif; ?><br />

<?php if(!empty($members)): ?>
    <strong>Group Membership:</strong>
    <?php foreach ($members as $i => $memberof): ?>
        <a href="admin/groups/<?=$member['id'];?>">
            <?=HTML::chars($memberof['name']) ?>
        </a>
        <?= $i < (count($memberof)-1) ? ', ' : '' ?>
     <?php endforeach; ?><br />
 <?php endif; ?>

<?php if($owners): ?>
  <strong>Group Ownership:</strong>
    <?php foreach ($owners as $i => $owned): ?>
        <a href="admin/groups/<?=$owned['id'];?>">
            <?=HTML::chars($owned['name']) ?>
        </a><?= $i < (count($owned)-1) ? ', ' : '' ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if($apikeys): ?>
    <strong>API Keys</strong>
    <?php foreach($apikeys as $i => $apikey): ?>
        <div><?= $i ?></div>
    <?php endforeach; ?>
<?php endif; ?>
<fieldset>
<legend>Add an API Key for <?= HTML::chars($user->username) ?></legend>
<form method="post" action="admin/apikeys/add">
<input type="hidden" name="user_id" value="<?= $user->id ?>" />
<input type="submit" value="Create API Key" />
</form>
</fieldset>
</div>
