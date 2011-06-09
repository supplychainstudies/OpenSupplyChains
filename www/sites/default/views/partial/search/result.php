<?php if(isset($result) && $result): ?>
        <div class="grid_5">
            <a href="map/view/<?= $result->id ?>">
                <img src="map/static/<?= $result->id ?>.s.png" />
            </a>
        </div>
        <div class="grid_11 details">
            <a href="map/view/<?= $result->id ?>">
                <h4 class="title"><?= isset($result->attributes->title) ? HTML::chars($result->attributes->title) : "An Unnamed Sourcemap" ?></h4>
            </a>
            <p class="dates">Created <span class="date created"><?= date('r', $result->created) ?></span>
                <?php if($result->modified > $result->created): ?>
                    <br />Updated <span class="date modified"><?= date('r', $result->modified) ?></span>
                <?php endif; ?>
            </p>
            <?php if(isset($result->attributes->teaser) && $result->attributes->teaser): ?>
            <p class="teaser"><?= HTML::chars($result->attributes->teaser) ?></p>
            <?php elseif(isset($result->attributes->description) && $result->attributes->description): ?>
            <p class="teaser"><?= HTML::chars($result->attributes->description) ?></p>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
<?php endif; ?>
