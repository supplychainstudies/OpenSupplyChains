<?php if(isset($result) && $result): ?>
        <div class="grid_3">
            <a href="map/view/<?= $result->id ?>">
                <img class="user-map-preview" src="map/static/<?= $result->id ?>.t.png" />
            </a>
        </div>
        <div class="grid_9 map-item">
            <a href="map/view/<?= $result->id ?>">
                <h2 class="user-map-title"><?= isset($result->attributes->title) ? HTML::chars($result->attributes->title) : "An Unnamed Sourcemap" ?></h2>
            </a>
            
            <?php if(isset($result->attributes->teaser) && $result->attributes->teaser): ?>
                <h3 class="user-map-teaser"><?= HTML::chars($result->attributes->teaser) ?></h3>
            <?php elseif(isset($result->attributes->description) && $result->attributes->description): ?>
                <h3 class="user-map-teaser"><?= HTML::chars($result->attributes->description) ?></h3>
            <?php else: ?>
                <h3 class="user-map-teaser">(No description yet)</h3>
            <?php endif; ?>
            
            <?php if(isset($result->owner->name) && $result->owner->name): ?>
                <h4 class="user-map-details">
                     <?= HTML::chars($result->owner->name) ?>
                    
                    <?php if($result->modified > $result->created): ?>
                        : <?= date('F j, Y', $result->modified) ?> 
                    <?php endif; ?>
                    : <?= date('F j, Y', $result->created) ?>
                </h4>
            <?php endif; ?>

            <?php if($result->other_perms & Sourcemap::READ): ?>
            <?php else: ?>
                <h4 class="user-map-details bad-news">(private)</h4>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
<?php endif; ?>
