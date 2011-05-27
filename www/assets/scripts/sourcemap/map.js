Sourcemap.Map = function(element_id, o) {
    this.broadcast('map:instantiated', this);
    this.layers = {};
    this.controls = {};
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
    "zoom_control": false,
    "ol_layer_switcher": false, "tileswitcher": false,
    "google_tiles": true,
    "cloudmade_tiles": true, "popups": true,
    "stop_popups": true, "hop_popups": true,
    "arrow_popups": true, "popup_width": 200,
    "popup_height": 100, "animation_enabled":false,
    "draw_hops": true, "hops_as_arcs": true,
    "hops_as_bezier": false, "arrows_on_hops": true,
    "stop_style": {
        "default": {
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": "${strokeWidth}",
            "strokeColor": "${strokeColor}",
            "fontColor": "#eee",
            "fontSize": "1.5em",
            "fontFamily": "Georgia, serif",
            "fillOpacity": 0.7,
            "label": "${label}",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": -4, // fixme: this is bad
        },
        "select": {
            "fillColor": "${color}",
            "fillOpacity": 1.0
        },
        "hascontent": {
            "strokeWidth": 1,
            "strokeColor": "#fff",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": -4,
            "fontSize": "em",
            "fillColor": "${color}"
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
            "strokeOpacity": 0.6,
            "rotation": "${angle}"
        },
        "select": {
            "strokeColor": "#050",
            "fillColor": "#050"
        }
    }, "prep_stop": null, "prep_hop": null,
    "prep_popup": null
}

Sourcemap.Map.prototype.init = function() {
    this.initMap().initBaseLayer().initLayers().initControls();
    var p = new OpenLayers.LonLat(-122.8764, 42.3263);
    p.transform(new OpenLayers.Projection("EPSG:4326"), this.map.getProjectionObject());
    this.map.setCenter(p);
    this.map.zoomTo(2);
    this.supplychains = {};
    this.mapped_features = {};
    this.stop_features = {}; // dicts of stop ftrs keyed by parent supplychain
    this.hop_features = {}; // dicts of hop ftrs keyed by parent supplychain
    this.prepareStopFeature = this.options.prep_stop ? this.options.prep_stop : false;
    this.prepareHopFeature = this.options.prep_hop ? this.options.prep_hop : false;
    this.preparePopup = this.options.prep_popup ? this.options.prep_popup : false;
    this.broadcast('map:initialized', this);
    return this;
}
Sourcemap.Map.prototype.initMap = function() {
    var controls = [
            new OpenLayers.Control.Navigation({"handleRightClicks": true}),
            new OpenLayers.Control.ArgParser(),
            new OpenLayers.Control.Attribution()
    ];
    if(this.options.zoom_control) 
        controls.push(new OpenLayers.Control.ZoomPanel());
    var options = {
        "theme": "assets/scripts/openlayers/theme/sourcemap/style.css",
        "projection": new OpenLayers.Projection("EPSG:900913"),
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
        //"minZoomLevel": 2,
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
        "styleId": 4993,
        "wrapDateLine": this.options.animation_enabled
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
                        if(this.options.popups) {
                            this.showPopup(feature);
                        }
                        this.broadcast('map:feature_selected', this, feature); 
                    }, 
                    this
                ),
                "onUnselect": OpenLayers.Function.bind(
                    function(feature) {
                        if(this.options.popups) {
                            this.hidePopup(feature);
                        }
                        this.broadcast('map:feature_unselected', this, feature); 
                    },
                    this
                ),
                "clickOut": true
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

Sourcemap.Map.prototype.initTileSwitcher = function() {
    this.tileswitcher_div = $('<div id="tileswitcher" class="'+Sourcemap.embed_params.basetileset+'">'+
        '<div id="current-tile">'+Sourcemap.embed_params.basetileset+'</div><ul id="available-tiles">'+
        '<li id="stylized"></li><li id="terrain"></li><li id="satellite">'+
        '</li></ul></div>'
    );
    $(this.map.div).append(this.tileswitcher_div);
    $("#tileswitcher #available-tiles li").click($.proxy(function(event) {
        var newtile = $(event.currentTarget).attr("id");
        $("#tileswitcher").attr("class",  newtile);
        $("#tileswitcher #current-tile").text(newtile);

        this.map.setBaseLayer( this.map.getLayersByName(newtile).pop() );
    }, this));

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
    var slayer = new OpenLayers.Layer.Vector(
        "Stops - "+sc.getLabel(), {
            "sphericalMercator": true,
            "styleMap": new OpenLayers.StyleMap(this.options.stop_style)
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
    if(supplychain.stops.length)
        this.map.zoomToExtent(this.getStopLayer(scid).getDataExtent());
    this.broadcast('map:supplychain_mapped', this, supplychain);
}

Sourcemap.Map.prototype.mapStop = function(stop, scid) {
    if(!(stop instanceof Sourcemap.Stop))
        throw new Error('Sourcemap.Stop required.');
    var new_feature = (new OpenLayers.Format.WKT()).read(stop.geometry);
    new_feature.attributes.supplychain_instance_id = scid;
    new_feature.attributes.local_stop_id = stop.local_stop_id; // todo: clarify this
    new_feature.attributes.stop_instance_id = stop.instance_id;
    new_feature.attributes.size = Math.max(stop.getAttr("size", false), 11);
    new_feature.attributes.color = stop.getAttr("color", false) || '#60CB59';
    new_feature.attributes.label = stop.getAttr("label", false) || '';
    stcolor = new Sourcemap.Color();
    stcolor = stcolor.fromHex(new_feature.attributes.color);
    stcolor.r -= 8; stcolor.g -= 8; stcolor.b -= 8;
    new_feature.attributes.strokeColor = stcolor.toString();
    new_feature.attributes.strokeWidth = 2;
    var new_popup = false;
    if(this.options.popups && this.options.stop_popups) {
        var puid = stop.instance_id+'-popup';
        var ll = new OpenLayers.LonLat(new_feature.geometry.x, new_feature.geometry.y);
        var sz = new OpenLayers.Size(this.options.popup_width, this.options.popup_height);
        var sc = this.findSupplychain(scid);
        var cb = function() { 
            this.sourcemap.controls.select.unselectAll(); 
        }
        var new_popup = new Sourcemap.Popup(puid, ll, sz, stop.getLabel(), true, cb);
        new_popup.feature = new_feature;
        new_popup.sourcemap = this;
        new_popup.hide();
    }
    if(this.prepareStopFeature instanceof Function) {
        this.prepareStopFeature.call(this, stop, new_feature);
    }
    // save references to features
    this.mapped_features[stop.instance_id] = new_feature;
    this.stop_features[scid][stop.instance_id] = {"stop": new_feature};
    if(new_popup) {
        if(this.preparePopup instanceof Function) this.preparePopup.apply(this, [stop, new_feature, new_popup]);
        this.map.addPopup(new_popup);
        this.stop_features[scid][stop.instance_id].popup = new_popup;
    }
    this.getStopLayer(scid).addFeatures([new_feature]);
    this.broadcast('map:stop_mapped', this, this.findSupplychain(scid), stop, new_feature);
}

// todo: removeStop

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
    var new_arrow = false;
    if(this.options.arrows_on_hops) {
        new_arrow = this.makeArrow(new_feature.geometry, {
            "color": "#072", "size": 7, "supplychain_instance_id": scid,
            "hop_instance_id": hop.instance_id, "from_stop_id": hop.from_stop_id,
            "to_stop_id": hop.to_stop_id
            
        });
    }
    var new_popup = false;
    if(this.options.popups && this.options.hop_popups && new_arrow) {
        var puid = hop.instance_id+'-popup';
        var ll = new OpenLayers.LonLat(new_arrow.geometry.x, new_arrow.geometry.y);
        var sz = new OpenLayers.Size(this.options.popup_width, this.options.popup_height);
        var sc = this.findSupplychain(scid);
        var fromst = sc.findStop(hop.from_stop_id);
        var tost = sc.findStop(hop.to_stop_id);
        var cb = function() { 
            this.sourcemap.controls.select.unselectAll(); 
        }
        var new_popup = new Sourcemap.Popup(puid, ll, sz, fromst.getLabel()+" to "+tost.getLabel(), true, cb);
        new_popup.sourcemap = this;
        new_popup.hide();
    }
    new_feature.attributes.supplychain_instance_id = scid;
    new_feature.attributes.hop_instance_id = hop.instance_id;
    new_feature.attributes.from_stop_id = hop.from_stop_id;
    new_feature.attributes.to_stop_id = hop.to_stop_id;
    new_feature.attributes.width = 4;
    new_feature.attributes.color = '#072';
    this.broadcast('map:hop_mapped', this, this.findSupplychain(scid), hop, new_feature);
    // save references to features
    this.mapped_features[hop.local_id] = new_feature;
    if(!this.hop_features[scid][hop.from_stop_id]) this.hop_features[scid][hop.from_stop_id] = {};
    this.hop_features[scid][hop.from_stop_id][hop.to_stop_id] = {"hop": new_feature};
    if(new_arrow) {
        this.hop_features[scid][hop.from_stop_id][hop.to_stop_id].arrow = new_arrow;
        if(new_popup) {
            if(this.preparePopup instanceof Function) this.preparePopup.call(this, hop, new_feature, new_popup);
            this.hop_features[scid][hop.from_stop_id][hop.to_stop_id].popup = new_popup;
            this.map.addPopup(new_popup);
        }
    }
    if(this.prepareHopFeature instanceof Function) {
        this.prepareHopFeature.call(this, hop, new_feature, new_arrow);
    }
    this.getHopLayer(scid).addFeatures([new_feature]);
    if(new_arrow)
        this.getHopLayer(scid).addFeatures([new_arrow]);
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
    var resolution = 20;
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

Sourcemap.Map.prototype.showPopup = function(feature) {
    var attrs = feature.attributes;
    if(attrs.supplychain_instance_id && attrs.stop_instance_id) {
        ftrs = this.findFeaturesForStop(attrs.supplychain_instance_id, attrs.stop_instance_id);
    } else if(attrs.supplychain_instance_id && attrs.hop_instance_id) {
        ftrs = this.findFeaturesForHop(attrs.supplychain_instance_id, attrs.from_stop_id, attrs.to_stop_id);
    }
    if(ftrs && ftrs.popup)
        ftrs.popup.show();
}

Sourcemap.Map.prototype.hidePopup = function(feature) {
    var attrs = feature.attributes;
    if(attrs.supplychain_instance_id && attrs.stop_instance_id) {
        ftrs = this.findFeaturesForStop(attrs.supplychain_instance_id, attrs.stop_instance_id);
    } else if(attrs.supplychain_instance_id && attrs.hop_instance_id) {
        ftrs = this.findFeaturesForHop(attrs.supplychain_instance_id, attrs.from_stop_id, attrs.to_stop_id);
    }
    if(ftrs && ftrs.popup)
        ftrs.popup.hide();

}

Sourcemap.Popup = function(id, ll, csz, chtm, clsbx, clscb) {
    OpenLayers.Popup.prototype.initialize.apply(this, arguments);
    this.initialize.apply(this, arguments);
}

Sourcemap.Popup.prototype = new OpenLayers.Popup();
Sourcemap.Popup.prototype.ANCHOR_HT = 16;

Sourcemap.Popup.prototype.initialize = function() {
    this.closeDiv = false;
    $(this.div).css({"background-color": 'none', "visibility": "none", height:"auto"});
    this.bottom_div = $('<div class="sourcemap-popup-bottom"></div>');
    $(this.bottom_div).css({
        "background-image": "url(assets/images/popup-anchor-16x16.png)",
        "background-position": "center", "background-repeat": "no-repeat",
        "height": this.ANCHOR_HT+"px", "width": "100%", "background-color": "none"
    });
    $(this.bottom_div).parent('.olPopup').css("height", "auto");
    $(this.div).append(this.bottom_div);
    this.fade_in = this.fade_in === undefined ? "fast" : this.fade_in;
}

Sourcemap.Popup.prototype.setBackgroundColor = function(color) {
    $(this.contentDiv).css("background-color", "#ffffff");
    return this;
}

Sourcemap.Popup.prototype.setOpacity = function(opacity) {
    $(this.div).css("opacity", opacity);
    return this;
}

Sourcemap.Popup.prototype.setBorder = function(border) {
    $(this.div).css("border", border);
    return this;
}

Sourcemap.Popup.prototype.setSize = function(content_sz) {
    //content_sz.h += this.ANCHOR_HT;
    OpenLayers.Popup.prototype.setSize.apply(this, arguments);
    $(this.div).css("height", (this.size.h + this.ANCHOR_HT) + "px");
}

Sourcemap.Popup.prototype.updateSize = function() {
    OpenLayers.Popup.prototype.updateSize.apply(this, arguments);
}

Sourcemap.Popup.prototype.moveTo = function(px) {
    if(px != null && this.div) {
        $(this.div).css("left", px.x - ($(this.div).width() / 2));
        $(this.div).css("top", px.y - ($(this.div).height()));
    }
}

Sourcemap.Popup.prototype.hide = function() {
    OpenLayers.Popup.prototype.hide.apply(this, arguments);
    $(this.div).hide();
    $(this.div).find('*').hide();
}

Sourcemap.Popup.prototype.show = function() {
    //OpenLayers.Popup.prototype.show.apply(this, arguments);
    $(this.div).fadeIn(this.fade_in, $.proxy(function() {
        $(this.div).find('*').fadeIn(this.fade_in);
    }, this));
}

// Similar to addCloseBox, but returns the close box div 
// rather than appending it.  Useful for working with templates.
/*
Sourcemap.Popup.prototype.getCloseBox = function() {

        this.closeDiv = OpenLayers.Util.createDiv(
            this.id + "_close", null, new OpenLayers.Size(17, 17)
        );
        this.closeDiv.className = "olPopupCloseBox";

        var contentDivPadding = 0; 
        this.getContentDivPadding();
        
        var closePopup = function(e) {
            this.hide();
            this.sourcemap.controls.select.unselectAll(); 
            OpenLayers.Event.stop(e);
        };
        
        OpenLayers.Event.observe(this.closeDiv, "click",
            OpenLayers.Function.bindAsEventListener(closePopup, this));
        
        return this.closeDiv;

}

Sourcemap.Popup.prototype.addCloseBox = function(callback) {
    this.closeDiv = OpenLayers.Util.createDiv(
        this.id + "_close", null, new OpenLayers.Size(17, 17)
    );
    this.closeDiv.className = "olPopupCloseBox";

    // use the content div's css padding to determine if we should
    //  padd the close div
    var contentDivPadding = 0; 
    this.getContentDivPadding();

    this.closeDiv.style.right = contentDivPadding.right + "px";
    this.closeDiv.style.top = contentDivPadding.top + "px";

    var closePopup = callback || function(e) {
        this.hide();
        OpenLayers.Event.stop(e);
    };
}
*/

