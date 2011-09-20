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

<?php if(isset($list) && $list): ?>
<table class="google-spreadsheets-list">
<thead>
<tr><th>Title</th><th>Updated</th><th>Author</th></tr>
</thead>
<tbody>
<?php foreach($list as $i => $sheet): ?>
<tr class="<?php $i % 2 ? 'odd' : 'even' ?>"><td><a href="tools/import/google/worksheets?k=<?php HTML::chars($sheet['key']) ?>"><?php HTML::chars($sheet['title']) ?></a></td><td><?php HTML::chars($sheet['updated']) ?></td><td><?php HTML::chars($sheet['author_name']) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<h3 class="error">You have no Google Spreadsheets to import.</h3>
<?php endif; ?>
