<?php if(isset($supplychains) && $supplychains): ?>
    <?php foreach($supplychains as $i => $item): ?>
        <div class="preview-map-item">
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>">
                <img class="preview-map small" src="static/<?php print $item->id; ?>.s.png" alt="" />
            </a>
            </div>
            <h3 class="preview-title">
                <a href="view/<?php print $item->id; ?>">
                <?= Text::limit_chars(isset($item->attributes->title) ? $item->attributes->title : "An Unnamed Sourcemap", 18) ?>
                </a>
            </h3>
            <h4>
                <div class="preview-author">
                    <a href="user/<?php print $item->owner->id; ?>">
                        <?= isset($item->owner->name) ? $item->owner->name : "Unknown Author" ?></a>, 
                    <?php print date("F j, Y",$item->created);?>
                </div>
            </h4>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    --
<?php endif; ?>
