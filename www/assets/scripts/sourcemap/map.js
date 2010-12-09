Sourcemap.Map = function(element_id, o) {
    this.broadcast('mapInstantiated', this);
    this.supplychains = [];
    this.layers = {};
    this.controls = {};
    var o = o || {};
    o.element_id = element_id;
    Sourcemap.Configurable.call(this, o);
    this.local_id = Sourcemap.local_id("sourcemap");
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
    this.initMap().initBaseLayer().initLayers().initControls();
    var p = new OpenLayers.LonLat(-122.8764, 42.3263);
    p.transform(new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject());
    this.map.setCenter(p);
    this.map.zoomTo(0);
    this.broadcast('mapInitialized', this);
    return this;
}

Sourcemap.Map.prototype.initMap = function() {
    var options = {
        "projection": new OpenLayers.Projection("EPSG:3857"),
        "displayProjection": new OpenLayers.Projection("EPSG:4326"),
        "units": "m",
        "maxExtent": new OpenLayers.Bounds(
            -20037508.43, -20037508.43,
            20037508.43, 20037508.43
        ),
        "restrictedExtent": new OpenLayers.Bounds(
            -20037508.43, -20037508.43,
            20037508.43, 20037508.43
        ),
        "minZoomLevel": 2
    };
    this.map = new OpenLayers.Map(this.options.element_id, options);
    this.broadcast('mapOpenLayersMapInitialized', this);
    return this;
}

Sourcemap.Map.prototype.initBaseLayer = function() {
    this.map.addLayer(new OpenLayers.Layer.Google(
        "Google Streets",
        {'sphericalMercator': true, "wrapeDateLine": true}
    ));
    this.broadcast('mapBaseLayerInitialized', this);
    return this;
}

Sourcemap.Map.prototype.initLayers = function() {
    var stylemap = new OpenLayers.StyleMap({
        "default": {
            "pointRadius": "${size}",
            "fillColor": "red"
        },
        "select": {
            "fillColor": "yellow"
        }
    });
    this.addLayer('stops', new OpenLayers.Layer.Vector(
        "Stop Layer",
        {
            "sphericalMercator": true,
            "styleMap": stylemap
        }
    ));
    this.addLayer('hops', new OpenLayers.Layer.Vector(
        "Hop Layer",
        {
            'sphericalMercator': true,
            "styleMap": new OpenLayers.StyleMap({
                "default": {
                    "strokeWidth": 4,
                    "strokeColor": "red"
                },
                "select": {
                    "strokeColor": "yellow",
                    "strokeWidth": 8
                }
            })
        }
    ));
    this.map.raiseLayer(this.getLayer('stops'),1);
    this.broadcast('mapLayersInitialized', this);
    return this;
}

Sourcemap.Map.prototype.initControls = function() {
    this.addControl('select', 
        new OpenLayers.Control.SelectFeature([this.getLayer('stops'), this.getLayer('hops')], {
            "onSelect": OpenLayers.Function.bind(function(feature) { this.broadcast('featureSelected', {'map': this, 'feature': feature}); }, this)
        })
    );
    this.addControl('drag',
        new OpenLayers.Control.DragFeature(this.getLayer('stops'))
    );
    this.broadcast('mapControlsInitialized', this);
    return this;
}

Sourcemap.Map.prototype.addLayer = function(label, layer) {
    this.map.addLayer(layer);
    this.layers[label] = layer;
    return this;
}

Sourcemap.Map.prototype.removeLayer = function(label) {
    if(this.layers[label]) {
        var layer = this.layers[label];
        this.map.removeLayer(layer.id);
        delete this.layers[label];
    }
    return this;
}

Sourcemap.Map.prototype.getLayer = function(label) {
    return this.layers[label];
}

Sourcemap.Map.prototype.addControl = function(label, control) {
    this.map.addControl(control);
    this.controls[label] = control;
    return this;
}

Sourcemap.Map.prototype.getControl = function(label) {
    return this.controls[label];
}

Sourcemap.Map.prototype.loadSupplychain = function(remote_id, callback) {
    // fetch and initialize supplychain
    var _that = this;
    var _remote_id = remote_id;
    $.get('services/supplychains/'+remote_id, {},  function(data) {
            callback.apply(this, arguments);
            _that.broadcast('mapSupplychainLoaded', this, data);
        }
    );
}

Sourcemap.Map.prototype.saveSupplychain = function(supplychain_id) {
    // this.findSupplychain(supplychain_id);
    // save supplychain
    // this.broadcast('mapSupplychainSaved', this, supplychain); asynch!
}
