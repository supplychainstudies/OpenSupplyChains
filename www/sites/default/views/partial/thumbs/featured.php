<div id="popular-maps">
<?php if(isset($supplychains) && $supplychains): ?>
    <?php $c = 0; ?>
    <?php foreach($supplychains as $i => $item):?>
        <div class="preview-map-item medium">
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>"><img class="preview-map small" src="static/<?= $item->id ?>.m.png" alt="" /></a>
            </div>
            <h3 class="preview-title">
                <a href="view/<?= $item->id; ?>">
                    <?php if(isset($item->attributes->title)): ?>
                        <?= HTML::chars($item->attributes->title) ?>
                    <?php elseif(isset($item->attributes->name)): ?>
                        <?= HTML::chars($item->attributes->name) ?>
                    <?php else: ?>
                        An Unnamed Sourcemap
                    <?php endif; ?>
                </a></h3>
            <h4 class="preview-author">By <a href="user/<?= $item->owner->id; ?>"><?= (isset($item->owner->name)) ? HTML::chars($item->owner->name) : "Unknown Owner"?></a>,
            <?= date("F j, Y", $item->created) ?></h4>
            <?php if(isset($item->teaser)): ?><p class="preview-teaser"><?= HTML::chars($item->teaser) ?></p><?php endif; ?>
        </div>
        <?php //limit to the first two ?>
        <?php if (++$c == 2) break; ?>
    <?php endforeach; ?>
<?php else: ?>
    <div class="preview-map-item medium">
        --
    </div>
<?php endif; ?>
</div>
<div class="clear"></div>
