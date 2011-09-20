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

<td><?php $item->apikey ?></td>
<td><?php $item->apisecret ?></td>
<td><?php date('r', $item->created) ?></td>
<td><?php $item->id ?></td>
<td><?php $item->requests ?></td>
<td><a href="admin/users/<?php $item->user_id ?>"><?php Html::chars($item->owner->username) ?></a></td>
