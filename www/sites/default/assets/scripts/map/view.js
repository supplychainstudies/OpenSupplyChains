$(document).ready(function() {
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-view');
    Sourcemap.loadSupplychain(window.location.href.split('/').pop(), function(data) {
        var sc = Sourcemap.factory('supplychain', data.supplychain);
        console.log(sc);
        Sourcemap.map_instance.addSupplychain(sc);
    });
    //Sourcemap.template('map/view/place', function(tpl, txt, loader) { console.log('loaded template: '+tpl); });
});
