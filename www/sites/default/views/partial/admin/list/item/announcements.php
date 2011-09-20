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

<td><?php $item->id ?></td>
<td><?php $item->message ?></td>
<td><?php date('F d, Y @ H:ia', $item->timestamp) ?></td>
<td>
<form name="delete-announcement-entry" method="post" action="admin/announcements/delete">
    <input name="user_event_id" type="hidden" value="<?php $item->id ?>" />
    <input type ="submit" value="delete" />
</form>
</td>

