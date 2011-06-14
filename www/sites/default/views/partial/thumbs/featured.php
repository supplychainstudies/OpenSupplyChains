<div class="container_12">
<?php $data = Sourcemap_Search::Find(array('l'=>3)); ?>
<?php if ($data): ?>
    <?php foreach($data->results as $i => $item): ?>
        <div class="map-item grid_4">
        <a href="/map/view/<?php print $item->id; ?>">
        <img class="small" src="/map/static/<?php print $item->id; ?>.s.png" alt="" />
        </a>
        <br />
        <h3><?= isset($item->attributes->title) ? $item->attributes->title : "Unknown Name" ?></h3>
        <h4 class="author">By <?= isset($item->owner->name) ? $item->owner->name : "Unknown Owner" ?>
        <br/>
        Published <?php print date("F j, Y",$item->created);?></h4>
        <br />
        </a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <h2>No maps have been created yet.</h2>
<?php endif; ?>
</div>
<div class="clear"></div>
