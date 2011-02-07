$(document).ready(function() {
    var scid = window.location.href.split('/').pop();
    Sourcemap.loadSupplychain(scid, function(sc) {
        var ed = new Sourcemap.TabbedEditor('#supplychain-form-edit', sc);
    });
});
