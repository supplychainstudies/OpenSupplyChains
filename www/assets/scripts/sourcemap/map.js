Sourcemap.Map = function(element_id, o) {
    var o = o || {};
    o.element_id = element_id;
    Sourcemap.Configurable.call(this, o);
    this.broadcast('mapInstantiated', this);
}

Sourcemap.Map.prototype = new Sourcemap.Configurable();

Sourcemap.Map.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.prototype.defaults = {
    "auto_init": true, "element_id": "sourcemap",
    "supplychains_uri": "services/supplychains/"
}

Sourcemap.Map.prototype.init = function() {
    this.supplychains = [];
    this.broadcast('mapInitialized', this);
}

Sourcemap.Map.prototype.loadSupplychain = function(remote_id, callback) {
    // fetch and initialize supplychain
    // this.broadcast('mapSupplychainLoaded', this, supplychain);
}

Sourcemap.Map.prototype.saveSupplychain = function(supplychain_id) {
    // this.findSupplychain(supplychain_id);
    // save supplychain
    // this.broadcast('mapSupplychainSaved', this, supplychain); asynch!
}
