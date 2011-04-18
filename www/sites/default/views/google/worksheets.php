<?php if(isset($worksheets, $spreadsheet_key) && $worksheets): ?>
<table class="google-spreadsheets-worksheets">
<thead>
<tr><th>Title</th><th>Updated</th></tr>
</thead>
<tbody>
<?php foreach($worksheets as $i => $sheet): ?>
<tr class="<?= $i % 2 ? 'odd' : 'even' ?>"><td><a href="tools/google/import?k=<?= HTML::chars($spreadsheet_key) ?>&amp;wsid=<?= HTML::chars($sheet['id']) ?>"><?= HTML::chars($sheet['title']) ?></a></td><td><?= HTML::chars($sheet['updated']) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<h3 class="error">No worksheets found.</h3>
<?php endif; ?>
