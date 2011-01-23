<h1>Welcome!</h1>
<?php foreach($supplychains->as_array('id', array('created', 'modified')) as $id => $details): ?>
<div class="map-thumb" style="width: 20%; float: left;">
<a href="map/view/<?= $id ?>">This is a map</a><br />
<img style="width: 80%;" src="map/static/<?= $id ?>" /><br />
</div>
<?php endforeach; ?>
<div style="float: left; clear: both; width: 100%">&nbsp;</div>
