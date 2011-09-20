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

<div class="user-details<?php $user->has_flag(Sourcemap::VERIFIED) ? ' verified' : '' ?>">
<?php HTML::chars($user->username)?> last signed in on <?php $last_login?><br />
<fieldset><legend>Flags</legend>
<form name="user-verification" method="post" action="admin/users/<?php $user->id ?>/flags">
<label for="verfified">Verified</label>
<input type="checkbox" name="verified" <?php $user->has_flag(Sourcemap::VERIFIED) ? 'checked' : '' ?> />
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
     <form name="user-roles" method="post" action="admin/users/<?php $user->id?>/delete_role">
        <?php HTML::chars($roles[$i]['name'])?> 
        <input type="hidden" name="role" value="<?php HTML::chars($roles[$i]['name']) ?>">
        <input type="submit" value="delete"/></form> 
    <? endforeach; ?><br />
<?php endif;?>


       
<?php if(count($roles) != count($all_roles)): ?>
    <strong>Change or add the user role:</strong>
    <form name="change-role" method="post" action="admin/users/<?php $user->id?>/add_role">
    <select name="addrole">
    <?php foreach ($all_roles as $role): ?>
        <?php $has_role = false; ?>
        <?php foreach($roles as $roler): ?>
             <?php if($roler['name'] == $role->name) { $has_role = true; break; } ?>
        <?php endforeach; ?>
        <?php if(!$has_role): ?>
             <option value="<?php HTML::chars($role->name); ?>"><?php HTML::chars($role->name) ?></option>
         <?php endif; ?>
    <?php endforeach; ?>
    </select>
    <input type="submit" value="add role"/></form>
<?php endif; ?><br />

<?php if(!empty($members)): ?>
    <strong>Group Membership:</strong>
    <?php foreach ($members as $i => $memberof): ?>
        <a href="admin/groups/<?php$member['id'];?>">
            <?phpHTML::chars($memberof['name']) ?>
        </a>
        <?php $i < (count($memberof)-1) ? ', ' : '' ?>
     <?php endforeach; ?><br />
 <?php endif; ?>

<?php if($owners): ?>
  <strong>Group Ownership:</strong>
    <?php foreach ($owners as $i => $owned): ?>
        <a href="admin/groups/<?php$owned['id'];?>">
            <?phpHTML::chars($owned['name']) ?>
        </a><?php $i < (count($owned)-1) ? ', ' : '' ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if($apikeys): ?>
    <strong>API Keys</strong>
    <?php foreach($apikeys as $i => $apikey): ?>
        <div><?php $i ?></div>
    <?php endforeach; ?>
<?php endif; ?>
<fieldset>
<legend>Add an API Key for <?php HTML::chars($user->username) ?></legend>
<form method="post" action="admin/apikeys/add">
<input type="hidden" name="user_id" value="<?php $user->id ?>" />
<input type="submit" value="Create API Key" />
</form>
</fieldset>
</div>
