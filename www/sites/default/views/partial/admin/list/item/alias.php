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

<td><?= $item->id ?></td>
<td><?= Html::chars($item->site) ?></a></td>
<td><?= Html::chars($item->alias) ?></td>
<td><a href="admin/supplychains/<?= $item->supplychain_id ?>"><?= $item->supplychain_id?></td>
<form name="delete-alias" method="post" action="admin/aliases/<?= $item->id?>/delete_supplychain_alias">
<td><input type ="submit" value="delete" /></form></td>

