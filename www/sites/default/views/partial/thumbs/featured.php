<div id="popular-maps" class="container_12">
<?php $data = Sourcemap_Search::Find(array('l'=>3)); ?>
<?php if ($data): ?>
    <?php foreach($data->results as $i => $item): ?>
        <div class="preview-map-item medium grid_4">
            <div class="preview-badge">
            <a href="/map/view/<?php print $item->id; ?>"><img class="preview-map small" src="/map/static/<?php print $item->id; ?>.s.png" alt="" /></a>
            </div>
            <h3 class="preview-title"><a href="/map/view/<?php print $item->id; ?>"><?= isset($item->attributes->title) ? $item->attributes->title : "Unknown Name" ?></a></h3>
            <h4 class="preview-author">By <a href="user/<?php $item->owner->id; ?>"><?= isset($item->owner->name) ? $item->owner->name : "Unknown Owner" ?></a>,
            <?php print date("F j, Y",$item->created);?></h4>
            <? if(isset($item->teaser)) { ?><p class="preview-teaser"><?= $item->teaser; ?></p><? } ?>
            
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="preview-map-item medium grid_4">    
        <h3>No maps have been created yet.</h3>
    </div>
<?php endif; ?>
</div>
<div class="clear"></div>
