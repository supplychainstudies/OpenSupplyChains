$(document).ready(function() {

    // get scid from inline script
    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();

    // fetch supplychain
    Sourcemap.loadSupplychain(scid, function(sc) {
        new Sourcemap.Map.Blog(sc);
    });
});