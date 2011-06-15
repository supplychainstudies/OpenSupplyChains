<?php if(isset($result) && $result): ?>
        <div class="grid_5">
            <a href="map/view/<?= $result->id ?>">
                <img src="map/static/<?= $result->id ?>.s.png" />
            </a>
        </div>
        <div class="grid_7">
            <a href="map/view/<?= $result->id ?>">
                <h2 class="title"><?= isset($result->attributes->title) ? HTML::chars($result->attributes->title) : "An Unnamed Sourcemap" ?></h2>
            </a>
            
            <?php if(isset($result->attributes->teaser) && $result->attributes->teaser): ?>
                <p class="teaser"><?= HTML::chars($result->attributes->teaser) ?></p>
            <?php elseif(isset($result->attributes->description) && $result->attributes->description): ?>
                <p class="teaser"><?= HTML::chars($result->attributes->description) ?></p>
            <?php else: ?>
                <p class="teaser">(No description yet)</p>
            </p>
            <?php endif; ?>
            
            <?php if(isset($result->owner->name) && $result->owner->name): ?>
                <h4 class="author">
                    By <?= HTML::chars($result->owner->name) ?>,
                    <?= date('F j, Y', $result->created) ?>
                    <?php if($result->modified > $result->created): ?>
                        <br />Last updated <?= date('F j, Y', $result->modified) ?>
                    <?php endif; ?>
                </h4>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
<?php endif; ?>
