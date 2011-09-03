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

<td><a href="admin/supplychains/<?= $item->id ?>"><?= $item->id ?></a></td>
<td><?= HTML::chars($item->created) ?></td>
<td><?= HTML::chars($item->owner) ?></td>
<td><?= HTML::chars(isset($item->attributes['title']) ? $item->attributes['title'] : '') ?></td>
<td><a href="static/<?= $item->id ?>.l.png" target="map_preview"><img class="medium" src="static/<?= $item->id ?>.m.png" /></a></td>

<form name="refresh-supplychain" method="post" action="admin/supplychains/<?= $item->id?>/refresh_supplychain">
<td><input type ="submit" value="refresh" /></form></td>