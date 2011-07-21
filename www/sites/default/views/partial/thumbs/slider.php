<?php // Display the three featured items ?>
<ul id="featured-slider">
<?php 
$data = Sourcemap_Search::Find(array('l'=>3));
if ($data):
    $results = $data->results;
    $i = 0;
    foreach($results as $item):
    ?>
        <li class="featured-item">
            <div class="featured-content">
                <a href="/map/view/<?php print $item->id; ?>">
                    <img class="featured-preview-map large" src="/map/static/<?php print $item->id; ?>.l.png" alt="" />
                </a>
            </div>
            <div id="featured-description-<?php $i ?>" class="featured-description">
                <h2 class="featured-title"><a href="/map/view/<?php print $item->id; ?>"><?= isset($item->attributes->title) ? $item->attributes->title : "Unknown Name" ?></a></h2>
                <? if(isset($item->teaser)) { ?><h3 class="featured-teaser"><?= $item->teaser; ?></h3><? } ?>
                <h4 class="featured-author">
                    By <a href="user/<?php $item->owner->name; ?>">
                        <?= isset($item->owner->name) ? $item->owner->name : "Unknown Author" ?></a>, 
                    <?php print date("F j, Y",$item->created);?>
                </h4>
            </div>
        </li>
        <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
</ul>
