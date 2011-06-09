$(document).ready(function() {

    Sourcemap.embed_params = Sourcemap.embed_params || {};
    Sourcemap.embed_instance = new Sourcemap.Map.Base(Sourcemap.embed_params);

    Sourcemap.listen("map:supplychain_mapped", function(evt, map, sc) {
        var embed = Sourcemap.embed_instance;
        embed.user_loc = Sourcemap.embed_params.iploc ? Sourcemap.embed_params.iploc[0] : false;
        if(embed.options.locate_user) {
            if(embed.tour) embed.tour.stop();
            embed.showLocationDialog();
        }
    });

    // get scid from inline script
    var scid = Sourcemap.embed_supplychain_id;

    // fetch supplychain
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.embed_instance.map.addSupplychain(sc);
    });
});
