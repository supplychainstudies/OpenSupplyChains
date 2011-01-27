Sourcemap.Map = function(element_id, o) {
    this.broadcast('map:instantiated', this);
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
    "auto_init": true, "element_id": "map",
    "supplychains_uri": "services/supplychains/",
    "stop_style": {
        "default": {
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": 0,
            "strokeColor": "#072",
            "fontColor": "#eee",
            "fontSize": "${size}",
            "opacity": 0.8
        },
        "select": {
            "fillColor": "#050"
        }
    }, "draw_hops": true,
    "hop_style": {
        "default": {
            "strokeWidth": 3,
            "strokeColor": "#072"
        },
        "select": {
            "strokeColor": "#050",
            "strokeWidth": 4
        }
    }
}

Sourcemap.Map.prototype.init = function() {
    this.initMap().initBaseLayer().initLayers().initControls();
    var p = new OpenLayers.LonLat(-122.8764, 42.3263);
    p.transform(new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject());
    this.map.setCenter(p);
    this.map.zoomTo(2);
    this.supplychains = {};
    this.broadcast('map:initialized', this);
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
        /*"restrictedExtent": new OpenLayers.Bounds(
            -20037508.43, -20037508.43,
            20037508.43, 20037508.43
        ),*/
        "minZoomLevel": 2,
        "controls": [
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.ArgParser(),
            new OpenLayers.Control.Attribution(),
            new OpenLayers.Control.ZoomPanel()
        ]
    };
    this.map = new OpenLayers.Map(this.options.element_id, options);
    this.broadcast('map:openlayers_map_initialized', this);
    return this;
}

Sourcemap.Map.prototype.initBaseLayer = function() {
    /*this.map.addLayer(new OpenLayers.Layer.Google(
        "Google Streets",
        {
            'sphericalMercator': true, "wrapeDateLine": true,
            "type": google.maps.MapTypeId.TERRAIN
        }
    ));*/
    // todo: make this togglable
    this.map.addLayer(new OpenLayers.Layer.CloudMade(
        "Cloudmade", {
        "key": "BC9A493B41014CAABB98F0471D759707",
        "styleId": 4993,
        "wrapDateLine": true
    }));
    this.broadcast('map:base_layer_initialized', this);
    return this;
}

Sourcemap.Map.prototype.initLayers = function() {
    this.broadcast('map:layers_initialized', this);
    return this;
}

Sourcemap.Map.prototype.initControls = function() {
    this.addControl('select', 
        new OpenLayers.Control.SelectFeature([this.getLayer('stops'), this.getLayer('hops')], {
            "onSelect": OpenLayers.Function.bind(function(feature) { this.broadcast('featureSelected', {'map': this, 'feature': feature}); }, this)
        })
    );
    this.controls.select.activate();
    this.broadcast('map:controls_initialized', this, ['select']);
    return this;
}

Sourcemap.Map.prototype.addLayer = function(label, layer) {
    this.map.addLayer(layer);
    this.layers[label] = layer;
    return this;
}

Sourcemap.Map.prototype.addStopLayer = function(scid) {
    var slayer = new OpenLayers.Layer.Vector(
        "Stop Layer - Supplychain #"+scid, {
            "sphericalMercator": true,
            "styleMap": new OpenLayers.StyleMap(this.options.stop_style)
        }
    );
    this.addLayer(([scid, 'stops']).join('-'), slayer);
    return this;
}

Sourcemap.Map.prototype.addHopLayer = function(scid) {
    var hlayer = new OpenLayers.Layer.Vector(
        "Hop Layer - Supplychain #"+scid, {
            "sphericalMercator": true,
            "styleMap": new OpenLayers.StyleMap(this.options.hop_style)
        }
    );
    this.addLayer(([scid, 'hops']).join('-'), hlayer);
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

Sourcemap.Map.prototype.removeStopLayer = function(scid) {
    return this.removeLayer(([scid, 'stops']).join('-'));
}

Sourcemap.Map.prototype.removeHopLayer = function(scid) {
    return this.removeLayer(([scid, 'hops']).join('-'));
}

Sourcemap.Map.prototype.getLayer = function(label) {
    return this.layers[label];
}

Sourcemap.Map.prototype.getStopLayer = function(scid) {
    var llabel = ([scid, 'stops']).join('-');
    return this.layers[llabel];
}

Sourcemap.Map.prototype.getHopLayer = function(scid) {
    var llabel = ([scid, 'hops']).join('-');
    return this.layers[llabel];
}

Sourcemap.Map.prototype.addControl = function(label, control) {
    this.map.addControl(control);
    this.controls[label] = control;
    return this;
}

Sourcemap.Map.prototype.getControl = function(label) {
    return this.controls[label];
}

Sourcemap.Map.prototype.mapSupplychain = function(scid) {
    var supplychain = this.findSupplychain(scid);
    if(!(supplychain instanceof Sourcemap.Supplychain))
        throw new Error('Supplychain not found/Sourcemap.Supplychain required.');
    for(var i=0; i<supplychain.stops.length; i++) {
        this.mapStop(supplychain.stops[i], scid);
    }
    for(var i=0; i<supplychain.hops.length; i++) {
        this.mapHop(supplychain.hops[i], scid);
    }
    this.broadcast('map:supplychain_mapped', this, supplychain);
}

Sourcemap.Map.prototype.mapStop = function(stop, scid) {
    if(!(stop instanceof Sourcemap.Stop))
        throw new Error('Sourcemap.Stop required.');
    var new_feature = (new OpenLayers.Format.WKT()).read(stop.geometry);
    new_feature.attributes.supplychain_id = stop.supplychain_id;
    new_feature.attributes.stop_id = stop.local_id;
    new_feature.attributes.size = 6;
    new_feature.attributes.color = '#072';
    if(this.prepareStopFeature instanceof Function) {
        this.prepareStopFeature(stop, new_feature);
    }
    this.broadcast('map:stop_mapped', this, this.findSupplychain(scid), stop, new_feature);
    this.getStopLayer(scid).addFeatures([new_feature]);
}

Sourcemap.Map.prototype.mapHop = function(hop, scid) {
    if(!(hop instanceof Sourcemap.Hop))
        throw new Error('Sourcemap.Hop required.');
    var new_feature = (new OpenLayers.Format.WKT()).read(hop.geometry);
    new_feature.attributes.supplychain_id = hop.supplychain_id;
    new_feature.attributes.hop_id = hop.local_id;
    new_feature.attributes.from_stop_id = hop.from_stop_id;
    new_feature.attributes.to_stop_id = hop.to_stop_id;
    new_feature.attributes.size = 4;
    this.broadcast('map:hop_mapped', this, this.findSupplychain(scid), hop, new_feature);
    this.getHopLayer(scid).addFeatures([new_feature]);
}

Sourcemap.Map.prototype.clearMap = function() {
    // clear map.
}

Sourcemap.Map.prototype.findSupplychain = function(scid) {
    if(scid instanceof Sourcemap.Supplychain)
        scid = scid.local_id;
    return this.supplychains[scid];
}

Sourcemap.Map.prototype.addSupplychain = function(supplychain) {
    var scid = supplychain.local_id;
    this.supplychains[scid] = supplychain;
    this.removeStopLayer(scid);
    this.addStopLayer(scid).addHopLayer(scid);
    this.mapSupplychain(scid);
    this.broadcast('map:supplychain_added', this, supplychain);
    return this;
}

Sourcemap.Map.prototype.removeSupplychain = function(scid) {
    var sc = this.findSupplychain(scid);
    var removed = false;
    if(sc && sc.local_id) {
        var scid = sc.local_id;
        removed = this.supplychains[scid];
        delete this.supplychains[scid];
        this.broadcast('map:supplychain_removed', this, removed, scid);
    }
    return removed;
}
