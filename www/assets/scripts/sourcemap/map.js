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
    "draw_hops": true, "hops_as_arcs": false,
    "hops_as_bezier": true, "arrows_on_hops": true,
    "stop_style": {
        "default": {
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": 0,
            "strokeColor": "#072",
            "fontColor": "#eee",
            "fontSize": "${size}",
            "fillOpacity": 0.8
        },
        "select": {
            "fillColor": "#050",
            "fillOpacity": 1.0
        }
    }, 
    "hop_style": {
        "default": {
            "graphicName": "${type}",
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": "${width}",
            "strokeColor": "${color}",
            "fillOpacity": 0.9,
            "strokeOpacity": 0.8,
            "rotation": "${angle}"
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
    this.mapped_features = {};
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
        "styleId": 1,
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
    // todo: select feature controls for vector layers
    var layers = [];
    for(var k in this.layers) layers.push(this.layers[k]);
    if(layers.length) {
        this.addControl('select', 
            new OpenLayers.Control.SelectFeature(layers, {
                "onSelect": OpenLayers.Function.bind(
                    function(feature) { 
                        this.broadcast('featureSelected', {'map': this, 'feature': feature}); 
                    }, this)
            })
        );
        $(document).bind(['map:layer_added', 'map:layer_removed'], function(e, map, label, layer) {
            var layers = [];
            for(var k in map.layers) layers.push(map.layers[k]);
            map.controls.select.setLayer(layers);
        });
        this.controls.select.activate();
    }
    $(document).one('map:layer_added', function(e, map, label, layer) {
        if(!map.controls.select)
            map.initControls();
    });
    this.broadcast('map:controls_initialized', this, ['select']);
    return this;
}

Sourcemap.Map.prototype.addLayer = function(label, layer) {
    this.map.addLayer(layer);
    this.layers[label] = layer;
    this.broadcast('map:layer_added', this, label, layer);
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
        this.broadcast('map:layer_removed', this, layer);
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
    if(this.options.draw_hops) {
        for(var i=0; i<supplychain.hops.length; i++) {
            this.mapHop(supplychain.hops[i], scid);
        }
    }
    this.broadcast('map:supplychain_mapped', this, supplychain);
}

Sourcemap.Map.prototype.mapStop = function(stop, scid) {
    if(!(stop instanceof Sourcemap.Stop))
        throw new Error('Sourcemap.Stop required.');
    var new_feature = (new OpenLayers.Format.WKT()).read(stop.geometry);
    new_feature.attributes.supplychain_id = stop.supplychain_id;
    new_feature.attributes.local_stop_id = stop.local_stop_id; // todo: clarify this
    new_feature.attributes.stop_id = stop.local_id;
    new_feature.attributes.size = 6;
    new_feature.attributes.color = '#072';
    if(this.prepareStopFeature instanceof Function) {
        this.prepareStopFeature(stop, new_feature);
    }
    this.broadcast('map:stop_mapped', this, this.findSupplychain(scid), stop, new_feature);
    this.mapped_features[stop.local_id] = new_feature;
    this.getStopLayer(scid).addFeatures([new_feature]);
}

Sourcemap.Map.prototype.mapHop = function(hop, scid) {
    if(!(hop instanceof Sourcemap.Hop))
        throw new Error('Sourcemap.Hop required.');
    if(this.options.hops_as_arcs || this.options.hops_as_bezier) {
        var sc = this.supplychains[scid];
        var wkt = new OpenLayers.Format.WKT();
        var from_pt = wkt.read(sc.findStop(hop.from_stop_id).geometry).geometry;
        var to_pt = wkt.read(sc.findStop(hop.to_stop_id).geometry).geometry;
    }
    if(this.options.hops_as_arcs) {
        var new_feature = new OpenLayers.Feature.Vector(this.makeBentLine(from_pt, to_pt));
    } else if(this.options.hops_as_bezier) {
        var new_feature = new OpenLayers.Feature.Vector(this.makeBezierCurve(from_pt, to_pt));
    } else {
        var new_feature = (new OpenLayers.Format.WKT()).read(hop.geometry);
    }
    if(this.options.arrows_on_hops) {
        var new_arrow = this.makeArrow(new_feature.geometry, {"color": "#072", "size": 11});
        this.getHopLayer(scid).addFeatures([new_arrow]);
    }
    new_feature.attributes.supplychain_id = hop.supplychain_id;
    new_feature.attributes.hop_id = hop.local_id;
    new_feature.attributes.from_stop_id = hop.from_stop_id;
    new_feature.attributes.to_stop_id = hop.to_stop_id;
    new_feature.attributes.width = 4;
    new_feature.attributes.color = '#072';
    this.broadcast('map:hop_mapped', this, this.findSupplychain(scid), hop, new_feature);
    this.mapped_features[hop.local_id] = new_feature;
    this.getHopLayer(scid).addFeatures([new_feature]);
}

Sourcemap.Map.prototype.makeArrow = function(hop_geom, o) {
    if(!OpenLayers.Renderer.symbol.arrow)
        OpenLayers.Renderer.symbol.arrow = [-5, 5,  0,3,  5, 5,  0, -5,  -5, 5];
    var verts = hop_geom.getVertices();
    var from_pt = verts[0];
    var to_pt = verts[verts.length-1];
    var mid_pt = verts[Math.ceil(verts.length/2)];
    var angle = (Math.atan2(to_pt.x-from_pt.x, to_pt.y-from_pt.y)/Math.PI)*180;
    var attrs = {"type": "arrow", "width": 0, "angle": angle};
    var o = o || {};
    for(var k in o) attrs[k] = o[k];
    var a = new OpenLayers.Feature.Vector(mid_pt, attrs);
    return a;
}

Sourcemap.Map.prototype.makeBezierCurve = function(from, to) {
    var x0 = from.x;
    var y0 = from.y;
    var x1 = to.x;
    var y1 = to.y;

    var dx = x1 - x0;
    var dy = y1 - y0;

    var bzx = x0 + dx/4;
    var bzy = y1;

    var res = 100;

    var pts = [];
    for(var t=0.0; t<1.0; t += 1.0/res) {
        var x = (1-t) * (1-t) * x0 + 2 * (1-t) * t * bzx + t * t * x1;
        var y = (1-t) * (1-t) * y0 + 2 * (1-t) * t * bzy + t * t * y1;
        pts.push(new OpenLayers.Geometry.Point(x, y));
    }
    if(!to.equals(pts[pts.length-1]))
        pts.push(to.clone());
    return new OpenLayers.Geometry.MultiLineString([new OpenLayers.Geometry.LineString(pts)]);
}

Sourcemap.Map.prototype.makeBentLine = function(from, to) {
    // dzwarg's "polyline" bent line routine,
    // minus the many-globes business
    var resolution = 8.0;
    var points = [];
    var dx = to.x - from.x;
    var dy = to.y - from.y;
    var theta = (Math.PI/2) - Math.atan(dy/dx);
    var maxdisp = Math.sqrt(dx*dx+dy*dy) * 0.05;

    if(dx == 0 && dy == 0) {
        points.push(new OpenLayers.Geometry.Point(from.x, from.y));
    } else {
        var absintheta = Math.abs(Math.sin(theta));
        var abcostheta = Math.abs(Math.cos(theta));
        for(var p=0; p<resolution; p++) {
            var relamt = Math.sin(p/resolution*Math.PI) * maxdisp;
            if(absintheta < abcostheta) {
                relamt *= Math.abs(Math.sin(Math.PI*dx/dy));
            }
            var ddx = Math.cos(theta+Math.PI) * relamt;
            var ddy = Math.sin(theta) * relamt;

            points.push(
                new OpenLayers.Geometry.Point(
                    from.x + (dx*p/resolution) + ddx,
                    from.y + (dy*p/resolution) + ddy
                )
            );
        }
    }
    points.push(new OpenLayers.Geometry.Point(to.x, to.y));
    return new OpenLayers.Geometry.MultiLineString([new OpenLayers.Geometry.LineString(points)]);
}

Sourcemap.Map.prototype.clearMap = function() {
    // clear map.
}

Sourcemap.Map.prototype.findSupplychain = function(scid) {
    if(scid instanceof Sourcemap.Supplychain)
        scid = scid.local_id;
    return this.supplychains[scid];
}

Sourcemap.Map.prototype.getSupplychains = function() {
    var scs = [];
    for(var scid in this.supplychains) {
        scs.push(scid);
    }
    return scs;
}

Sourcemap.Map.prototype.addSupplychain = function(supplychain) {
    var scid = supplychain.local_id;
    this.supplychains[scid] = supplychain;
    //this.removeStopLayer(scid);
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
