<?php if(isset($supplychain) && $supplychain): ?>
        <div class="map-item small">
            <a href="view/<?= $supplychain->id ?>">
                <img class="user-map-preview small" src="map/static/<?= $supplychain->id ?>.s.png" />
            </a>
        </div>
        <div class="map-description">
            <a href="view/<?= $supplychain->id ?>">
                <h2 class="map-title"><?= HTML::chars(isset($supplychain->attributes->title) ? $supplychain->attributes->title : "An Unnamed Sourcemap") ?></h2>
            </a>
            <h4 class="map-details">
            	<?= date('F j, Y', $supplychain->created) ?>
            </h4>
            <div class="clear"></div>
            
            <?php if(isset($supplychain->attributes->description) && $supplychain->attributes->description): ?>
                <span class="map-teaser"><?= HTML::chars($supplychain->attributes->description) ?></span>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
<?php endif; ?>
