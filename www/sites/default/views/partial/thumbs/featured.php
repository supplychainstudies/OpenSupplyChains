<?php // Display the three featured items ?>
<div class="container_16">
<?php 
$data=Sourcemap_Search::Find(array('l'=>3));
if ($data):
    $results = $data->results;
    $i = 0;
    foreach($results as $item):
    ?> 
        <div class="map-item grid_5">
        <a href="/map/view/<?php print $item->id; ?>">
        <img class="small" src="/map/static/<?php print $item->id; ?>.s.png" alt="" />
        <br />
        <h4><?php print $item->attributes->title;?></h4>
        <h5>Created by <?php print $item->owner->name; ?> on <?php print date("F j, Y",$item->created);?></h5>
        <br />
        </a>
        </div>
        <?php if($i<2){?>
            <div class="grid_1">&nbsp;</div>
        <?php } ?>
        <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
</div>
<div class="clear"></div>
