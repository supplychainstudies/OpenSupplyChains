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
                </a>
                <div class="description">
                    <h2><?= print isset($item->attributes->title) ? $item->attributes->title : "Unknown Name" ?></h2>
                    <p>
                    created by <a href="user/<?php print $item->owner->id; ?>"><?= print isset($item->owner->name) ? $item->owner->name : "Unknown Author" ?></a> 
                    <br />
                    on <?php print date("F j, Y",$item->created);?>
                    <br />
                </div>
            </div>
        </li>
        <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
</ul>
