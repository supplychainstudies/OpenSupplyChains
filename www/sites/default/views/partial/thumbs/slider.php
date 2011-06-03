<?php // Display the three featured items ?>
<ul id="slider">
<?php 
$data = Sourcemap_Search::Find(array('l'=>3));
if ($data):
    $results = $data->results;
    $i = 0;
    foreach($results as $item):
    ?>
        <li>
            <div class="map">
            <a href="/map/view/<?php print $item->id; ?>">
            <img class="medium" src="/map/static/<?php print $item->id; ?>.m.png" alt="" />
            <div class="description">    
            <h2><?= print isset($item->attributes->title) ? $item->attributes->title : "A Sourcemap" ?></h2>
            <p>
            created by <a href="user/<?php print $item->owner->id; ?>"><?php print $item->owner->name; ?></a> 
            <br />
            on <?php print date("F j, Y",$item->created);?></h5>
            <br />
            </div>
        </li>
        <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
</ul>
