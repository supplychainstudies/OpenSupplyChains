<div class="container_16">
<?php $data = Sourcemap_Search::Find(array('l'=>3)); ?>
<?php if ($data): ?>
    <?php foreach($data->results as $i => $item): ?>
        <div class="map-item grid_5">
        <a href="/map/view/<?php print $item->id; ?>">
        <img class="small" src="/map/static/<?php print $item->id; ?>.s.png" alt="" />
        </a>
        <br />
        <h4><?= isset($item->attributes->title) ? $item->attributes->title : "Unknown Name" ?></h4>
        <h5>By <?= isset($item->owner->name) ? $item->owner->name : "Unknown Owner" ?>
        <br/>
        Published <?php print date("F j, Y",$item->created);?></h5>
        <br />
        </a>
        </div>
        <?php if($i<2): ?>
            <div class="grid_1">&nbsp;</div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <h2>No maps have been created yet.</h2>
<?php endif; ?>
</div>
<div class="clear"></div>
