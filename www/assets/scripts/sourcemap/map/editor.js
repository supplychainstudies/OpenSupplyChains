Sourcemap.Map.Editor = function(map, o) {
    var o = o || {}
    this.map = map.map ? map.map : map;
    Sourcemap.Configurable.call(this);
    this.instance_id = Sourcemap.instance_id("sourcemap-editor");
}

Sourcemap.Map.Editor.prototype = new Sourcemap.Configurable();

Sourcemap.Map.Editor.prototype.defaults = {
    "trigger_events": true, "auto_init": true
}

Sourcemap.Map.Editor.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.Editor.prototype.init = function() {
    this.map.dockAdd('addstop', {
        "icon_url": "sites/default/assets/images/dock/add.png",
        "callbacks": {
            "click": $.proxy(function() {
                // make a suitable geometry
                var geometry = (new OpenLayers.Format.WKT()).write(
                    new OpenLayers.Feature.Vector(
                        new OpenLayers.Geometry.Point(this.map.map.center.lon, this.map.map.center.lat)
                    )
                );
                attributes = {
                    "title": "New Stop"
                };
                // make a new stop
                var new_stop = new Sourcemap.Stop(
                    geometry, attributes
                );
                // grab the first supplychain
                var sc = false;
                for(var k in this.map.supplychains) {
                    sc = this.map.supplychains[k];
                    break;
                }
                // add a stop to the supplychain object
                sc.addStop(new_stop);
                // redraw the supplychain
                //this.map.mapSupplychain(sc.instance_id);
                this.map.mapStop(new_stop, sc.instance_id);
                // get the new feature
                var f = this.map.stopFeature(sc.instance_id, new_stop.instance_id)
                // select the new feature
                //this.map.controls.select.select(f);
            }, this)
        }
    });
}

