<?php if(isset($list) && $list): ?>
<table class="google-spreadsheets-list">
<thead>
<tr><th>Title</th><th>Updated</th><th>Author</th></tr>
</thead>
<tbody>
<?php foreach($list as $i => $sheet): ?>
<tr class="<?= $i % 2 ? 'odd' : 'even' ?>"><td><a href="tools/google/worksheets?k=<?= HTML::chars($sheet['key']) ?>"><?= HTML::chars($sheet['title']) ?></a></td><td><?= HTML::chars($sheet['updated']) ?></td><td><?= HTML::chars($sheet['author_name']) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<h3 class="error">You have no Google Spreadsheets to import.</h3>
<?php endif; ?>
