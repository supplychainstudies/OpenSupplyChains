<form name="alias-info" method="post" action="">
<label for="site">site:</label>
<input type="text" name="site" class="input text" value=""/>

<label for="alias">alias:</label>
<input type="text" name="alias" class="input text" value=""/>
<input type="submit" value="Create Alias" />
</form><br />


<strong>Number of stops:</strong> <?=$stop_count;?><br />
<strong>Number of hops:</strong> <?=$hop_count;?><br />
<?php if(!empty($attribute_key)): ?>
<strong>Attribute:</strong> <?=$attribute_key;?> 
 <?php endif;?><br />
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






