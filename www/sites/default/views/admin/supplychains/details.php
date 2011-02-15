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

<table>
<tr>
<th>Site</th>
<th>Alias</th>
</tr>
<?php foreach($alias as $alias_name): ?>
<tr>
<td><?=$alias_name['site'];?></td>
<td><?=$alias_name['alias'];?></td>
</tr>
<?php endforeach;?>
</table> 






