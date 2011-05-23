<?php // Display the three most recent maps

$data=Sourcemap_Search::Find(array('l'=>3));
$results = $data->results;
$i = 0;
foreach($results as $item):
?>
    <div class="map-item">
    <img src="/map/static/<?php print $item->id; ?>.t.png" alt="" />
    <br />
    <h4><?php foreach ($item->attributes as $attribute){ print $attribute; } ?></h4>
    <br />
    </div>
    <?php $i++;
endforeach;
?>
