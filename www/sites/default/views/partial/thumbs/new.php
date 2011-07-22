<?php // Display the three most recent maps

$data=Sourcemap_Search::Find(array('l'=>3));
if ($data):
    $results = $data->results;
    $i = 0;
    foreach($results as $item):
        ?>
        <div class="preview-map-item">
            <div class="preview-badge">
            <a href="/map/view/<?php print $item->id; ?>">
                <img class="preview-map small" src="/map/static/<?php print $item->id; ?>.t.png" alt="" />
            </a>
            </div>
            <h3 class="preview-title">
                <a href="/map/view/<?php print $item->id; ?>">
                <?= isset($item->attributes->title) ? $item->attributes->title : "A Sourcemap" ?>
                </a>
            </h4>
            <h4>
                <div class="preview-author">
                    By <a href="user/<?php $item->owner->id; ?>">
                        <?= isset($item->owner->name) ? $item->owner->name : "Unknown Author" ?></a>, 
                    <?php print date("F j, Y",$item->created);?>
                </div>
            </h4>
        </div>
    <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
