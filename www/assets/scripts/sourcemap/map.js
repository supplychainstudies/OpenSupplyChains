Sourcemap.Map = function(element_id, o) {
    var o = o || {};
    o.element_id = element_id;
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.Map.prototype = new Sourcemap.Configurable();
Sourcemap.Map.prototype.defaults = {
    "auto_init": true, "element_id": "sourcemap"
}

Sourcemap.Map.prototype.init = function() {
    this.supplychains = [];
}
