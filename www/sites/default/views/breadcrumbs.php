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

<?php if(isset($breadcrumbs)): ?>
<div class="breadcrumbs">
<?php foreach($breadcrumbs as $i => $crumb): ?>
<span class="breadcrumb">
<?php if($crumb->uri): ?><?php HTML::anchor($crumb->uri, $crumb->label) ?><?php else: ?><?php HTML::chars($crumb->label) ?><?php endif; ?> &raquo;&nbsp;
</span>
<?php endforeach; ?>
</div>
<?php endif; ?>
