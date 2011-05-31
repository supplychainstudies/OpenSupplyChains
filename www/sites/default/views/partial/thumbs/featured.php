<?php // Display the three featured items ?>

<?php 
$data=Sourcemap_Search::Find(array('l'=>3));
$results = $data->results;
$i = 0;
foreach($results as $item):
?>
    <div class="map-item fourcol<?php if($i == 2): ?> last<? endif; ?>">
    <a href="/map/view/<?php print $item->id; ?>">
    <img class="small" src="/map/static/<?php print $item->id; ?>.s.png" alt="" />
    <br />
    <h4><?php foreach ($item->attributes as $attribute){ print $attribute; } ?></h4>
    <h5>Created by <?php print $item->owner->name; ?> on <?php print date("F j, Y",$item->created);?></h5>
    <br />
    </a>
    </div>
    <?php $i++;
endforeach;
?>
