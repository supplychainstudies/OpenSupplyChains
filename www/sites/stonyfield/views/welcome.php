<ul class="map-nav<?= isset($scalias) ? " $scalias" : '' ?>">
    <li class="yogurt"><a href="yogurt">Home</a></li>
    <li class="dairy"><a href="milk">Milk</a>
        <!--ul>
            <li><a href="northeast">Northeast</a></li>
            <li><a href="southeast">Southeast</a></li>
            <li><a href="greatlakes">Great Lakes</a></li>
            <li><a href="midwest">Midwest</a></li>
            <li><a href="southcentral">South Central</a></li>
            <li><a href="mountain">Mountain</a></li>
            <li><a href="northwest">Northwest</a></li>
            <li><a href="california">California</a></li>
        </ul-->
    </li>
    <li class="sweeteners"><a href="sweeteners">Fruit and Sweeteners</a></li>
    <li class="other"><a href="other">Other Ingredients</a></li>
</ul>
<div id="map">
<iframe height="500" width="100%" scrolling="no" frameborder="0" 
    src="http://alpha.sourcemap.org/map/embed/<?= $scid ?>?banner=no"></iframe>
</div>
