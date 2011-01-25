$(document).ready(function() {
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-embed');
    Sourcemap.loadSupplychain(window.location.href.split('/').pop(), function(data) {
        Sourcemap.map_instance.mapSupplychain(Sourcemap.factory('supplychain', data.supplychain));
    });
    $(document).bind('map:supplychain_mapped', function(evt, map) {
        if(map === Sourcemap.map_instance)
            (new Sourcemap.MapTour(Sourcemap.map_instance));
    });
});
