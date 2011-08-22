<div id="popular-maps">
<?php if(isset($supplychains) && $supplychains): ?>
    <?php foreach($supplychains as $i => $item):?>
        <div class="preview-map-item medium">
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>"><img class="preview-map medium" src="static/<?= $item->id ?>.m.png" alt="" /></a>
            </div>
            <h3 class="preview-title">
                <a href="view/<?= $item->id; ?>">
                    <?= Text::limit_chars(HTML::chars(isset($item->attributes->title) ? $item->attributes->title : "An Unnamed Sourcemap"), 23) ?>                   
                </a></h3>
            <h4 class="preview-author"><a href="user/<?= $item->owner->id; ?>"><?=  Text::limit_chars(HTML::chars($item->owner->name), 17) ?></a>,
            <?= date("M j, Y", $item->created) ?></h4>
        </div>
    <?php endforeach; ?>
<?php else: ?>
<?php endif; ?>
</div>
