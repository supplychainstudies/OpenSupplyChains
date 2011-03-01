$(document).ready(function() {
    Sourcemap.TPL_PATH = "sites/default/assets/scripts/tpl/";
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-embed', {
        "prep_popup": function(ref, ftr, pop) {
            var t = ['popup'];
            var tscope = {"popup": pop, "feature": ftr};
            if(ref instanceof Sourcemap.Stop) {
                t.push('stop');
                tscope.stop = ref;
            } else if(ref instanceof Sourcemap.Hop) {
                t.push('hop');
                tscope.hop = ref;
            }
            Sourcemap.template('embed/'+t.join('-'), $.proxy(function(p, tx, th) {
                $(this.popup.contentDiv).html(th);
            }, tscope), tscope);
        }
    });
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
        Sourcemap.map_tour = new Sourcemap.MapTour(Sourcemap.map_instance, {"features": features});
        // the line below has to be here because, otherwise,
        // openlayers doesn't properly position the popup in webkit (chrome)
        Sourcemap.map_instance.map.zoomIn();
        Sourcemap.map_instance.map.zoomToExtent(
            Sourcemap.map_instance.getStopLayer(sc.instance_id).getDataExtent()
        );
        var overlay = $('<div class="sourcemap-embed-overlay" id="map-overlay"></div>');
        overlay.css({"width": "100%", "height": "10%",
            "position": "absolute", "top": 0, "left": 0, "z-index": 1000,
            "overflow": "hidden"
        });
        Sourcemap.map_overlay = overlay;
        $(Sourcemap.map_instance.map.div).css("position", "relative");
        $(Sourcemap.map_instance.map.div).append(overlay);
        Sourcemap.TPL_PATH = "sites/default/assets/scripts/tpl/";
        Sourcemap.template('embed/overlay/supplychain', function(p, tx, th) {
            $(Sourcemap.map_overlay).html(th);
        }, sc);
        var ze = new OpenLayers.Control.ZoomToMaxExtent({"title": "zoom all the way out"});
        var zi = new OpenLayers.Control.ZoomIn({"title": "zoom in"});
        var zo = new OpenLayers.Control.ZoomOut({"title": "zoom out"});
        var cpanel = new OpenLayers.Control.Panel({"defaultControl": ze});
        cpanel.addControls([zo, ze, zi]);
        Sourcemap.map_instance.map.addControl(cpanel);

        // pause tour on click
        Sourcemap.map_instance.map.events.register('click', Sourcemap.map_instance, function() {
            Sourcemap.map_tour.wait();
        });

        /*
        $(Sourcemap.map_overlay).data("state", 1);
        $(Sourcemap.map_overlay).click(function() {
            var st = $(this).data("state");
            if(st == 0) {
                st = -1;
                $(this).animate({"width": "100%", "height": "20%"}, 750, function() { $(Sourcemap.map_overlay).data("state", 1); });
            } else if(st == 1) {
                st = -1;
                $(this).animate({"width": "0%", "height": "20%"}, 750, function() { $(Sourcemap.map_overlay).data("state", 0); });
            }
        });*/
    });
});
