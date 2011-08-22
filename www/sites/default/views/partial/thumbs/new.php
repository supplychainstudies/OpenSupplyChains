<?php // Display the three most recent maps

$data=Sourcemap_Search::Find(array('l'=>4));
if ($data):
    $results = $data->results;
    $i = 0;
    foreach($results as $item):
        ?>
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>">
                <img class="preview-map small" src="/map/static/<?php print $item->id; ?>.t.png" alt="" />
            </a>
            </div>
            <h3 class="preview-title">
                <a href="view/<?php print $item->id; ?>">
                <?= Text::limit_chars(HTML::chars($item->attributes->title), 16) ?>
                </a>
            </h4>
            <h4>
                <div class="preview-author">
                    <a href="user/<?php $item->owner->id; ?>">
                        <?= isset($item->owner->name) ? Text::limit_chars(HTML::chars($item->owner->name), 17) : "Unknown Author" ?></a>, 
                    <?php print date("M j, Y",$item->created);?>
                </div>
            </h4>
        </div>
    <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
