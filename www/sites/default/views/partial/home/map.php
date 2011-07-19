<?php if(isset($supplychain) && $supplychain): ?>
        <div class="grid_3">
            <a href="map/view/<?= $supplychain->id ?>">
                <img class="user-map-preview" src="map/static/<?= $supplychain->id ?>.t.png" />
            </a>
        </div>
        <div class="grid_9 map-item">
            <a href="map/view/<?= $supplychain->id ?>">
                <h2 class="user-map-title"><?= isset($supplychain->attributes->title) ? HTML::chars($supplychain->attributes->title) : "An Unnamed Sourcemap" ?></h2>
            </a>
            
            <?php if(isset($supplychain->attributes->teaser) && $supplychain->attributes->teaser): ?>
                <h3 class="user-map-teaser"><?= HTML::chars($supplychain->attributes->teaser) ?></h3>
            <?php elseif(isset($supplychain->attributes->description) && $supplychain->attributes->description): ?>
                <h3 class="user-map-teaser"><?= HTML::chars($supplychain->attributes->description) ?></h3>
            <?php else: ?>
                <h3 class="user-map-teaser">(No description yet)</h3>
            <?php endif; ?>
            
            <?php if(isset($supplychain->owner->name) && $supplychain->owner->name): ?>
                <h4 class="user-map-details">
                    Created by <?= HTML::chars($supplychain->owner->name) ?>,
                    
                    <?php if($supplychain->modified > $supplychain->created): ?>
                        Last updated <?= date('F j, Y', $supplychain->modified) ?> | 
                    <?php endif; ?>
                    Created <?= date('F j, Y', $supplychain->created) ?>.
                </h4>
            <?php endif; ?>

            <?php if($supplychain->other_perms & Sourcemap::READ): ?>
            <?php else: ?>
                <h4 class="user-map-details bad-news">(private)</h4>
            <?php endif; ?>
        </div>
        <div class="grid_4 map-controls">
            <?php $public = ($supplychain->other_perms & Sourcemap::READ) > 0; ?>
            <a href="edit/visibility/<?= $supplychain->id ?>?publish=<?= $public ? "no" : "yes"?>"><?= $public ? "Unpublish" : "Publish" ?></a> | <a href="edit/<?= $supplychain->id ?>">Edit</a> | <a href="delete/<?= $supplychain->id ?>">Delete</a>
        </div>
        <div class="clear"></div>
<?php endif; ?>
