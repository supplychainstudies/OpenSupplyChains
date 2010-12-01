<?php if(isset($breadcrumbs)): ?>
<div class="breadcrumbs">
<?php foreach($breadcrumbs as $i => $crumb): ?>
<span class="breadcrumb">&raquo;&nbsp;
<?php if($crumb->uri): ?><?= HTML::anchor($crumb->uri, $crumb->label) ?><?php else: ?><?= HTML::chars($crumb->label) ?><?php endif; ?>
</span>
<?php endforeach; ?>
</div>
<?php endif; ?>
