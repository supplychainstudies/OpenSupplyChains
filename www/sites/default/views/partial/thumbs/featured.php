<div id="popular-maps">
<?php if(isset($supplychains) && $supplychains): ?>
    <?php foreach($supplychains as $i => $item):?>
        <div class="preview-map-item medium">
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>"><img class="preview-map medium" src="static/<?= $item->id ?>.m.png" alt="" /></a>
            </div>
            <h3 class="preview-title">
                <a href="view/<?= $item->id; ?>">
                    <?php if(isset($item->attributes->title)): ?>
                        <?= Text::limit_chars(HTML::chars($item->attributes->title), 25) ?>
                    <?php elseif(isset($item->attributes->name)): ?>
                        <?= Text::limit_chars(HTML::chars($item->attributes->name), 25) ?>
                    <?php else: ?>
	                    <?= Text::limit_chars(HTML::chars("An Unnamed Sourcemap"), 25) ?>                        
                    <?php endif; ?>
                </a></h3>
            <h4 class="preview-author"><a href="user/<?= $item->owner->id; ?>"><?= (isset($item->owner->name)) ? HTML::chars($item->owner->name) : "Unknown Owner"?></a>,
            <?= date("F j, Y", $item->created) ?></h4>
        </div>
    <?php endforeach; ?>
<?php else: ?>
<?php endif; ?>
</div>
