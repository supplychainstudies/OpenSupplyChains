<div id="edit-map" class="container">
    <h1>Edit your Sourcemap <span>"<?= $supplychain->attributes->name ?>"</span></h1>
    <div class="edit-map-form">
        <?= $form ?>
    </div>

    <div class="edit-map-details">
        <div class="edit-map-thumbnail">
            <div><h2>Map Thumbnail</h2></div>
            <hr />
            <div class="preview-map-item medium">
                <div class="preview-badge">
                <a href="view/<?php print $supplychain->id; ?>"><img class="preview-map small" src="static/<?= $supplychain->id ?>.m.png" alt="" /></a>
                </div>
                <h3 class="preview-title">
                    <a href="view/<?= $supplychain->id; ?>">
                        <?php if(isset($supplychain->attributes->title)): ?>
                            <?= HTML::chars($supplychain->attributes->title) ?>
                        <?php elseif(isset($supplychain->attributes->name)): ?>
                            <?= HTML::chars($supplychain->attributes->name) ?>
                        <?php else: ?>
                            An Unnamed Sourcemap
                        <?php endif; ?>
                    </a></h3>
                <h4 class="preview-author">By <a href="user/<?= $supplychain->owner->id; ?>"><?= (isset($item->owner->name)) ? HTML::chars($item->owner->name) : "Unknown Owner"?></a>,
                <?= date("F j, Y", $supplychain->created) ?></h4>
                <?php if(isset($supplychain->teaser)): ?><p class="preview-teaser"><?= HTML::chars($item->teaser) ?></p><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div> 

