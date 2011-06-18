$(document).ready(function() {
    Sourcemap.view_params = Sourcemap.view_params || {};
    Sourcemap.view_instance = new Sourcemap.Map.Base(Sourcemap.view_params);

    Sourcemap.listen("map:supplychain_mapped", function(evt, map, sc) {
        var view = Sourcemap.view_instance;
        view.user_loc = Sourcemap.view_params.iploc ? Sourcemap.view_params.iploc[0] : false;
        if(view.options.locate_user) {
            if(view.tour) view.tour.stop();
            view.showLocationDialog();
        }
    });

    // get scid from inline script
    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();

    // fetch supplychain
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.view_instance.map.addSupplychain(sc);
        if(sc.editable){
            new Sourcemap.Map.Editor(Sourcemap.view_instance);
        }
        // move to the middle
        Sourcemap.view_instance.map.map.zoomToExtent(
            Sourcemap.view_instance.map.getDataExtent(), true
        );
    });
});
