<?php if(isset($supplychain) && $supplychain): ?>
        <div class="map-item small">
            <a href="view/<?= $supplychain->id ?>">
                <img class="user-map-preview small" src="map/static/<?= $supplychain->id ?>.s.png" />
            </a>
        </div>
        <div class="map-description">
            <a href="view/<?= $supplychain->id ?>">
                <h2 class="map-title"><?= isset($supplychain->attributes->title) ? HTML::chars($supplychain->attributes->title) : "An Unnamed Sourcemap" ?></h2>
            </a>
            <h4 class="map-details">
            	<?= date('F j, Y', $supplychain->created) ?>
            </h4>
            <div class="clear"></div>
            
            <?php if(isset($supplychain->attributes->description) && $supplychain->attributes->description): ?>
                <span class="map-teaser"><?= HTML::chars($supplychain->attributes->description) ?></span>
            <?php endif; ?>
        </div>
        <div class="map-controls">
            <?php $public = ($supplychain->other_perms & Sourcemap::READ) > 0; ?>
            <div class="map-controls-publish">
                <input id="map-publish-checkbox" type="checkbox" name="publish" onclick="window.location='edit/visibility/<?= $supplychain->id ?>?publish=<?= $public ? "no" : "yes"?>'; return true;"<?= $public ? "checked" : "" ?>/>
                <a id="map-publish-link">Public</a>
            </div>
            <div class="map-controls-edit">
                <a href="edit/<?= $supplychain->id ?>">Edit</a>
            </div>
            <div class="map-controls-delete">
                <a class="red" href="delete/<?= $supplychain->id ?>">Delete</a>
            </div>
        </div>
        <div class="clear"></div>
<?php endif; ?>
