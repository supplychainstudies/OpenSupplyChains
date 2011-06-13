<a href="map/view/<?= $supplychain->id ?>">
<img src="map/static/<?= $supplychain->id ?>.s.png" />
<h3><?= isset($supplychain->title) ? HTML::chars($supplychain->title) : "A Sourcemap" ?></h3>
<h4>Updated: <?= date('g:i a F d, Y', $supplychain->modified) ?></h4>
</a>
