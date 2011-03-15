<ul class="map-nav<?= isset($scalias) ? " $scalias" : '' ?>">
    <li class="yogurt"><a href="yogurt">Yogurt</a></li>
    <li class="sweeteners"><a href="sweeteners">Fruit and Sweeteners</a></li>
    <li class="dairy"><a href="#">Dairy</a>
        <ul>
            <li><a href="northeast">Northeast</a></li>
            <li><a href="southeast">Southeast</a></li>
            <li><a href="greatlakes">Great Lakes</a></li>
            <li><a href="midwest">Midwest</a></li>
            <li><a href="southcentral">South Central</a></li>
            <li><a href="mountain">Mountain</a></li>
            <li><a href="northwest">Northwest</a></li>
            <li><a href="california">California</a></li>
        </ul>
    </li>
    <li class="other"><a href="other">Other Ingredients</a></li>
</ul>
<div id="map">
<iframe height="500" width="100%" scrolling="no" frameborder="0" 
    src="http://sourcemap.local/map/embed/<?= $scid ?>?tour=yes&amp;tour_start_delay=0&amp;tour_interval=3"></iframe>
</div>
