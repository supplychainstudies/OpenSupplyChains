<div id="edit-map" class="container">
    <h1><?= $supplychain->attributes->title ?></span></h1>
    <div class="edit-map-form">
        <?= $form ?>
    </div>

    <div class="edit-map-details">
        <div class="edit-map-thumbnail">
            <div class="preview-map-item medium">
                <div class="preview-badge">
                <a href="view/<?php print $supplychain->id; ?>"><img class="preview-map medium" src="static/<?= $supplychain->id ?>.m.png" alt="" /></a>
                </div>
                <h3 class="preview-title">
                    <a href="view/<?= $supplychain->id; ?>">
                        <?= HTML::chars($supplychain->attributes->title) ?>                    
                    </a></h3>
                <h4 class="preview-author"><a href="user/<?= $supplychain->owner->id; ?>">HTML::chars($supplychain->owner->name) ?></a>,
                <?= date("F j, Y", $supplychain->created) ?></h4>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div> 

