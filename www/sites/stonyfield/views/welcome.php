<ul class="map-nav<?= isset($scalias) ? " $scalias" : '' ?>">
    <li class="yogurt"><a href="yogurt">Yogurt</a></li>
    <li class="sweeteners"><a href="sweeteners">Fruit and Sweeteners</a></li>
    <li class="dairy"><a href="dairy">Dairy</a></li>
    <li class="other"><a href="other">Other Ingredients</a></li>
</ul>
<div id="map">
<iframe height="500" width="100%" scrolling="no" frameborder="0" 
    src="http://alpha.sourcemap.org/map/embed/<?= $scid ?>?tour=yes&amp;tour_start_delay=0&amp;tour_interval=3"></iframe>
</div>
