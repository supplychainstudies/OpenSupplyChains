$(document).ready(function() {
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-embed');
    Sourcemap.loadSupplychain(window.location.href.split('/').pop(), function(data) {
        var sc = Sourcemap.factory('supplychain', data.supplychain);
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
    });
});
