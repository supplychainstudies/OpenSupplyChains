/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

Sourcemap.Map = function(element_id, o) {
    this.broadcast('map:instantiated', this);
    this.layers = {};
    this.controls = {};
    this.dock_controls = {};
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
     "zoom_control": true, "default_stop_size": 12,
    "min_stop_size": 6, "max_stop_size": 32, "tileswitcher": true, 
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
            "strokeOpacity": 0.8,
            "fontColor": "${fcolor}",
            "fontWeight": "bold",           
            "cursor":"pointer",
            "fontSize": "${fsize}",
            "fontFamily": "Helvetica, sans-serif",
            "fillOpacity": 1,
            "label": "${label}",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": "${yoffset}", 
        },
        "select": {
            "fillColor": "#ffffff",
            "fillOpacity": 1.0
        },
        "cluster": {
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeColor": "${scolor}",
            "strokeOpacity": 0.8,
            "strokeWidth": "${swidth}",
            "fontColor": "${fcolor}",
            "fontWeight": "bold",           
            "cursor":"pointer",
            "fontSize": "${fsize}",
            "fontFamily": "Helvetica, sans-serif",
            "fillOpacity": 1,
            "label": "${label}",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": "${yoffset}" // fixme: this is bad
        },
        "hascontent": {
            "strokeWidth": "${swidth}",
            "strokeColor": "${scolor}",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": "${yoffset}",
            "fontSize": "${fsize}",
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
            "fontColor": "${fcolor}",
            "fillOpacity": 1,
            "strokeOpacity": "${opacity}",
            "rotation": "${angle}"
        },
    	"arrow": {
    		"graphicName": "${type}",
            "pointRadius": "${size}",
            "fillColor": "${color}",
            "strokeWidth": "${width}",
            "strokeColor": "${color}",
            "fillOpacity": 1,
            "strokeOpacity": "${opacity}",
            "rotation": "${angle}",
            "cursor":"pointer",
            "fontColor": "${fcolor}",
            "fontWeight": "bold",           
            "fontSize": "${fsize}",
            "fontFamily": "Helvetica, sans-serif",
            "label": "${label}",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": "${yoffset}",
    	},
        "select": {
            "fillColor": "#ffffff",
            "fillOpacity": 1.0,
            "fontColor": "${fcolor}",
            "fontWeight": "bold",           
            "fontSize": "${fsize}",
            "fontFamily": "Helvetica, sans-serif",
            "label": "${label}",
            "labelAlign": "cm",
            "labelXOffset": 0,
            "labelYOffset": "${yoffset}",
        }
	}, "prep_stop": null, "prep_hop": null
}

Sourcemap.Map.prototype.init = function() {
    this.initMap().initBaseLayer().initLayers()
        .initControls().initDock().initEvents();
    this.supplychains = {};
    this.mapped_features = {};
    this.stop_features = {}; // dicts of stop ftrs keyed by parent supplychain
    this.hop_features = {}; // dicts of hop ftrs keyed by parent supplychain
    this.cluster_features = {}; // dicts of cluster ftrs keyed by parent supplychain    
    this.cluster = null;
    this.prepareStopFeature = this.options.prep_stop ? this.options.prep_stop : false;
    this.prepareHopFeature = this.options.prep_hop ? this.options.prep_hop : false;

    //this.broadcast('map:initialized', this);
    return this;
}
Sourcemap.Map.prototype.initMap = function() {
    var controls = [
            new OpenLayers.Control.Navigation({"handleRightClicks": true}),
            new OpenLayers.Control.Attribution()
    ];
    var options = {
        "theme": "assets/scripts/libs/openlayers/theme/sourcemap/style.css",
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
            'sphericalMercator': true,
            "type": google.maps.MapTypeId.TERRAIN,
            "animationEnabled": false,
            "minZoomLevel": 1, "maxZoomLevel": 17,
            //"wrapDateLine":false,
    }));
    this.map.addLayer(new OpenLayers.Layer.Google(
        "satellite", {
            'sphericalMercator': true,
            "type": google.maps.MapTypeId.SATELLITE,
            "animationEnabled": false,
            "minZoomLevel": 1, "maxZoomLevel": 17,
            //"wrapDateLine":false,
    }));
    this.map.addLayer(new OpenLayers.Layer.CloudMade(
        "cloudmade", {
        "key": "BC9A493B41014CAABB98F0471D759707",
        "styleId": 44909,
        "maxResolution":39135.758475,
        "minZoomLevel": 1, "maxZoomLevel": 12
    }));
    
    this.broadcast('map:base_layer_initialized', this);
    return this;
}

Sourcemap.Map.prototype.setBaseLayer = function(nm) {
    this.map.setBaseLayer(this.map.getLayersByName(nm).pop());
    this.map.minZoomLevel = this.map.baseLayer.minZoomLevel;
    this.map.maxZoomLevel = this.map.baseLayer.maxZoomLevel;

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
    this.dock_element = $('<div id="sourcemap-dock"></div>');
    $(this.map.div).css("position", "relative").append(
        this.dock_element.append(this.dock_outerwrap.append(this.dock_content))
    );
    this.dockAdd('zoomout', {
        "title": 'Zoom Out',
        "panel": 'zoom',
        "callbacks": {
            "click": function() {
                this.map.zoomOut();
            }
        }
    });
    this.dockAdd('zoomin', {
        "title": 'Zoom In',
        "panel": 'zoom',
        "callbacks": {
            "click": function() {
                this.map.zoomIn();
            }
        }
    });
    this.dockAdd('fitscreen', {
        "title": 'Fit Screen',
        "panel": 'zoom',
        "callbacks": {
            "click": function() {
                this.zoomToExtent(this.getFeaturesExtent(), false);
            }
        }
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
    if(o.panel){
        // check to see if panel exists already.  if not, create it
        if ($('#sourcemap-dock').find("." + o.panel).length){
            $('#sourcemap-dock').find("." + o.panel).append(cel); 
        }
        else{
            var panel = $('<div class="panel ' + o.panel + '"></div>');
            panel.append(cel); 
            $(this.dock_content).append(panel);
        }
    }
    else{
    }
    if(callbacks.click) {
        $(cel).click($.proxy(callbacks.click, this));
    }
    if(o.toggle) {
        $(cel).addClass("toggle");
        // TODO: callback arg here...
    }
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
    if(cel) {$(cel).addClass("active");}
    return this;
}

Sourcemap.Map.prototype.dockToggleInactive = function(nm) {
    var cel = $(this.dock_content).find(".control."+nm);
    if(cel) {$(cel).removeClass("active");}
    return this;
}

Sourcemap.Map.prototype.dockControlEl = function(nm) {
    return $(this.dock_content).find('.control.'+nm.replace(/\s+/, '-'));
}

Sourcemap.Map.prototype.dockRemove = function(nm) {
    if(this.dock_controls[nm]) delete this.dock_controls[nm];
    if(this.dockControlEl(nm)) this.dockControlEl(nm).remove();
}

Sourcemap.Map.prototype.initEvents = function() {
    this.map.events.register("movestart", this, function() {
        var s = this.getSelected();
        s = s.length ? s[0] : false;
        if(s) {
            if(s.cluster_instance_id) {
                // pass
            } else {
                this._sel_before_zoom = s;
            }
        }
    });
    // zoom evts
    this.map.events.register("zoomend", this, function() {
        var s = this.getSelected();
        s = s.length ? s[0] : false;
        if(!s) s = this._sel_before_zoom;
        if(s) {
            if(s.cluster) {
                Sourcemap.broadcast("map:feature_unselected", this, s);
            } else {
                if(s.layer)
                    this.controls.select.select(s);
                else
                    Sourcemap.broadcast("map:feature_unselected", this, s);
            }
            this.map.setCenter(s.geometry.getBounds().getCenterLonLat());
        }
        this._sel_before_zoom = null;
    });
    return this;
}

Sourcemap.Map.prototype.initControls = function() {    
    var layers = [];
    for(var k in this.layers) layers.push(this.layers[k]);
    if(layers.length) {
        if(this.options.tileswitcher) {
    		// TODO: still need basic tile switcher
            //this.initTileSwitcher();
        }
        this.addControl('select', 
            new OpenLayers.Control.SelectFeature(layers, {
                "geometryTypes": ["OpenLayers.Geometry.Point", "OpenLayers.Geometry.MultiLineString"],
                "onSelect": OpenLayers.Function.bind(
                    function(feature) {
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
                "toggle": true
            })
        );

        // wrap select control select method to look for features
        // after map redraw...

        // wrap clickoutFeature
        var cof = this.controls.select.clickoutFeature;
        this.controls.select.callbacks.clickout = $.proxy(function(f) {
            $.proxy(cof, this.controls.select)(f);
        }, this);

        this.controls.select.select = $.proxy(function(f) {
            var c = this.controls.select;
            if(!f.layer && f.attributes.ref) {
                f = this.refFeature(f.attributes.ref);
            } else if(!f.layer && f.cluster) {
                var cl = f.cluster.slice(0);
                var stops = [];
                for(var i=0; i<cl.length; i++) stops.push(cl[i].attributes.ref.instance_id);
                f = this.findCluster(stops);
                if(f && !f.layer) {
                    f.layer = this.getStopLayer(f.cluster[0].attributes.supplychain_instance_id);
                }
            }
            if(!f.layer){
                f.layer = this.getStopLayer(f.attributes.supplychain_instance_id);
            }
            if(c.handlers.feature.feature && !c.handlers.feature.feature.layer) {
                if(c.handlers.feature.feature.attributes.ref) {
                    c.handlers.feature.feature = this.refFeature(c.handlers.feature.feature.attributes.ref);
                } else if(c.handlers.feature.feature.cluster) {
                    var cl = c.handlers.feature.feature.cluster.slice(0);
                    var stops = [];
                    for(var i=0; i<cl.length; i++) stops.push(cl[i].attributes.ref.instance_id);
                    c.handlers.feature.feature = this.findCluster(stops);
                    var hf = c.handlers.feature.feature;
                    if(hf && !hf.layer) {
                        c.handlers.feature.feature.layer = this.getStopLayer(hf.cluster[0].attributes.supplychain_instance_id);
                    }
                }
            }
            if(c.handlers.feature.lastFeature && !c.handlers.feature.lastFeature.layer) {
                if(c.handlers.feature.lastFeature.attributes.ref) {
                    c.handlers.feature.lastFeature = this.refFeature(c.handlers.feature.lastFeature.attributes.ref);
                } else if(c.handlers.feature.lastFeature.cluster) {
                    var cl = c.handlers.feature.lastFeature.cluster.slice(0);
                    var stops = [];
                    for(var i=0; i<cl.length; i++) stops.push(cl[i].attributes.ref.instance_id);
                    c.handlers.feature.lastFeature = this.findCluster(stops);
                    var hlf = c.handlers.feature.lastFeature;
                    if(hlf && !hlf.layer) {
                        c.handlers.feature.lastFeature.layer = this.getStopLayer(hlf.cluster[0].attributes.supplychain_instance_id);
                    }
                }
            }
            if(!f) return;
            OpenLayers.Control.SelectFeature.prototype.select.call(c, f);
        }, this);

        $(document).bind(['map:layer_added', 'map:layer_removed'], function(e, map, label, layer) {
            var layers = [];
            for(var k in map.layers) layers.push(map.layers[k]);
            map.controls.select.setLayer(layers);
        });
        this.controls.select.activate();
    }
    $(document).one('map:layer_added', function(e, map, label, layer) {
        if(!map.controls.select) { map.initControls(); }
    });

    this.broadcast('map:controls_initialized', this, ['select']);
    return this;
}

Sourcemap.Map.prototype.updateControls = function() {
    var layers = [];
    for(var k in this.layers) layers.push(this.layers[k]);
    if(this.controls.select) { this.controls.select.setLayer(layers); }
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
    this.cluster = new Sourcemap.Cluster({distance: this.options.default_stop_size, threshold: 2, map: this});
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

    // build graph and assign (max) tiers to stops.
    var g = new Sourcemap.Supplychain.Graph2(supplychain);
    var stids = g.nids.slice(0);
    var tiers = {};
    for(var i=0; i<stids.length; i++) {
        tiers[stids[i]] = 0;
    }
    var max_plen = 0;
    for(var i=0; i<g.paths.length; i++) {
        var p = g.paths[i];
        max_plen = p.length > max_plen ? p.length : max_plen;
        for(var j=0; j<p.length; j++) {
            if(j > tiers[p[j]]) tiers[p[j]] = j;
        }
    }
    // build palette
    var dfc = this.options.default_feature_colors.slice(0);
    for(var i=0; i<dfc.length; i++) {
        dfc[i] = (new Sourcemap.Color()).fromHex(dfc[i]);
    }
    var palette = Sourcemap.Color.graduate(dfc, max_plen || 1);
    
    if(this.getStopLayer(scid)) this.getStopLayer(scid).removeAllFeatures();
    if(this.getHopLayer(scid)) this.getHopLayer(scid).removeAllFeatures();
    var featureList = [];
    
    for(var i=0; i<supplychain.stops.length; i++) {
        var st = supplychain.stops[i];
        var new_ftr = this.mapStop(st, scid);
        var scolor = st.getAttr("color", palette[tiers[st.instance_id]].toString());
        new_ftr.attributes.tier = tiers[st.instance_id];
        new_ftr.attributes.color = scolor;
        new_ftr.attributes.scolor = scolor;
        new_ftr.attributes.fcolor = scolor;
        featureList.push(new_ftr);
    }
    this.getStopLayer(scid).addFeatures(featureList);
    this.getStopLayer(scid);
    if(this.options.draw_hops) {
        for(var i=0; i<supplychain.hops.length; i++) {
            var h = supplychain.hops[i];
            var fc = palette[tiers[h.from_stop_id]];
            var tc = palette[tiers[h.to_stop_id]];
            //var fc_color = supplychain.findStop(h.from_stop_id).attributes.color ? supplychain.findStop(h.from_stop_id).attributes.color : null;
            //var tc_color = supplychain.findStop(h.to_stop_id).attributes.color ? supplychain.findStop(h.to_stop_id).attributes.color : null;
            // problem occur when user define custom color in string ex: red
            //var fc = fc_color ? (new Sourcemap.Color()).fromHex(fc_color) : palette[tiers[h.from_stop_id]];
            //var tc = tc_color ? (new Sourcemap.Color()).fromHex(tc_color) : palette[tiers[h.to_stop_id]];
            var new_ftr = this.mapHop(supplychain.hops[i], scid);
            var hc = h.getAttr("color", fc.midpoint(tc).toString());
            new_ftr.hop.attributes.color = hc;
            this.getHopLayer(scid).addFeatures([new_ftr.hop]);
            if(new_ftr.arrow) {
                new_ftr.arrow.attributes.color = hc;
                this.getHopLayer(scid).addFeatures([new_ftr.arrow]);
            }
            if(new_ftr.arrow2) {
                new_ftr.arrow2.attributes.color = hc;
                this.getHopLayer(scid).addFeatures([new_ftr.arrow2]);
            }
        }
    }

    this.broadcast('map:supplychain_mapped', this, supplychain);
}

Sourcemap.Map.prototype.mapStop = function(stop, scid) {
    if(!(stop instanceof Sourcemap.Stop))
        throw new Error('Sourcemap.Stop required.');
    this.eraseStop(scid, stop.instance_id);
    var new_feature = (new OpenLayers.Format.WKT()).read(stop.geometry);
    // copy attributes for starters.
    var fsize = 12;
    new_feature.attributes = Sourcemap.deep_clone(stop.attributes);
    new_feature.attributes.supplychain_instance_id = scid;
    new_feature.attributes.local_stop_id = stop.local_stop_id; // TODO: clarify this
    new_feature.attributes.stop_instance_id = stop.instance_id;
    var sz = stop.getAttr("size", this.options.default_stop_size);
    var maxsz = this.options.max_stop_size;
    var minsz = this.options.min_stop_size;
    new_feature.attributes.size = Math.min(Math.max(sz, minsz), maxsz);
    new_feature.attributes.fsize = fsize + "px";
    new_feature.attributes.yoffset = -1*(Math.min(Math.max(sz, minsz), maxsz)+fsize);
  
    var rand_color = this.options.default_feature_colors[0];
    new_feature.attributes.color = stop.getAttr("color", rand_color);
    new_feature.attributes.fcolor = stop.getAttr("color", rand_color);
    stop.attributes.title = new_feature.attributes.title = stop.getAttr("title", false);
    if(!stop.attributes.title) {
        var idx = parseInt(stop.instance_id.split("-")[1])-1;
        var ct = Math.floor(idx/26)+1;
        var c = String.fromCharCode("A".charCodeAt(0)+(idx%26));
        var l = "";
        while(l.length < ct) l += c;
        stop.attributes.title = l;
        new_feature.attributes.title = l;
    }
    var slabel = stop.getAttr("title", false) || "";
    //slabel = slabel.length > 24 ? slabel.substring(0,24)+"..." : slabel;
    slabel = Sourcemap.ttrunc(slabel, 24);
    new_feature.attributes.label = slabel;
    stop.attributes.description = new_feature.attributes.description = stop.getAttr("description", false) || "";
    new_feature.attributes.ref = stop;
    stcolor = new Sourcemap.Color();    
    if(new_feature.attributes.color) {
        try {
            stcolor = stcolor.fromHex(new_feature.attributes.color);
        } catch(e) {
            stcolor = stcolor.fromHex(rand_color);
        }
        stcolor.r -= 8; stcolor.g -= 8; stcolor.b -= 8;
        new_feature.attributes.scolor = stcolor.toString();
    } else {
        stcolor = stcolor.fromHex(rand_color);
        new_feature.attributes.scolor = stcolor.toString();
    }
    new_feature.attributes.swidth = 4;
    
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

Sourcemap.Map.prototype.refFeature = function(ref) {
    var f = false;
    if(ref instanceof Sourcemap.Stop) {
        f = this.stopFeature(ref);
    } else if(ref instanceof Sourcemap.Hop) {
        f = this.hopFeature(ref);

    }
    return f;
}

Sourcemap.Map.prototype.stopFeature = function(scid, stid) {
    if(scid && !stid && (scid instanceof Sourcemap.Stop)) {
        var st = scid;
        scid = st.supplychain_id;
        stid = st.instance_id;
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
    if(!f) {
        if(this.stop_features && this.stop_features[scid]) {
            if(this.stop_features[scid][stid]) {
                f = this.stop_features[scid][stid].stop;
            }
        }
    }
    return f;
}

Sourcemap.Map.prototype.hopFeature = function(scid, hid, comp) {
    if(scid && !hid && (scid instanceof Sourcemap.Hop)) {
        hid = scid;
        scid = hid.supplychain_id;
        hid = hid.instance_id;
    }
    var comp = comp || "hop";
    var hl = this.getHopLayer(scid);
    var f = false;
    if(hl) {
        for(var i=0; i<hl.features.length; i++) {
            var hlf = hl.features[i];
            if(hlf.attributes.hop_component == comp 
                && hlf.attributes.hop_instance_id == hid) {
                f = hlf;
                break;
            }
        }
    }
    return f;
}

Sourcemap.Map.prototype.findCluster = function(stop_ids) {
    var make_id = function(s) {
        var ss = [];
        if(s.length && s[0] instanceof Sourcemap.Stop) {
            ss = [];
            for(i=0; i<s.length; i++)
                ss.push(s.instance_id);
            ss = ss.sort();
        } else {
            ss = s.sort();
        }
        var id = ss.join(',');
        return id;
    }
    var targetid = make_id(stop_ids);
    for(var sc in this.cluster_features) {
        var sccls = this.cluster_features[sc];
        for(var i=0; i<sccls.length; i++) {
            var cl = sccls[i].cluster;
            if(cl.length <= 1) continue;
            var st = [];
            for(var k in cl) {
                st.push(cl[k].attributes.ref.instance_id);
            }
            var cid = st.sort().join(',');
            if(cid == targetid) {
                return sccls[i];
            }
        }
    }
    return false;
}

// TODO: removeStop
Sourcemap.Map.prototype.mapHop = function(hop, scid) {
    if(!(hop instanceof Sourcemap.Hop))
        throw new Error('Sourcemap.Hop required.');
    this.eraseHop(scid, hop.instance_id);
    if(this.options.hops_as_arcs || this.options.hops_as_bezier) {
        var sc = this.supplychains[scid];
        var wkt = new OpenLayers.Format.WKT();
    	var from_stop = sc.findStop(hop.from_stop_id);
    	var to_stop = sc.findStop(hop.to_stop_id);
        var from_pt = wkt.read(from_stop.geometry).geometry;
        var to_pt = wkt.read(to_stop.geometry).geometry;
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
    var rand_color = this.options.default_feature_colors[0]

    if(this.options.arrows_on_hops) {
        new_arrow = this.makeArrow(new_feature.geometry, {
    		"width":1, "size": 7, "supplychain_instance_id": scid,
            "hop_instance_id": hop.instance_id, "from_stop_id": hop.from_stop_id,
            "to_stop_id": hop.to_stop_id, "ref": hop, "color": hop.getAttr("color", rand_color),
    		"fcolor": hop.getAttr("color", rand_color), "label":""
        });
        var tmp = new_arrow;
        if(new_arrow instanceof Array) {
            new_arrow = tmp[0];
            new_arrow2 = tmp[1];
        }
    	new_arrow.renderIntent = new_arrow2.renderIntent = "arrow";
    }

    new_feature.attributes = Sourcemap.deep_clone(hop.attributes);
    new_feature.attributes.supplychain_instance_id = scid;
    new_feature.attributes.hop_instance_id = hop.instance_id;
    new_feature.attributes.from_stop_id = hop.from_stop_id;
    new_feature.attributes.to_stop_id = hop.to_stop_id;
    hop.attributes.title = new_feature.attributes.title = 
    	hop.getAttr("title", from_stop.getAttr("title","")+" to "+to_stop.getAttr("title",""));

    new_feature.attributes.width = 2;
    new_feature.attributes.opacity = 0.8;
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
       
    var r = {"hop":new_feature};
    r.hop.attributes.hop_component = "hop";
    if(new_arrow) {
        r.arrow = new_arrow;
        if(new_arrow2) {
            r.arrow2 = new_arrow2;
        }
    }
    return r;
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
        angle = Sourcemap.great_circle_bearing(mid_pt, wrap);
        angle2 = Sourcemap.great_circle_bearing(mid_pt2, to);
    } else {
        mid_pt = Sourcemap.great_circle_midpoint(from, to);
        angle = Sourcemap.great_circle_bearing(mid_pt, to);
    }

    mid_pt = new OpenLayers.Geometry.Point(mid_pt.x, mid_pt.y);
    mid_pt = mid_pt.transform(pdst, psrc);

    var attrs = {"type": "arrow", "hop_component": "arrow", "width": 0, "opacity":1.0, "angle": angle};
    var o = o || {};
    for(var k in o) attrs[k] = o[k];
    var a = new OpenLayers.Feature.Vector(mid_pt, attrs);
    var a2 = null;
    if(wrapped) {
        mid_pt2 = new OpenLayers.Geometry.Point(mid_pt2.x, mid_pt2.y);
        mid_pt2 = mid_pt2.transform(pdst, psrc);
        var attrs = _S.deep_clone(attrs);
        attrs.angle = angle2;
        attrs.hop_component = "arrow2";
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

Sourcemap.Map.prototype.getFeaturesExtent = function() {
    var bounds = new OpenLayers.Bounds();
    for(var scid in this.stop_features) {
        for(var k in this.stop_features[scid]) {
            var s = this.stop_features[scid][k];
            s = s.stop ? s.stop : s;
            bounds.extend(s.geometry.bounds);
        }
    }
    for(var scid in this.cluster_features) {
        for(var k in this.cluster_features) {
            var c = this.cluster_features[scid][k];
            if(c) bounds = bounds.extend(c.geometry.bounds);
        }
    }
    for(var scid in this.hop_features) {
        for(var fromStop in this.hop_features[scid]){
            for (var toStop in this.hop_features[scid][fromStop]){
                var h = this.hop_features[scid][fromStop][toStop];
                h = h.hop ? h.hop : h;
                bounds.extend(h.geometry.bounds);
            }
        }
    }
    return bounds;
}

Sourcemap.Map.prototype.zoomToExtent = function(bounds, closest){
    
    var center = bounds.getCenterLonLat();

    //if there's only one stop on the map, let's zoom to the minimum level
    //if (oneStop() == true){
    //    this.map.setCenter(center, this.map.minZoomLevel+1);
    //    console.log("One stop");
    //}
    //else{
        if (this.map.baseLayer.wrapDateLine) {
            var maxExtent = this.map.getMaxExtent();
            
            bounds_c = bounds.clone();
            while (bounds_c.right < bounds_c.left) {
                bounds_c.right += maxExtent.getWidth();
            }
            
            center = bounds_c.getCenterLonLat();
            //center = bounds_c.getCenterLonLat().wrapDateLine(maxExtent);
        }
        
        this.map.setCenter(center, this.getZoomForExtent(bounds, closest));
        //this.map.setCenter(center, 2);
    //}
}

Sourcemap.Map.prototype.getZoomForExtent = function(extent, closest) {
    var viewSize = this.map.getSize();

    // add padding around viewport so features don't appear offscreen
    // TODO: improve the way this works
    viewSize.h *= .5;
    viewSize.w *= .5;
   
    var idealResolution = Math.max( extent.getWidth()  / viewSize.w,
                                    extent.getHeight() / viewSize.h );


    var zoomForExtent = this.getZoomForResolution(idealResolution,closest);
    //console.log("ZFE:"+zoomForExtent+",viewSize:"+viewSize.w+"/"+viewSize.h+",extent:"+extent.getWidth()+"/"+extent.getHeight());
    return zoomForExtent;
    //return this.getZoomForResolution(idealResolution, closest);

}

Sourcemap.Map.prototype.getZoomForResolution = function (resolution, closest){
    var zoom;
    if(this.map.fractionalZoom) {
        var lowZoom = 0;
        var highZoom = this.resolutions.length - 1;
        var highRes = this.resolutions[lowZoom];
        var lowRes = this.resolutions[highZoom];
        var res;
        for(var i=0, len=this.map.baseLayer.resolutions.length; i<len; ++i) {
            res = this.map.baseLayer.resolutions.resolutions[i];
            if(res >= resolution) {
                highRes = res;
                lowZoom = i;
            }
            if(res <= resolution) {
                lowRes = res;
                highZoom = i;
                break;
            }
        }
        var dRes = highRes - lowRes;
        if(dRes > 0) {
            zoom = lowZoom + ((highRes - resolution) / dRes);
        } else {
            zoom = lowZoom;
        }
        } else {
            var diff;
            var minDiff = Number.POSITIVE_INFINITY;

            var init_i = 0;
            if(this.map.baseLayer.name=="default")
                init_i += 2;
                
            for(var i=0, len=this.map.baseLayer.resolutions.length; i<len; i++) {
                if (closest) {
                    // use false all the time
                    if(this.map.baseLayer.name!="default")
                        diff = Math.abs(this.map.baseLayer.resolutions[i+2] - resolution);
                    else
                        diff = Math.abs(this.map.baseLayer.resolutions[i] - resolution);
                    if (diff > minDiff) {
                        break;
                    }
                    minDiff = diff;
                } else {
                    if (this.map.baseLayer.resolutions[i] < resolution*1.38) {
                        // *1.38 : magic number to extent view for banner
                        i += init_i;
                        break;
                    }
                }
            }
            zoom = Math.max(0, i);
        }


        var zoomFromReso = Math.max(this.map.minZoomLevel, zoom);
        //console.log("zoom:"+zoom+"/minZoom:"+this.map.minZoomLevel+"/zoomFromReso:"+zoomFromReso);
        return zoomFromReso;
        //return Math.max(this.map.minZoomLevel, zoom);
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
    this.cluster_features[scid] = [];
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

Sourcemap.Map.prototype.getSelected = function() {
    var s = [];
    for(var i=0; i<this.map.layers.length; i++) {
        var l = this.map.layers[i];
        if(l instanceof OpenLayers.Layer.Vector) {
            if(l.selectedFeatures instanceof Array)
                s = s.concat(l.selectedFeatures.slice(0));
        }
    }
    return s;
}

Sourcemap.Cluster = function(distance, threshold, map) {
    this.map = map;
    OpenLayers.Strategy.Cluster.prototype.initialize.apply(this, arguments);
    this.initialize.apply(this, arguments);    
}
Sourcemap.Cluster.prototype = new OpenLayers.Strategy.Cluster();

Sourcemap.Cluster.prototype.createCluster = function(feature) {
    var scid = feature.attributes.supplychain_instance_id;
    var center = feature.geometry.getBounds().getCenterLonLat();
    var cid = "cluster-"+feature.attributes.stop_instance_id;
    // TODO: aggregate size?
    var csize = this.map.options.default_stop_size;
    var slabel = feature.attributes.title;
    var fsize = 12;
    slabel = 1;
    
    var stcolor = new Sourcemap.Color();
    try {
        stcolor = stcolor.fromHex(feature.attributes.color);
    } catch(e) {
        stcolor = stcolor.fromHex(Sourcemap.Map.prototype.defaults.default_feature_color);
    }
    stcolor.r = Math.max(0,stcolor.r-30); 
    stcolor.g = Math.max(0,stcolor.g-30);
    stcolor.b = Math.max(0,stcolor.b-30);
    
    var fcolor = stcolor.toString();
    var cluster = new OpenLayers.Feature.Vector(
        new OpenLayers.Geometry.Point(center.lon, center.lat), {
            "count": 1, 
            "size":csize,
            "fsize":fsize+"px",
            "swidth":4,
            "fcolor":fcolor,
            "label": slabel,
            "yoffset":0,          
            "supplychain_instance_id":scid,
            "cluster_instance_id":cid
        }
    );

    cluster.renderIntent = "cluster";

    cluster.cluster = [feature];    
    this.map.cluster_features[scid].push(cluster);
    
    return cluster;
}
Sourcemap.Cluster.prototype.addToCluster = function(cluster, feature) {
   
    // add
    cluster.cluster.push(feature);
    
    // calulate avg color
    var c = new Sourcemap.Color();
    for(var i=0; i<cluster.cluster.length; i++) {
        var f = cluster.cluster[i];
        var fc = false;
        if(f.attributes.color) {
            fc = (new Sourcemap.Color()).fromHex(f.attributes.color);
        } else continue;
        c.r += fc.r; c.g += fc.g; c.b += fc.b;
    }
    
    c.r /= cluster.cluster.length;
    c.g /= cluster.cluster.length;
    c.b /= cluster.cluster.length;

    cluster.attributes.color = c.toString();
    cluster.attributes.scolor = c.toString();

    // darken font color
    c.r = Math.max(0,c.r-30); 
    c.g = Math.max(0,c.g-30);
    c.b = Math.max(0,c.b-30);    
    cluster.attributes.fcolor = c.toString();

    cluster.attributes.count += 1;
    slabel = cluster.attributes.count;

    cluster.attributes.label = slabel;
}
