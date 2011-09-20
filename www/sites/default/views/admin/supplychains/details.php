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

<form name="alias-info" method="post" action="">
<label for="site">site:</label>
<input type="text" name="site" class="input text" value=""/>

<label for="alias">alias:</label>
<input type="text" name="alias" class="input text" value=""/>
<input type="submit" value="Create Alias" />
</form><br />


<h3><?php isset($attributes['title']) ? HTML::chars($attributes['title']) : 'Untitled Sourcemap' ?></h3>
 <a target="map_view" href="view/<?php $id ?>"><img class="large" src="static/<?php $id ?>.l.png" /></a><br />
<?php if($flags & Sourcemap::FEATURED): ?><p class="featured good-news">** This map is a <a href="admin/featured">featured</a> map. **</p><?php endif; ?>
<p><?php if($owner): ?>
This map belongs to <a href="admin/users/<?php $owner_id ?>"><?php HTML::chars($owner)?></a>.
<? else: ?>
This map belongs to <span class="bad-news">*nobody*</span>.
<?php endif;?> It has <?php $stop_count ?> stop<?php $stop_count == 1 ? '' : 's' ?>
 and <?php $hop_count ?> hop<?php $hop_count == 1 ? '' : 's' ?>.

 <form name="chown" method="post" action="admin/supplychains/<?php $id ?>/chown">
<label for="chown">Change owner?</label><input type="checkbox" name="chown" /><br />
<label for="new_owner">New Owner:</label><br />
<input name="new_owner" type="text" value="<?php HTML::chars($owner) ?>" /><br />
<input type="submit" value="Change" />
 </form>

<h3>Attribute(s):</h3>
<?php if($attributes): ?>
    <dl class="attr-list">
    <?php foreach($attributes as $k => $v): ?>
        <dt><?php HTML::chars($k) ?></dt>
        <dd>
            <?php substr(HTML::chars($v), 0, 32) ?>
            <?php if(strlen(HTML::chars($v)) > 32): ?>...<?php endif; ?>
        </dd>
     <?php endforeach;?>
    </dl>
<?php endif;?>

<form name="permissions" method="post" action="admin/supplychains/<?php$id?>/change_perms">
 <strong>Reset Permissions:</strong> 
<select name="perms">
     <?php foreach($permissions_array as $i => $perm):?>
     <option value="<?php$perm ?>" <?php if($perm == $permissions):?>selected<?php endif; ?>><?php HTML::chars($perm); ?>
     </option>
     <?php endforeach;?>
</select>
<input type="submit" value="Save" /></form><br />

<?php if(!empty($owner_group)): ?>
 <strong>Owned by group:</strong> <?phpHTML::chars($owner_group)?>
<?php endif;?>


<form name="group-permissions" method="post" action="admin/supplychains/<?php$id?>/change_usergroup_perms">
 <strong>Group Permissions:</strong> 
<select name="groupperm">
      <?php foreach($group_permissions_array as $i => $group_perm): ?>
      <option value="<?php$group_perm ?>" <?php if($group_perm == $usergroup_perms):?>selected<?php endif; ?>><?php HTML::chars($group_perm); ?>
      </option>
     <?php endforeach;?>
</select>
<input type="submit" value="Save" /></form><br />


<?php if(!empty($alias)): ?>
<table>
<tr>
<th>Site</th>
<th>Alias</th>
</tr>
<?php foreach($alias as $alias_name): ?>
<tr>
<form name="delete-alias" method="post" action="admin/supplychains/<?php $alias_name['supplychain_id']?>/delete_alias">
<td><?php Html::chars($alias_name['site'])?><input type="hidden" name="site" value="<?php$alias_name['site'];?>"></td>
<td><?php Html::chars($alias_name['alias'])?><input type="hidden" name="alias" value="<?php$alias_name['alias'];?>"></td>
<td><input type ="submit" value="delete" /></form></td>
</tr>
<?php endforeach;?>
</table> 
<?php endif;?>






