Sourcemap.Map = function(element_id, o) {
    this.broadcast('mapInstantiated', this);
    this.supplychain = null;
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
        {
            'sphericalMercator': true, "wrapeDateLine": true,
            "type": google.maps.MapTypeId.TERRAIN
        }
    ));
    this.broadcast('mapBaseLayerInitialized', this);
    return this;
}

Sourcemap.Map.prototype.initLayers = function() {
    var stylemap = new OpenLayers.StyleMap({
        "default": {
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": 1,
            "strokeColor": "#eee",
            "fontColor": "#eee",
            "fontSize": "${size}"//,
            /*"label": "${label}",
            "fontSize": "16px",
            "fontFamily": "Arial, sans-serif",
            "fontWeight": "bold",
            "fontStyle": "italic",
            "labelAlign": "tm",
            "fontOpacity": .9,
            "labelXOffset": "${size}",
            "labelYOffset": "${size}"*/

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
    this.controls.drag.activate();
    this.addControl('mouse',
        new OpenLayers.Control.MousePosition()
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
            // notice this event fires _after_ the callback runs.
            _that.broadcast('mapSupplychainLoaded', this, data);
        }
    );
}

Sourcemap.Map.prototype.saveSupplychain = function(supplychain_id) {
    // this.findSupplychain(supplychain_id);
    // save supplychain
    // this.broadcast('mapSupplychainSaved', this, supplychain); asynch!
}

Sourcemap.Map.prototype.mapSupplychain = function(supplychain) {
    if(!(supplychain instanceof Sourcemap.Supplychain))
        throw new Error('Sourcemap.Supplychain required.');
    this.supplychain = supplychain;
    var vols = [];
    for(var i=0; i<supplychain.stops.length; i++) {
        var stop = supplychain.stops[i];
        var volp = stop.getAttr('org.mysourcemap.stonyfield.volp', 0);
        if(typeof volp == 'string') volp = parseFloat(volp.replace(/%/, ''));
        if(!volp) volp = 0;
        stop.attributes['org.mysourcemap.stonyfield.volp'] = volp;
        vols.push(volp);
    }
    supplychain.attributes['org.mysourcemap.stonyfield.vmax'] = Math.max.apply(Math, vols);
    supplychain.attributes['org.mysourcemap.stonyfield.vmin'] = Math.min.apply(Math, vols);
    for(var i=0; i<supplychain.stops.length; i++) {
        this.mapStop(supplychain.stops[i], supplychain);
    }
    for(var i=0; i<supplychain.hops.length; i++) {
        this.mapHop(supplychain.hops[i]);
    }
    this.broadcast('mapSupplychainMapped', this);
}

Sourcemap.Map.prototype.mapStop = function(stop, supplychain) {
    if(!(stop instanceof Sourcemap.Stop))
        throw new Error('Sourcemap.Stop required.');
    var new_feature = (new OpenLayers.Format.WKT()).read(stop.geometry);
    new_feature.attributes.supplychain_id = stop.supplychain_id;
    new_feature.attributes.stop_id = stop.local_id;
    new_feature.attributes.size = 6;
    if(this.prepareStopFeature instanceof Function) {
        this.prepareStopFeature(stop, new_feature);
    }
    this.broadcast('mapStopMapped', this, stop, new_feature);
    this.layers.stops.addFeatures([new_feature]);
}

Sourcemap.Map.prototype.mapHop = function(hop) {
    if(!(hop instanceof Sourcemap.Hop))
        throw new Error('Sourcemap.Hop required.');
    var new_feature = (new OpenLayers.Format.WKT()).read(hop.geometry);
    new_feature.attributes.supplychain_id = hop.supplychain_id;
    new_feature.attributes.hop_id = hop.local_id;
    new_feature.attributes.from_stop_id = hop.from_stop_id;
    new_feature.attributes.to_stop_id = hop.to_stop_id;
    new_feature.attributes.size = 8;
    this.broadcast('mapHopMapped', this, hop, new_feature);
    this.layers.hops.addFeatures([new_feature]);
}

Sourcemap.Map.prototype.clearMap = function() {
    // clear map.
}
