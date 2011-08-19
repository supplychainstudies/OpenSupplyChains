<?php if(isset($supplychain) && $supplychain): ?>
        <div class="map-item small">
            <a href="map/view/<?= $supplychain->id ?>">
                <img class="user-map-preview" src="map/static/<?= $supplychain->id ?>.s.png" />
            </a>
        </div>
        <div class="map-description">
            <a href="map/view/<?= $supplychain->id ?>">
                <h2 class="map-title"><?= isset($supplychain->attributes->title) ? HTML::chars($supplychain->attributes->title) : "An Unnamed Sourcemap" ?></h2>
            </a>
            <h4 class="map-details">
            	<?= HTML::chars($supplychain->owner->name) ?>, <?= date('F j, Y', $supplychain->created) ?>
            </h4>
            <div class="clear"></div>
            
            <?php if(isset($supplychain->attributes->description) && $supplychain->attributes->description): ?>
                <span class="map-teaser"><?= HTML::chars($supplychain->attributes->description) ?></span>
            <?php endif; ?>
        </div>

        <div class="clear"></div>
<?php endif; ?>
