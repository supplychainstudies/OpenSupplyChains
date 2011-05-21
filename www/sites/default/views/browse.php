<?php //Returns rows of three map-thumbs ?>
<?php foreach($supplychains as $id => $details): ?>


<?php print_r(Sourcemap_Search::Find(array('l'=>3))) ?>
<?php endforeach; ?>
<div style="float: left; clear: both; width: 100%">&nbsp;</div>
