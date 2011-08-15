<form name="alias-info" method="post" action="">
<label for="site">site:</label>
<input type="text" name="site" class="input text" value=""/>

<label for="alias">alias:</label>
<input type="text" name="alias" class="input text" value=""/>
<input type="submit" value="Create Alias" />
</form><br />


<h3><?= isset($attributes['title']) ? HTML::chars($attributes['title']) : 'Untitled Sourcemap' ?></h3>
 <a target="map_view" href="view/<?= $id ?>"><img class="large" src="static/<?= $id ?>.l.png" /></a><br />
<?php if($flags & Sourcemap::FEATURED): ?><p class="featured good-news">** This map is a <a href="admin/featured">featured</a> map. **</p><?php endif; ?>
<p><?php if($owner): ?>
This map belongs to <a href="admin/users/<?= $owner_id ?>"><?= HTML::chars($owner)?></a>.
<? else: ?>
This map belongs to <span class="bad-news">*nobody*</span>.
<?php endif;?> It has <?= $stop_count ?> stop<?= $stop_count == 1 ? '' : 's' ?>
 and <?= $hop_count ?> hop<?= $hop_count == 1 ? '' : 's' ?>.

<h3>Attribute(s):</h3>
<?php if($attributes): ?>
    <dl class="attr-list">
    <?php foreach($attributes as $k => $v): ?>
        <dt><?= HTML::chars($k) ?></dt>
        <dd>
            <?= substr(HTML::chars($v), 0, 32) ?>
            <?php if(strlen(HTML::chars($v)) > 32): ?>...<?php endif; ?>
        </dd>
     <?php endforeach;?>
    </dl>
<?php endif;?>

<form name="permissions" method="post" action="admin/supplychains/<?=$id?>/change_perms">
 <strong>Reset Permissions:</strong> 
<select name="perms">
     <?php foreach($permissions_array as $i => $perm):?>
     <option value="<?=$perm ?>" <?php if($perm == $permissions):?>selected<?php endif; ?>><?= HTML::chars($perm); ?>
     </option>
     <?php endforeach;?>
</select>
<input type="submit" value="Save" /></form><br />

<?php if(!empty($owner_group)): ?>
 <strong>Owned by group:</strong> <?=HTML::chars($owner_group)?>
<?php endif;?>


<form name="group-permissions" method="post" action="admin/supplychains/<?=$id?>/change_usergroup_perms">
 <strong>Group Permissions:</strong> 
<select name="groupperm">
      <?php foreach($group_permissions_array as $i => $group_perm): ?>
      <option value="<?=$group_perm ?>" <?php if($group_perm == $usergroup_perms):?>selected<?php endif; ?>><?= HTML::chars($group_perm); ?>
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
<form name="delete-alias" method="post" action="admin/supplychains/<?= $alias_name['supplychain_id']?>/delete_alias">
<td><?= Html::chars($alias_name['site'])?><input type="hidden" name="site" value="<?=$alias_name['site'];?>"></td>
<td><?= Html::chars($alias_name['alias'])?><input type="hidden" name="alias" value="<?=$alias_name['alias'];?>"></td>
<td><input type ="submit" value="delete" /></form></td>
</tr>
<?php endforeach;?>
</table> 
<?php endif;?>






