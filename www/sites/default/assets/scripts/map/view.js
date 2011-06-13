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
        if(sc.editable){
            Sourcemap.log("Supplychain "+sc.remote_id+" is editable.");
            // Append the "Add Stop" button to the dock
            Sourcemap.view_instance.map.dockAdd('addstop', {
                "icon_url": "sites/default/assets/images/dock/add.png",
                "callbacks": {
                    "click": function() {
                        var geometry = this.map.center;
                        attributes = {};
                        var new_stop = new Sourcemap.Stop(
                            geometry, attributes
                        );
                        sc.addStop(new_stop);
                        // Reload supplychain
                        // todo: create an event for this and bind a reload function to it
                        this.getStopLayer(sc.instance_id).removeAllFeatures();
                        this.getHopLayer(sc.instance_id).removeAllFeatures();
                        console.log(sc);
                        this.mapSupplychain(sc.instance_id);
                    }
                }
            });
            
        // Append the "Save" button to the banner
        // var savebutton = $('<div class="banner-item '+nm.replace(/\s+/, '-')+'"><img src="'+icon_url+'" /></div>');
        }
        Sourcemap.view_instance.map.addSupplychain(sc);
        control = new OpenLayers.Control.Button();
        control.div = 'sourcemap-banner';
        Sourcemap.view_instance.map.map.addControl(control);
    });
});
