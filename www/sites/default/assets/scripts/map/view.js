$(document).ready(function() {
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-view');
    Sourcemap.loadSupplychain(window.location.href.split('/').pop(), function(data) {
        Sourcemap.map_instance.mapSupplychain(Sourcemap.factory('supplychain', data.supplychain));
    });
});
