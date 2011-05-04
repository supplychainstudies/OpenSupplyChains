<ul class="map-nav dairy">
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
<div style="background-color: #fff; width: 957px;">
    <div style="padding: 2em; text-align: center; margin: 0 auto; width: 624px">
        <div class="instructions">Select a region from the map below:</div>
        <img id="milk-map" src="sites/stonyfield/assets/images/milk-map.png" usemap="#usa" />
        <map name="usa"> 
            <div style="text-align:center; width:624px; margin-left:auto; margin-right:auto;"> 
            <area shape="poly" coords="11,101,87,121,94,93,94,87,93,86,104,68,101,61,112,18,56,1,34,5,10,86,8,98" 
            href="northwest" alt="Northwest" title="Northwest"   /> 
            <area shape="poly" coords="414,212,417,209,423,208,429,208,435,205,442,199,447,191,452,186,458,190,469,191,479,193,483,182,493,171,494,157,492,136,480,145,468,148,461,144,467,127,462,109,455,90,442,73,432,67,402,61,381,76,382,81,403,87,407,99,412,103,410,128,415,151,418,187,419,193,412,212," 
            href="greatlakes" alt="Great Lakes" title="Great Lakes"/> 
            <area shape="poly" coords="477,195,485,182,494,171,496,155,493,135,507,120,505,113,514,108,541,78,578,63,584,36,589,23,600,23,622,55,604,74,593,90,594,107,605,111,586,123,567,146,567,169,558,197,542,182,536,174,527,173,521,175,514,187,509,190,503,205,491,210," 
            href="northeast" alt="Northeast" title="Northeast"/> 
            <area shape="poly" coords="415,212,417,209,428,208,435,204,445,194,452,187,465,192,473,192,479,199,486,206,493,208,505,204,511,186,523,170,536,174,539,182,544,192,551,198,557,205,563,219,555,236,541,251,521,275,510,286,507,311,536,370,535,392,526,395,516,380,502,360,498,355,490,336,477,325,462,331,441,323,409,333,412,347,393,347,384,346,375,340,349,341,352,316,346,304,347,289,383,288,381,279,398,241,403,224,410,223,411,217," 
            href="southeast" alt="Southeast" title="Southeast"   /> 
            <area shape="poly" coords="84,271,85,263,89,253,94,245,91,233,94,221,102,218,105,206,164,217,248,226,337,228,337,233,391,233,388,241,397,241,392,253,382,276,381,288,346,288,346,305,353,315,351,332,347,342,325,356,310,363,302,379,302,378,302,399,290,396,277,386,253,341,236,336,228,347,223,349,209,340,205,329,195,314,185,302,164,299,164,298,162,306,126,300," 
            href="southcentral" alt="South" title="South"   /> 
            <area shape="poly" coords="12,101,53,114,42,158,91,233,93,247,87,254,86,268,54,264,39,235,20,223,9,189,7,162,3,145,1,121,10,102,"  
            href="california" alt="California" title="California"   /> 
            <area shape="poly" coords="239,38,229,161,250,165,247,226,336,229,337,234,390,234,390,240,398,240,398,232,403,228,406,222,414,216,418,194,417,188,417,182,415,154,410,140,411,104,407,96,404,87,383,81,373,73,382,55,332,42,277,40," 
            href="midwest" alt="Midwest" title="Midwest"   /> 
            <area shape="poly" coords="247,226,104,205,101,220,97,220,92,233,42,158,54,114,88,121,92,85,105,66,101,61,111,17,189,31,238,38,229,162,252,165," 
            href="mountain" alt="Rockies" title="Rockies"   /> 
        </map> 
        <div style="clear: both; width: 100%">&nbsp;</div>
    </div>
</div>
<script>
$(document).ready(function() {
    $.fn.maphilight.defaults = {
        fill: true,
        fillColor: '000000',
        fillOpacity: 0.2,
        stroke: false,
        strokeColor: 'ff0000',
        strokeOpacity: 1,
        strokeWidth: 1,
        fade: true,
        alwaysOn: false,
        neverOn: false,
        groupBy: false
    }
    $('#milk-map').maphilight();
});

</script>
