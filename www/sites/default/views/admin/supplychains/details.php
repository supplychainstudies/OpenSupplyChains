<form name="alias-info" method="post" action="">
<label for="site">site:</label>
<input type="text" name="site" class="input text" value=""/>

<label for="alias">alias:</label>
<input type="text" name="alias" class="input text" value=""/>
<input type="submit" value="Create Alias" />
</form><br />

<?php if(!empty($stop_count)): ?>
 <strong>Number of stops:</strong> <?=$stop_count;?><br />
<?php endif;?>

<?php if(!empty($hop_count)): ?>
 <strong>Number of hops:</strong> <?=$hop_count;?><br />
<?php endif;?>

<?php if(!empty($attributes)): ?>
 <?php $last_attribute = end($attributes);?>
 <strong>Attribute(s):</strong>  
 <?php foreach($attributes as $attribute): ?>
   <?=$attribute['key'];?>
 <?php if($attribute != $last_attribute) {?>, <?}?>
 <?php endforeach;?>
<?php endif;?>

<?php if(!empty($alias)): ?>
<table>
<tr>
<th>Site</th>
<th>Alias</th>
</tr>
<?php foreach($alias as $alias_name): ?>
<tr>
<form name="delete-alias" method="post" action="admin/supplychains/<?= $alias_name['supplychain_id']?>/delete_alias">
<td><?=$alias_name['site'];?><input type="hidden" name="site" value="<?=$alias_name['site'];?>"></td>
<td><?=$alias_name['alias'];?><input type="hidden" name="alias" value="<?=$alias_name['alias'];?>"></td>
<td><input type ="submit" value="delete" /></form></td>
</tr>
<?php endforeach;?>
</table> 
<?php endif;?>






