Sourcemap.Map = function(element_id, o) {
    this.broadcast('map:instantiated', this);
    this.layers = {};
    this.controls = {};
    this.dock_controls = {};
    this.dock_el = null;
    var o = o || {};
    o.element_id = element_id;
    Sourcemap.Configurable.call(this, o);
    this.instance_id = Sourcemap.instance_id("sourcemap");
}

Sourcemap.Map.prototype = new Sourcemap.Configurable();

Sourcemap.Map.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.prototype.defaults = {
    "auto_init": true, "element_id": "map",
    "supplychains_uri": "services/supplychains/",
    "zoom_control": true,
    "ol_layer_switcher": false, "tileswitcher": false,
    "google_tiles": false, "basetileset": "cloudmade",
    "cloudmade_tiles": true, "animation_enabled":false,
    "draw_hops": true, "hops_as_arcs": true,
    "hops_as_bezier": false, "arrows_on_hops": true,
    "default_feature_color": "#35a297", "clustering": true,
    "default_feature_colors": ["#35a297", "#b01560", "#e2a919"],
    "stop_style": {
        "default": {
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": "${swidth}",
            "strokeColor": "${scolor}",
            "strokeOpacity": 0.5,
            "fontColor": "${fcolor}",
            "cursor":"pointer",
            "fontSize": "0.8em",
            "fontFamily": "Helvetica, sans-serif",
            "fillOpacity": 1,
            "label": "${label}",
            "labelAlign": "cb",
            "labelXOffset": 0,
            "labelYOffset": -35, // fixme: this is bad
        },
        "select": {
            "fillColor": "#ffffff",
            "fillOpacity": 1.0
        },
        "cluster": {
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeColor": "${scolor}",
            "strokeOpacity": 0.5,
            "strokeWidth": "${swidth}",
            "fontColor": "${fcolor}",
            "cursor":"pointer",
            "fontSize": "0.8em",
            "fontFamily": "Helvetica, sans-serif",
            "fillOpacity": 1,
            "label": "${label}",
            "labelAlign": "cb",
            "labelXOffset": 0,
            "labelYOffset": "${yoffset}" // fixme: this is bad
        },
        "hascontent": {
            "strokeWidth": "${swidth}",
            "strokeColor": "${scolor}",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": -35,
            "fontSize": "em",
            "fillColor": "${color}"
        },
        "connecting": {
            "graphicName": "stareight",
            "pointRadius": "${size}",
            "fillcolor": "${color}"
        }
    }, 
    "hop_style": {
        "default": {
            "graphicName": "${type}",
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": "${width}",
            "strokeColor": "${color}",
            "fillOpacity": 0.6,
            "strokeOpacity": "${opacity}",
            "rotation": "${angle}"
        },
        "select": {
            "strokeColor": "${scolor}",
            "fillColor": "#ffffff",
            "fillOpacity": 1.0
        }
    }, "prep_stop": null, "prep_hop": null
}

Sourcemap.Map.prototype.init = function() {
    this.initMap().initBaseLayer().initLayers().initControls().initDock();
    var p = new OpenLayers.LonLat(-122.8764, 42.3263);
    p.transform(new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject());
    this.map.setCenter(p);
    this.map.zoomTo(2);
    this.supplychains = {};
    this.mapped_features = {};
    this.stop_features = {}; // dicts of stop ftrs keyed by parent supplychain
    this.hop_features = {}; // dicts of hop ftrs keyed by parent supplychain
    this.cluster = null;
    this.prepareStopFeature = this.options.prep_stop ? this.options.prep_stop : false;
    this.prepareHopFeature = this.options.prep_hop ? this.options.prep_hop : false;
    this.broadcast('map:initialized', this);
    return this;
}
Sourcemap.Map.prototype.initMap = function() {
    var controls = [
            new OpenLayers.Control.Navigation({"handleRightClicks": true}),
            new OpenLayers.Control.ArgParser(),
            new OpenLayers.Control.Attribution(),
    ];
    //if(this.options.zoom_control) 
    //    controls.push(new OpenLayers.Control.ZoomPanel());
    var options = {
        "theme": "assets/scripts/openlayers/theme/sourcemap/style.css",
        "projection": new OpenLayers.Projection("EPSG:900913"),
        //"displayProjection": new OpenLayers.Projection("EPSG:4326"),
        "controls": controls
    };
    this.map = new OpenLayers.Map(this.options.element_id, options);
    this.broadcast('map:openlayers_map_initialized', this);
    return this;
}

Sourcemap.Map.prototype.initBaseLayer = function() {
    this.map.addLayer(new OpenLayers.Layer.Google(
        "terrain", {
            'sphericalMercator': true, "wrapeDateLine": true,
            "type": google.maps.MapTypeId.TERRAIN,
            "animationEnabled": this.options.animation_enabled
    }));
    this.map.addLayer(new OpenLayers.Layer.CloudMade(
        "cloudmade", {
        "key": "BC9A493B41014CAABB98F0471D759707",
        "styleId": 41413,
        "minZoomLevel": 3,
        "maxZoomLevel": 12
    }));
    
    this.map.addLayer( new OpenLayers.Layer.Google(
        "satellite", {
        "sphericalMercator": true,
        "type": google.maps.MapTypeId.HYBRID,
        "wrapDateLine": true, "animationEnabled": this.options.animation_enabled
    })); 
    
    if(this.options.basetileset) {
        this.map.setBaseLayer(
            this.map.getLayersByName(this.options.basetileset).pop()
        );
        this.map.minZoomLevel = this.map.baseLayer.minZoomLevel;
        this.map.maxZoomLevel = this.map.baseLayer.maxZoomLevel;
    }
    this.broadcast('map:base_layer_initialized', this);
    return this;
}

Sourcemap.Map.prototype.setBaseLayer = function(nm) {
    this.map.setBaseLayer(this.map.getLayersByName(nm).pop());
    return this;
}

Sourcemap.Map.prototype.initLayers = function() {
    this.broadcast('map:layers_initialized', this);
    return this;
}

Sourcemap.Map.prototype.initDock = function() {
    this.dock_controls = this.dock_controls || {};
    // Needed for centering
    this.dock_outerwrap = $('<div class="sourcemap-dock-outerwrap"></div>');
    this.dock_content = $('<div class="sourcemap-dock-content"></div>');
    this.dock_element = $('<div class="sourcemap-dock"></div>');
    $(this.map.div).css("position", "relative").append(
        this.dock_element.append(this.dock_outerwrap.append(this.dock_content)));
    this.dockAdd('zoomin', {
        "ordinal": 2,
        "title": 'Zoom In',
        "icon_url": "sites/default/assets/images/dock/zoomin.png",
        "callbacks": {
            "click": function() {
                this.controls.select.unselectAll(); 
                this.map.zoomIn();
                this.reselect();
            }
        }
    });
    this.dockAdd('zoomout', {
        "ordinal": 1,
        "title": 'Zoom Out',
        "icon_url": "sites/default/assets/images/dock/zoomout.png",
        "callbacks": {
            "click": function() {
                this.controls.select.unselectAll();
                this.map.zoomOut();
                this.reselect();
            }
        }
    });
    this.dockAdd('firstSpacer', {
        "ordinal": 3,
        "title": 'Spacer',
    });
    this.dockAdd('secondSpacer', {
        "ordinal": 5,
        "title": 'Spacer',
    });
    return this;
}

Sourcemap.Map.prototype.dockAdd = function(nm, o) {
    var icon_url = o.icon_url ? o.icon_url : null;
    var title = o.title ? o.title : null;
    var callbacks = o.callbacks ? o.callbacks : {};
    var image = o.icon_url ? '<img src="'+o.icon_url+'" alt="'+o.title+'" />' : "";
    var content = o.content ? o.content : ""
    this.dockRemove(nm);
    this.dock_controls[nm] = o;
    var cel = $('<div class="control '+nm.replace(/\s+/, '-')+'"><div class="content">'+image+content+'</div></div>');
    $(this.dock_content).append(cel);
    if(callbacks.click) {
        $(cel).click($.proxy(callbacks.click, this));
    }
    if(o.toggle) {
        $(cel).addClass("toggle");
        // todo: callback arg here...
    }
    return this.dockPack();
}

Sourcemap.Map.prototype.dockToggle = function(nm) { 
    var cel = $(this.dock_content).find(".control."+nm);
    if(cel) {
        if($(cel).hasClass("active")) $(cel).removeClass("active");
        else $(cel).addClass("active");
    }
    return this;
}

Sourcemap.Map.prototype.dockToggleActive = function(nm) {
    var cel = $(this.dock_content).find(".control."+nm);
    if(cel) {
        $(cel).addClass("active");
    }
    return this;
}

Sourcemap.Map.prototype.dockToggleInactive = function(nm) {
    var cel = $(this.dock_content).find(".control."+nm);
    if(cel) {
        $(cel).removeClass("active");
    }
    return this;
}

Sourcemap.Map.prototype.dockControlEl = function(nm) {
    return $(this.dock_content).find('.control.'+nm.replace(/\s+/, '-'));
}

Sourcemap.Map.prototype.dockPack = function() {
    var controls = [];
    for(var c in this.dock_controls) {
        var ctrl = this.dock_controls[c];
        var o = ctrl.ordinal ? ctrl.ordinal : 9999;
        controls.push([c,o]);
    }
    controls.sort(function(a,b) { return a[1] > b[1] ? 1 : (a[1] < b[1] ? -1 : 0); });
    var order = [];
    for(var i=0; i<controls.length; i++) {
        this.dock_content.append(this.dockControlEl(controls[i][0]));
    }
    return this;
}

Sourcemap.Map.prototype.dockRemove = function(nm) {
    if(this.dock_controls[nm]) delete this.dock_controls[nm];
    if(this.dockControlEl(nm)) this.dockControlEl(nm).remove();
    return this.dockPack();
}

Sourcemap.Map.prototype.initControls = function() {
    // todo: select feature controls for vector layers
    var layers = [];
    for(var k in this.layers) layers.push(this.layers[k]);
    if(layers.length) {
        if(this.options.ol_layer_switcher) {
            this.addControl('layer_switcher',
                new OpenLayers.Control.LayerSwitcher()
            );
        }  
        if(this.options.tileswitcher) {
            this.initTileSwitcher();
        }
        this.addControl('select', 
            new OpenLayers.Control.SelectFeature(layers, {
                "geometryTypes": ["OpenLayers.Geometry.Point", "OpenLayers.Geometry.MultiLineString"],
                "onSelect": OpenLayers.Function.bind(
                    function(feature) {
                        this.last_selected = feature.attributes;
                        this.broadcast('map:feature_selected', this, feature); 
                    }, 
                    this
                ),
                "onUnselect": OpenLayers.Function.bind(
                    function(feature) {
                        this.broadcast('map:feature_unselected', this, feature); 
                    },
                    this
                ),
                "clickoutFeature": OpenLayers.Function.bind(
                    function(feature) {
                        this.controls.select.unselectAll();
                        this.broadcast('map:feature_clickout', this, feature); 
                    }, 
                    this
                )
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

Sourcemap.Map.prototype.updateControls = function() {
    var layers = [];
    for(var k in this.layers) layers.push(this.layers[k]);
    if(this.controls.select)
        this.controls.select.setLayer(layers);
    return this;
}

Sourcemap.Map.prototype.addLayer = function(label, layer) {
    this.map.addLayer(layer);
    this.layers[label] = layer;
    this.updateControls();
    this.broadcast('map:layer_added', this, label, layer);
    return this;
}

Sourcemap.Map.prototype.addStopLayer = function(scid) {
    var sc = this.findSupplychain(scid);
    this.cluster = new Sourcemap.Cluster({distance: 35, threshold: 2});
    var strategies = this.options.clustering ? [this.cluster] : [];
    var slayer = new OpenLayers.Layer.Vector(
        "Stops - "+sc.getLabel(), {
            "styleMap": new OpenLayers.StyleMap(this.options.stop_style),
            "displayOutsideMaxExtent": false,
            "maxExtent": this.map.getMaxExtent(),
            "wrapDateLine": false,
            "strategies": strategies
        }
    );    
    this.addLayer(([scid, 'stops']).join('-'), slayer);
    return this;
}

Sourcemap.Map.prototype.addHopLayer = function(scid) {
    var sc = this.findSupplychain(scid);
    var hlayer = new OpenLayers.Layer.Vector(
        "Hop Layer - Supplychain #"+sc.getLabel(), {
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
        this.map.removeLayer(layer);
        this.layers[label].destroy();
        delete this.layers[label];
        this.updateControls();
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

Sourcemap.Map.prototype.getDataExtent = function() {
    var ext = new OpenLayers.Bounds();
    for(var llabel in this.layers) {
        ext.extend(this.layers[llabel].getDataExtent());
    }
    return ext;
}

Sourcemap.Map.prototype.addControl = function(label, control) {
    this.map.addControl(control);
    this.controls[label] = control;
    return this;
}

Sourcemap.Map.prototype.removeControl = function(label, control) {
    this.map.removeControl(control);
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
    var reselect = true;
    if(this.getStopLayer(scid)) this.getStopLayer(scid).removeAllFeatures();
    if(this.getHopLayer(scid)) this.getHopLayer(scid).removeAllFeatures();
    var featureList = [];
    for(var i=0; i<supplychain.stops.length; i++) {
        featureList.push(this.mapStop(supplychain.stops[i], scid));
    }
    this.getStopLayer(scid).addFeatures(featureList);
    this.getStopLayer(scid);
    if(this.options.draw_hops) {
        for(var i=0; i<supplychain.hops.length; i++) {
            this.mapHop(supplychain.hops[i], scid);
        }
    }
    //if(supplychain.stops.length)
    //    this.map.zoomToExtent(this.getStopLayer(scid).getDataExtent());
    this.broadcast('map:supplychain_mapped', this, supplychain);
    if(reselect) this.reselect();
}

Sourcemap.Map.prototype.mapStop = function(stop, scid) {
    if(!(stop instanceof Sourcemap.Stop))
        throw new Error('Sourcemap.Stop required.');
    this.eraseStop(scid, stop.instance_id);
    var new_feature = (new OpenLayers.Format.WKT()).read(stop.geometry);
    // copy attributes for starters.
    new_feature.attributes = Sourcemap.deep_clone(stop.attributes);
    new_feature.attributes.supplychain_instance_id = scid;
    new_feature.attributes.local_stop_id = stop.local_stop_id; // todo: clarify this
    new_feature.attributes.stop_instance_id = stop.instance_id;
    new_feature.attributes.size = Math.max(stop.getAttr("size", false), 14);
    var rand_color = this.options.default_feature_colors[Math.floor(Math.random()*3)];
    new_feature.attributes.color = stop.getAttr("color", false) || rand_color;
        stop.attributes.color = stop.getAttr("color", false) || rand_color;
    new_feature.attributes.fcolor = stop.getAttr("color", false) || rand_color;
    stop.attributes.title = new_feature.attributes.title = stop.getAttr("title", false) || "" + stop.instance_id.split("-")[1];
    new_feature.attributes.label = stop.getAttr("title", false) || "";
    stop.attributes.description = new_feature.attributes.description = stop.getAttr("description", false) || "";
    new_feature.attributes.ref = stop;
    stcolor = new Sourcemap.Color();    
    if(new_feature.attributes.color) {
        stcolor = stcolor.fromHex(new_feature.attributes.color);
        stcolor.r -= 8; stcolor.g -= 8; stcolor.b -= 8;
        new_feature.attributes.scolor = stcolor.toString();
    } else {
        stcolor = stcolor.fromHex(rand_color);
        new_feature.attributes.scolor = stcolor.toString();
    }
    new_feature.attributes.swidth = 14;

    // save references to features
    this.mapped_features[stop.instance_id] = new_feature;
    this.stop_features[scid][stop.instance_id] = {"stop": new_feature};
   
    this.broadcast('map:stop_mapped', this, this.findSupplychain(scid), stop, new_feature);
    return new_feature;
}

Sourcemap.Map.prototype.eraseStop = function(scid, stid) {
    var f = this.stopFeature(scid, stid);
    if(f) {
        this.getStopLayer(scid).removeFeatures([f]);
    }
    return this;
}

Sourcemap.Map.prototype.stopFeature = function(scid, stid) {
    if(scid && !stid && (scid instanceof Sourcemap.Stop)) {
        stid = scid;
        scid = stid.supplychain_id;
        stid = stid.instance_id;
    }
    var stl = this.getStopLayer(scid);
    var f = false;
    if(stl) {
        for(var i=0; i<stl.features.length; i++) {
            var stlf = stl.features[i];
            if(stlf.attributes.stop_instance_id == stid) {
                f = stlf;
                break;
            }
        }
    }
    return f;
}

Sourcemap.Map.prototype.hopFeature = function(scid, hid) {
    if(scid && !hid && (scid instanceof Sourcemap.Hop)) {
        hid = scid;
        scid = hid.supplychain_id;
        hid = hid.instance_id;
    }
    var hl = this.getHopLayer(scid);
    var f = false;
    if(hl) {
        for(var i=0; i<hl.features.length; i++) {
            var hlf = hl.features[i];
            if(hlf.attributes.hop_instance_id == hid) {
                f = hlf;
                break;
            }
        }
    }
    return f;
}

// todo: removeStop
Sourcemap.Map.prototype.mapHop = function(hop, scid) {
    if(!(hop instanceof Sourcemap.Hop))
        throw new Error('Sourcemap.Hop required.');
    this.eraseHop(scid, hop.instance_id);
    if(this.options.hops_as_arcs || this.options.hops_as_bezier) {
        var sc = this.supplychains[scid];
        var wkt = new OpenLayers.Format.WKT();
        var from_pt = wkt.read(sc.findStop(hop.from_stop_id).geometry).geometry;
        var to_pt = wkt.read(sc.findStop(hop.to_stop_id).geometry).geometry;
    }
    if(this.options.hops_as_arcs) {
        var new_feature = new OpenLayers.Feature.Vector(this.makeGreatCircleRoute(from_pt, to_pt));
    } else if(this.options.hops_as_bezier) {
        var new_feature = new OpenLayers.Feature.Vector(this.makeBezierCurve(from_pt, to_pt));
    } else {
        var new_feature = (new OpenLayers.Format.WKT()).read(hop.geometry);
    }
    var new_arrow = false;
    var new_arrow2 = false; // for wrapped arcs
    var rand_color = this.options.default_feature_colors[Math.floor(Math.random()*3)]
    hop.attributes.color = hop.getAttr("color", false) || rand_color
    if(this.options.arrows_on_hops) {
        new_arrow = this.makeArrow(new_feature.geometry, {
            "color": hop.getAttr("color", rand_color),
            "size": 10, "supplychain_instance_id": scid,
            "hop_instance_id": hop.instance_id, "from_stop_id": hop.from_stop_id,
            "to_stop_id": hop.to_stop_id
            
        });
        var tmp = new_arrow;
        if(new_arrow instanceof Array) {
            new_arrow = tmp[0];
            new_arrow2 = tmp[1];
        }
    }

    new_feature.attributes = Sourcemap.deep_clone(hop.attributes);
    new_feature.attributes.supplychain_instance_id = scid;
    new_feature.attributes.hop_instance_id = hop.instance_id;
    new_feature.attributes.from_stop_id = hop.from_stop_id;
    new_feature.attributes.to_stop_id = hop.to_stop_id;
    new_feature.attributes.width = 3;
    new_feature.attributes.opacity = 0.6;
    new_feature.attributes.color = hop.getAttr("color", false) || rand_color;
    new_feature.attributes.ref = hop;
    this.broadcast('map:hop_mapped', this, this.findSupplychain(scid), hop, new_feature);
    // save references to features
    this.mapped_features[hop.local_id] = new_feature;
    if(!this.hop_features[scid][hop.from_stop_id]) this.hop_features[scid][hop.from_stop_id] = {};
    this.hop_features[scid][hop.from_stop_id][hop.to_stop_id] = {"hop": new_feature};
    if(new_arrow) {
        this.hop_features[scid][hop.from_stop_id][hop.to_stop_id].arrow = new_arrow;
        if(new_arrow2)
            this.hop_features[scid][hop.from_stop_id][hop.to_stop_id].arrow2 = new_arrow2;
    }
    if(this.prepareHopFeature instanceof Function) {
        this.prepareHopFeature.call(this, hop, new_feature, new_arrow);
    }
    this.getHopLayer(scid).addFeatures([new_feature]);
    if(new_arrow)
        this.getHopLayer(scid).addFeatures([new_arrow]);
    if(new_arrow2)
        this.getHopLayer(scid).addFeatures([new_arrow2]);
}

Sourcemap.Map.prototype.eraseHop = function(scid, hid) {
    var rm = [];
    var f = this.hopFeature(scid, hid);
    if(f) {
        rm.push(f);
        if(f.arrow)
            rm.push(f.arrow);
        if(f.arrow2)
            rm.push(f.arrow2);
    }
    if(rm.length) {
        this.getHopLayer(scid).removeFeatures(rm);
    }
    return this;
}

Sourcemap.Map.prototype.makeArrow = function(hop_geom, o) {
    if(!OpenLayers.Renderer.symbol.arrow)
        OpenLayers.Renderer.symbol.arrow = [-5, 5,  0,3,  5, 5,  0, -5,  -5, 5];
    
    var psrc = this.map.projection;
    var pdst = new OpenLayers.Projection('EPSG:4326');

    var fline = hop_geom.components[0];
    var lline = hop_geom.components[hop_geom.components.length-1];

    var from_pt = fline.components[0];
    var to_pt = null;
    var wrapped = false;
    if(hop_geom.components.length === 2) {
        // assume we've split the multilinestring's only element into
        // two arcs, one on each side of the map. use the endpoint of he first
        // segment as the location for the arrow.
        wrap_pt = hop_geom.components[0].components[hop_geom.components[0].components.length-1];
        wrap_pt2 = hop_geom.components[hop_geom.components.length-1].components[0];
        to_pt = lline.components[lline.components.length-1];
        wrapped = true;
    } else {
        to_pt = lline.components[lline.components.length-1];
    }


    var from = from_pt.clone().transform(psrc, pdst);
    var to = to_pt.clone().transform(psrc, pdst);
    var wrap = null;
    var wrap2 = null;
    if(wrapped) {
        wrap = wrap_pt.clone().transform(psrc, pdst);
        wrap2 = wrap_pt2.clone().transform(psrc, pdst);
    }

    var mid_pt = null;
    if(wrapped) {
        mid_pt = Sourcemap.great_circle_midpoint(from, wrap);
        mid_pt2 = Sourcemap.great_circle_midpoint(wrap2, to);
        angle = Sourcemap.great_circle_bearing(from, wrap);
    } else {
        mid_pt = Sourcemap.great_circle_midpoint(from, to);
        angle = Sourcemap.great_circle_bearing(mid_pt, to);
    }

    mid_pt = new OpenLayers.Geometry.Point(mid_pt.x, mid_pt.y);
    mid_pt = mid_pt.transform(pdst, psrc);

    var attrs = {"type": "arrow", "width": 0, "opacity":1.0, "angle": angle};
    var o = o || {};
    for(var k in o) attrs[k] = o[k];
    var a = new OpenLayers.Feature.Vector(mid_pt, attrs);
    var a2 = null;
    if(wrapped) {
        mid_pt2 = new OpenLayers.Geometry.Point(mid_pt2.x, mid_pt2.y);
        mid_pt2 = mid_pt2.transform(pdst, psrc);
        angle2 = Sourcemap.great_circle_bearing(wrap2, to);
        attrs.angle = angle2;
        var a2 = new OpenLayers.Feature.Vector(mid_pt2, attrs);
    }
    return a2 ? [a,a2] : a;
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

Sourcemap.Map.prototype.makeGreatCircleRoute = function(from, to) {
    var psrc = this.map.projection;
    var pdst = new OpenLayers.Projection('EPSG:4326');
    var from = from.transform(psrc, pdst);
    var to = to.transform(psrc, pdst);
    var rt = Sourcemap.great_circle_route({"x": from.x, "y": from.y}, {"x": to.x, "y": to.y}, 7);
    var rtpts = [];
    var lns = [];
    var buf = [];
    var mapext = this.map.getMaxExtent().clone().transform(this.map.projection, new OpenLayers.Projection('EPSG:4326'));
    var split_wayward_routes = true;
    if(split_wayward_routes) {
        var oobl = false;
        var oobr = false;
        for(var i=0; i<rt.length; i++) {
            var flipped = false;
            var newpt = new OpenLayers.Geometry.Point(rt[i].x, rt[i].y);
            if(!mapext.containsBounds(newpt)) {
                if(newpt.x > mapext.right) {
                    newpt.x = mapext.left + (newpt.x - mapext.right);
                    if(!oobl) flipped = true;
                    oobl = true;
                } else if(newpt.x < mapext.left){
                    newpt.x = mapext.right - (mapext.left - newpt.x);
                    if(!oobr) flipped = true;
                    oobr = true;
                }
                if(flipped && buf.length) {
                    lns.push(new OpenLayers.Geometry.LineString(buf));
                    buf = [];
                }
            } 
            buf.push(newpt);
        }
        if(buf.length) lns.push(new OpenLayers.Geometry.LineString(buf));
    } else {
        var rtpts = [];
        for(var i=0; i<rt.length; i++) {
            rtpts.push(new OpenLayers.Geometry.Point(rt[i].x, rt[i].y));
        }
        lns.push(new OpenLayers.Geometry.LineString(rtpts));
    }

    var rtgeo = new OpenLayers.Geometry.MultiLineString(lns);
    rtgeo = rtgeo.clone().transform(pdst, psrc);

    return rtgeo;
}

Sourcemap.Map.prototype.clearMap = function() {
    // clear map.
}

Sourcemap.Map.prototype.findSupplychain = function(scid) {
    if(scid instanceof Sourcemap.Supplychain)
        scid = scid.instance_id;
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
    var scid = supplychain.instance_id;
    if(this.findSupplychain(scid))
        throw new Error("Supplychain already attached to this map.");
    this.supplychains[scid] = supplychain;
    this.addHopLayer(scid).addStopLayer(scid);
    this.stop_features[scid] = {};
    this.hop_features[scid] = {};
    this.mapSupplychain(scid);
    this.broadcast('map:supplychain_added', this, supplychain);
    
    return this;
}

Sourcemap.Map.prototype.removeSupplychain = function(scid) {
    var sc = this.findSupplychain(scid);
    var removed = false;
    if(sc && sc.local_id) {
        var scid = sc.local_id;
        this.removeStopLayer(scid);
        this.removeHopLayer(scid);
        removed = this.supplychains[scid];
        delete this.supplychains[scid];
        this.broadcast('map:supplychain_removed', this, removed, scid);
    }
    return removed;
}

Sourcemap.Map.prototype.findFeaturesForStop = function(scid, stid) {
    var ftrs = false;
    if(this.stop_features[scid]) {
        var sc_st_ftrs = this.stop_features[scid];
        if(sc_st_ftrs[stid]) {
            ftrs = sc_st_ftrs[stid];
        }
    }
    return ftrs;
}

Sourcemap.Map.prototype.findFeaturesForHop = function(scid, from_stid, to_stid) {
    var ftrs = false;
    if(this.hop_features[scid]) {
        var sc_hop_ftrs = this.hop_features[scid];
        if(sc_hop_ftrs[from_stid]) {
            var from_st_ftrs = sc_hop_ftrs[from_stid];
            if(from_st_ftrs[to_stid]) {
                ftrs = from_st_ftrs[to_stid];
            }
        }
    }
    return ftrs;
}

Sourcemap.Map.prototype.getStopFeatures = function(scid) {
    var features = [];
    if(scid) {
        var stl = this.getStopLayer(scid);
        features = features.concat(stl.features);
    } else {
        for(var k in this.supplychains) {
            features = features.concat(this.getStopLayer(k).features);
        }
    }
    return features;
}

Sourcemap.Map.prototype.getHopFeatures = function(scid) {
    var features = [];
    if(scid) {
        var hl = this.getHopLayer(scid);
        features = features.concat(hl.features);
    } else {
        for(var k in this.supplychains) {
            features = features.concat(this.getHopLayer(k).features);
        }
    }
    return features;
}

Sourcemap.Map.prototype.redraw = function() {
    var ftrs = this.getStopFeatures();
    ftrs = ftrs.concat(this.getHopFeatures());
    for(var i=0; i<ftrs.length; i++)  {
        if(ftrs[i].layer)
            ftrs[i].layer.drawFeature(ftrs[i]);
    }
    return this;
}

Sourcemap.Map.prototype.reselect = function() {
    if(this.controls.select && this.last_selected) {
        var scid = this.last_selected.supplychain_instance_id;
        var stid = false;
        var hid = false;
        var f = null;
        if(this.last_selected.stop_instance_id) {
            stid = this.last_selected.stop_instance_id;
            f = this.stopFeature(scid, stid);
        } else {
            hid = this.last_selected.hop_instance_id;
            f = this.hopFeature(scid, hid);
        }
        this.controls.select.select(f);
        
    }
}

Sourcemap.Cluster = function(distance, threshold) {
    OpenLayers.Strategy.Cluster.prototype.initialize.apply(this, arguments);
    this.initialize.apply(this, arguments);    
}
Sourcemap.Cluster.prototype = new OpenLayers.Strategy.Cluster();

Sourcemap.Cluster.prototype.createCluster = function(feature) {
    var scid = feature.attributes.supplychain_instance_id;
    var center = feature.geometry.getBounds().getCenterLonLat();
    var cid = "cluster-"+feature.attributes.stop_instance_id;
    var cluster = new OpenLayers.Feature.Vector(
        new OpenLayers.Geometry.Point(center.lon, center.lat), {
            "count": 1, 
            "size":feature.attributes.size*2,
            "color":feature.attributes.color,
            "swidth":14,
            "scolor":feature.attributes.color,
            "fcolor":feature.attributes.color,
            "label": feature.attributes.title+" (and -x- more)",
            "yoffset":-45, 
            "supplychain_instance_id":scid,
            "cluster_instance_id":cid
        }
    );
    cluster.renderIntent = "cluster";

    cluster.cluster = [feature];    
    return cluster;
}
Sourcemap.Cluster.prototype.addToCluster = function(cluster, feature) {
    cluster.cluster.push(feature);
    cluster.attributes.count += 1;
    //cluster.attributes.size = cluster.attributes.swidth + (2.25 * cluster.attributes.count);
    //cluster.attributes.yoffset = (-21 * (cluster.attributes.size/cluster.attributes.swidth)) - cluster.attributes.swidth/2;
}