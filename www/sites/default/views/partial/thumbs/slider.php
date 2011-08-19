<?php if(isset($supplychains) && $supplychains): ?>
<ul id="featured-slider">
<?php foreach($supplychains as $i => $item): ?>
    <li class="featured-item">
        <div class="featured-content">
            <a href="view/<?php print $item->id; ?>">
                <img class="featured-preview-map large" src="/map/static/<?php print $item->id; ?>.l.png" alt="" />
            </a>
        </div>
        <div id="featured-description-<?= $i ?>" class="featured-description">
            <h2 class="featured-title-leader">The Sourcemap of:</h2>
            <h1 class="featured-title"><a href="/view/<?php print $item->id; ?>"><?= Text::limit_chars(HTML::chars($item->attributes->title),35) ?></a></h1>
            <? if(isset($item->attributes->description)) { ?><h3 class="featured-teaser"><?= Text::limit_chars(HTML::chars($item->attributes->description),70); ?></h3><? } ?>
            <h4 class="featured-info">
                 <a href="user/<?php $item->owner->id; ?>">
                    <?= HTML::chars($item->owner->name) ?></a>,   
                <?php print date("F j, Y",$item->created);?>
            </h4>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<?php endif; ?>
