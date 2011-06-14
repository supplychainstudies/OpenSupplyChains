<?php // Display the three most recent maps

$data=Sourcemap_Search::Find(array('l'=>3));
if ($data):
    $results = $data->results;
    $i = 0;
    foreach($results as $item):
        ?>
        <div class="map-item grid-3">
            <a href="/map/view/<?php print $item->id; ?>">
            <img class="thumb" src="/map/static/<?php print $item->id; ?>.t.png" alt="" />
            </a>
            <h4>
                <a href="/map/view/<?php print $item->id; ?>">
                <?= isset($item->attributes->title) ? $item->attributes->title : "A Sourcemap" ?>
                </a>
            </h4>
        </div>
    <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
