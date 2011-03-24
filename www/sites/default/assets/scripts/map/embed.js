$(document).ready(function() {
    Sourcemap.embed_params = Sourcemap.embed_params || {};
    Sourcemap.embed_instance = new Sourcemap.Map.Embed(Sourcemap.embed_params);

    // get scid from inline script
    var scid = Sourcemap.embed_supplychain_id;

    // fetch supplychain
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.embed_instance.map.addSupplychain(sc);
    });
});
