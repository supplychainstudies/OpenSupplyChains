$(document).ready(function() {
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-embed');
    var scid = new Number(window.location.href.split('/').pop());
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.map_instance.addSupplychain(sc);
    });
    $(document).bind('map:supplychain_mapped', function(evt, map) {
        if(map !== Sourcemap.map_instance)
            return;
        var features = [];
        for(var k in map.supplychains) {
            var sc = map.supplychains[k];
            var g = new Sourcemap.Supplychain.Graph(map.supplychains[k]);
            var order = g.depthFirstOrder();
            order = order.concat(g.islands());
            for(var i=0; i<order.length; i++)
                features.push(map.mapped_features[order[i]]);
        }
        var tour = new Sourcemap.MapTour(Sourcemap.map_instance, {"features": features});
        Sourcemap.map_instance.map.zoomToExtent(
            Sourcemap.map_instance.getStopLayer(sc.instance_id).getDataExtent()
        );
        var overlay = $('<div class="map-overlay" id="map-overlay"></div>');
        overlay.css("width", "100%").css("height", "100%").css("background-color", "red")
            .css("position", "absolute").css("top", 0).css("left", 0).css("z-index", 5000)
            .css("overflow", "hidden").hide();
        Sourcemap.map_overlay = overlay;
        $(Sourcemap.map_instance.map.div).css("position", "relative");
        $(Sourcemap.map_instance.map.div).append(overlay);
        Sourcemap.TPL_PATH = "sites/default/assets/scripts/tpl/";
        Sourcemap.template('embed/overlay/supplychain', function(p, tx, th) {
            $(Sourcemap.map_overlay).html(th).show('slide');
        }, {});
        $(Sourcemap.map_overlay).click(function() {
            $(this).hide('slide');
        });
    });
});
