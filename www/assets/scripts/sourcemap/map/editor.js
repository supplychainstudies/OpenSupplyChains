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

Sourcemap.Map.Editor = function(map, o) {
    var o = o || {}
    this.map = map.map ? map.map : map;
    if(map instanceof Sourcemap.Map.Base)
        this.map_view = map;
    Sourcemap.Configurable.call(this);
    this.instance_id = Sourcemap.instance_id("sourcemap-editor");
    this.map.editor = this;
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

    // add symbol for 'connecting'
    if(!OpenLayers.Renderer.symbol.stareight)
        OpenLayers.Renderer.symbol.stareight = [
            0,9,
                3,5, 9,8, 5,3,
            9,0,
                3,-5, 9,-8, 5,-3,
            0,-9,
                -3,-5, -9,-8, -5,-3,
            -9,0,
                -3,5, -9,8, -5,3,
            0,9
        ];

    // listen for supplychain updates and save
    Sourcemap.listen('supplychain-updated', function(evt, sc, no_remap) {
        var succ = $.proxy(function() {
            this.map_view.updateStatus("Saved...", "good-news");           
        }, this);
        var fail = $.proxy(function() {
            this.map_view.updateStatus("Could not save! Contact support.", "bad-news");
        }, this);
        // redraw ?
        if(!no_remap) {
            this.map.mapSupplychain(sc.instance_id, true);
        }
        this.map_view.updateStatus("Saving...");
        Sourcemap.saveSupplychain(sc, {"supplychain_id": sc.remote_id, "success": succ, "failure": fail});

        // maintain visualization
        var viz = this.map_view.visualization_mode;
        if(viz) {
            this.map_view.visualization_mode = null;
            this.map_view.toggleVisualization(viz);
        }

    }, this);

    // listen for select clickout events, for connect-to, etc.
    Sourcemap.listen('map:feature_clickout', $.proxy(function(evt, map, ftr) {
        this.connect_from = false;
    }, this));

    Sourcemap.listen('map:feature_unselected', $.proxy(function() {
        // pass
    }, this));

    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
        if(this.connect_from) {
            var fromstid = this.connect_from.attributes.stop_instance_id;
            var tostid = ftr.attributes.stop_instance_id;
            if(fromstid == tostid) return;
            var sc = this.map.findSupplychain(ftr.attributes.supplychain_instance_id);
            var fromst = sc.findStop(fromstid);
            var tost = sc.findStop(tostid);
            var new_hop = fromst.makeHopTo(tost);
            sc.addHop(new_hop);
            this.map.mapHop(new_hop, sc.instance_id);
            this.connect_from = false;
            // TODO: review if the selection of the hop is ideal
            this.connect_from = false;
            // if you want to uncomment this, figure out why it breaks things.
            //this.map.controls.select.select(this.map.hopFeature(new_hop));
            Sourcemap.broadcast('supplychain-updated', sc);
        } else if(ftr.attributes.hop_instance_id) {
            var ref = this.map.hopFeature(ftr.attributes.supplychain_instance_id, ftr.attributes.hop_instance_id);
            var supplychain = this.map.findSupplychain(ftr.attributes.supplychain_instance_id);
            this.showEdit(ftr);
        } else if(ftr.attributes.stop_instance_id) {
            var ref = this.map.stopFeature(ftr.attributes.supplychain_instance_id, ftr.attributes.stop_instance_id);
            var supplychain = this.map.findSupplychain(ftr.attributes.supplychain_instance_id);
            this.showEdit(ftr);
        }
    }, this));

    this.map.dockAdd('addstop', {
        "title": 'Add a Point',
        "content": "<span>Add a Point</span>",
        "ordinal": 4,
        "panel": "edit",
        "callbacks": {
            "click": $.proxy(function() {
    			var s = this.map_view;
    		    var cb = function(p, tx, th) {
    				if(this.dialog) {
    					if(!this.dialog) {
    				        this.dialog = $('<div id="dialog"></div>');
    				        $(this.map.map.div).append(this.dialog);
    				    } else this.hideDialog();

    				    this.dialog_content = $('<div id="dialog-content"></div>');
    				    this.dialog.append(this.dialog_content);
    			        $(this.dialog_content).html(th);
    			        $(this.dialog_content).find(".close").click($.proxy(function() { 
    				        $(this.dialog).hide();
    				        $(this.dialog).removeClass("called-out");					
    						this.dialog_content.empty(); 
    					}, this));
    					$(this.dialog_content).find("#newpoint-button").click($.proxy(function() {
    						var f = this.dialog_content.find('form');
    			            var vals = f.serializeArray();
    			            var attributes = {};
    			            for(var i=0; i<vals.length; i++) {
    			                attributes[vals[i].name] = vals[i].value;
    			            }
    			
    		                var sc = false;
    		                for(var k in this.map.supplychains) { sc = this.map.supplychains[k]; break; }
    						
    						$(this.dialog_content).find("#newpoint-button").attr("disabled","disabled").addClass("disabled");
    						
    		                var cb = $.proxy(function(data) {
    		                    if(data && data.results && data.results.length) {
    								this.map.controls.select.unselectAll();
    								var new_geom = new OpenLayers.Geometry.Point(data.results[0].lon, data.results[0].lat);
    			                    new_geom = new_geom.transform(
    			                        new OpenLayers.Projection('EPSG:4326'),
    			                        new OpenLayers.Projection('EPSG:900913')
    			                    );
    			                    var geometry = (new OpenLayers.Format.WKT()).write(new OpenLayers.Feature.Vector(new_geom));
    								var stop = new Sourcemap.Stop(geometry, this.attr);
    		                        stop.setAttr("address", data.results[0].placename);
    								this.sc.addStop(stop);
    				                Sourcemap.broadcast('supplychain-updated', sc);			
    								
    		                        stop.attributes.stop_instance_id = stop.instance_id;
    		                        stop.attributes.supplychain_instance_id = stop.supplychain_id;
    				                this.map.mapSupplychain(this.sc.instance_id);                									
    		                        this.map.controls.select.select(this.map.stopFeature(this.sc.instance_id, stop.instance_id));
    		                    } else {
    								$("#dialog").shake();
    								$("#dialog").find("#newpoint-button").removeAttr("disabled").removeClass("disabled");									
    					        }
    		                }, {"map":this.map, "sc":sc, "attr":attributes});

    		                Sourcemap.Stop.geocode(attributes.address, cb);
    					}, this));
    			        var fade = $(this.dialog).css("display") == "block" ? 0 : 100;
    			        $(this.dialog).fadeIn(fade, function() {});
    					$(this.dialog).addClass("called-out");						
    			    }
    				
    			}
    		    Sourcemap.template('map/edit/add-stop', cb, s, s);
    			/*
                this.map.controls.select.unselectAll();
                
                // make a suitable geometry
                var geometry = (new OpenLayers.Format.WKT()).write(
                    new OpenLayers.Feature.Vector(
                        new OpenLayers.Geometry.Point(this.map.map.center.lon, this.map.map.center.lat)
                    )
                );
                attributes = {};
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
                this.map.mapSupplychain(sc.instance_id);                
                
                var cb = $.proxy(function(data) {
                    if(data && data.results) {
                        this.stop.setAttr("address", data.results[0].placename);
                        
                        this.stop.attributes.stop_instance_id = this.stop.instance_id;
                        this.stop.attributes.supplychain_instance_id = this.stop.supplychain_id;
                        this.map.controls.select.select(this.map.stopFeature(sc.instance_id, this.stop.instance_id));
                           
                    }
                }, {"stop":new_stop, "map":this.map, "sc":sc});
                                
                Sourcemap.Stop.geocode(new_stop, cb);
                */
   
            }, this)
        }
    });

    // save contents of editor ui on dialog close
    Sourcemap.listen('sourcemap-base-dialog-close', $.proxy(function(b, vs) {
        if(this.editing) {
            // save updated attributes
            var f = this.map_view.dialog_content.find('form');
            var valsa = f.serializeArray();
            var vals = {};
            for(var i=0; i<valsa.length; i++) {
                vals[valsa[i].name] = valsa[i].value;
            }
            this.updateFeature(this.editing, vals);
        }
        this.editing = null;
    }, this));
    
    // set up drag control
    var stopl = false;
    for(var k in this.map.supplychains) {
        stopl = this.map.getStopLayer(k);
        break;
    }

    var scid = k;

    this.map.addControl('stopdrag', new OpenLayers.Control.DragFeature(stopl, {
        "onStart": $.proxy(function(ftr, px) {
            if(ftr.cluster) this.map.controls.stopdrag.cancel();
            return false;
        }, this),
        "onDrag": $.proxy(function(ftr, px) {
            if(this.map.map.getMaxExtent().containsLonLat(this.map.map.getLonLatFromPixel(px)))
                this.moveStopToFeatureLoc(ftr, false, false);
            else this.map.controls.stopdrag.cancel();
            this.map.controls.select.unselectAll();
        }, this),
        "onComplete": $.proxy(function(ftr, px) {
            if(ftr.attributes.stop_instance_id) {
                this.editor.moveStopToFeatureLoc(ftr, true, true);
                //this.editor.syncStopHops(ftr.attributes.supplychain_instance_id, ftr.attributes.stop_instance_id);
            }
            //this.editor.map.controls.select.select(ftr);
        }, {"editor": this})
    }));

    this.map.controls.stopdrag.handlers.drag.stopDown = false;
    this.map.controls.stopdrag.handlers.drag.stopUp = false;
    this.map.controls.stopdrag.handlers.drag.stopClick = false;

    this.map.controls.stopdrag.handlers.feature.stopDown = false;
    this.map.controls.stopdrag.handlers.feature.stopUp = false;
    this.map.controls.stopdrag.handlers.feature.stopClick = false;

    this.map.controls.stopdrag.activate();
    
    // load transport catalog
    this.loadTransportCatalog();

    // setup calculator display
    $("#tileset-select").change($.proxy(function(evt) {
    	var sc = false;
        for(var k in this.editor.map.supplychains) {
            sc = this.editor.map.supplychains[k];
            break;
        }
     	sc.attributes["sm:ui:tileset"] = $(evt.target).val();
    	this.editor.map_view.toggleTileset(sc);
    	Sourcemap.broadcast('supplychain-updated', sc);
    	
    }, {"editor":this}));
    
    $("#impact-use-co2e").change($.proxy(function(evt) {
    	var co2e = $(evt.target).is(':checked') ? true : false;
    	
        // grab the first supplychain
        var sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }
    	
    	if(co2e) { 
            sc.attributes["sm:ui:co2e"] = true; 
        } else { 
            delete sc.attributes["sm:ui:co2e"]; 
        }
    	
    	this.map_view.updateFilterDisplay(sc);
    	Sourcemap.broadcast('supplychain-updated', sc);
    }, this));
    $("#impact-use-weight").change($.proxy(function(evt) {
    	var weight = $(evt.target).is(':checked') ? true : false;
        // grab the first supplychain
        var sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }
    	
    	if(weight) { sc.attributes["sm:ui:weight"] = true } 
    	else { delete sc.attributes["sm:ui:weight"]; }
    	
    	this.map_view.updateFilterDisplay(sc);
    	Sourcemap.broadcast('supplychain-updated', sc);
    }, this));
    $("#impact-use-water").change($.proxy(function(evt) {
    	var water = $(evt.target).is(':checked') ? true : false;
        // grab the first supplychain
        var sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }

    	if(water) { sc.attributes["sm:ui:water"] = true; } 
    	else { delete sc.attributes["sm:ui:water"]; }

    	this.map_view.updateFilterDisplay(sc);
    	Sourcemap.broadcast('supplychain-updated', sc);
    }, this));
}

Sourcemap.Map.Editor.prototype.loadTransportCatalog = function() {
    var o = {
        "url": "services/catalogs/osi", "type": "get",
        "data": {"category": "transportation"},
        "success": $.proxy(function(data) {
            var nil = {
                "name": 'None', "co2e": 0.0, "unit": "kg*km"
            }
            this.transport_catalog = data.results;
            this.transport_catalog.unshift(nil);
        }, this)
    };
    $.ajax(o);
}

Sourcemap.Map.Editor.prototype.moveStopToFeatureLoc = function(ftr, geocode, trigger_events) {
    var scid = ftr.attributes.supplychain_instance_id;
    var stid = ftr.attributes.stop_instance_id;
    var st = this.map.findSupplychain(scid).findStop(stid);
    st.geometry = (new OpenLayers.Format.WKT()).write(ftr);
    var ll = new OpenLayers.LonLat(ftr.geometry.x, ftr.geometry.y)
    ll = ll.clone();
    ll.transform(new OpenLayers.Projection('EPSG:900913'), new OpenLayers.Projection('EPSG:4326'));
    this.syncStopHops(scid, st);
    if(geocode) {
        this.map_view.updateStatus("Moved stop '"+st.getLabel()+'"..."');
        Sourcemap.Stop.geocode(ll, $.proxy(function(data) {
            if(data && data.results && data.results.length) {
                this.editor.map_view.updateStatus("Updated address...");
                this.stop.setAttr("address", data.results[0].placename);
                Sourcemap.broadcast('supplychain-updated', 
                    this.editor.map.findSupplychain(this.stop.supplychain_id), true
                );
                // if you uncomment this, be prepared to fix some things.
                //this.editor.map.controls.select.select(ftr);
            } else {
                var geom = (new OpenLayers.Format.WKT()).read(this.stop.geometry);
                var ll = new OpenLayers.LonLat(geom.geometry.x, geom.geometry.y)
                this.stop.setAttr('address', ll.lat+','+ll.lon);
            }
        }, {"stop": st, "editor": this, "trigger_events": trigger_events}));
    }
    if(trigger_events) {
        Sourcemap.broadcast('supplychain-updated', 
            this.map.findSupplychain(st.supplychain_id)
        );
    }
}

Sourcemap.Map.Editor.prototype.syncStopHops = function(sc, st) {
    if(!(sc instanceof Sourcemap.Supplychain))
        sc = this.map.findSupplychain(sc);
    if(!(st instanceof Sourcemap.Stop))
        st = sc.findStop(st);
    var stophops = sc.stopHops(st);
    var fs = [];
    // inbound hops
    for(var i=0; i<stophops["in"].length; i++) {
        var h = sc.findHop(stophops["in"][i]);
        var fromst = sc.findStop(h.from_stop_id);
        var tost = st;
        var tmph = fromst.makeHopTo(tost);
        h.geometry = tmph.geometry;
    }
    // outbound hops
    for(var i=0; i<stophops.out.length; i++) {
        var h = sc.findHop(stophops.out[i]);
        var fromst = st;
        var tost = sc.findStop(h.to_stop_id);
        var tmph = fromst.makeHopTo(tost);
        h.geometry = tmph.geometry;
    }
    // both
}

Sourcemap.Map.Editor.prototype.showEdit = function(ftr) {
    var ref = ftr.attributes.ref;
    var reftype = ref instanceof Sourcemap.Hop ? 'hop' : 'stop';

    var s = {"ref": ref, "editor": this, "feature": ftr};
    var cb = function(p, tx, th) {
     	this.editor.map_view.showDialog(th);
    	this.editor.prepEdit(this.ref, this.attr, this.feature);
    }
    Sourcemap.template('map/edit/edit-'+reftype, cb, s, s);
}

Sourcemap.Map.Editor.prototype.prepEdit = function(ref, attr, ftr) {

    // track currently edited feature
    this.editing = ref;
    $("#editor-tabs").tabs();

    $(this.map_view.dialog).find('.load-catalog-button').click($.proxy(function() {
        this.q = '';
        this.params = {"name": ''};
        this.showCatalog(this);
    }, this));

    $(this.map_view.dialog).find('#media-content-type').bind('change', $.proxy(function(e) {
        this.editor.updateMedia(ref, this);
    }, {"ref" : ref, "editor" : this}));

    // populate media type preview on load 
    $(this.map_view.dialog).find('#media-content-type').trigger('change'); 

    // general case, save on every change
    $(this.map_view.dialog).find('input,select,textarea').bind('change', $.proxy(function(e) {
        var kvpairs = $(this.editor.map_view.dialog).find('form').serializeArray();
        var vals = {};
        for(var i=0; i<kvpairs.length; i++) vals[kvpairs[i].name] = kvpairs[i].value;
        this.editor.updateFeature(ref, vals);
        this.editor.updateMedia(ref, this);
    }, {"ref": ref, "editor": this}));

    

    // impact calculator for stops
    if(ref instanceof Sourcemap.Stop) {
        $("#edit-stop-footprint input").keyup($.proxy(function(e){ 
            editor = $('#edit-stop-footprint');
            var quantity = editor.find('input[name="qty"]').val(); 
            var unit     = editor.find('.footprint-unit').val(); 
            var weight   = (unit == "kg") ? 1 : Math.max(editor.find('input[name="weight"]').val(), 0); 
            var factor   = editor.find('input[name="co2e"]').val(); 

            if (!isNaN(quantity * factor * weight)){ 
                var output = quantity * weight * factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kg', 2);
                editor.find('.result').text(scaled.value + " " + scaled.unit + " CO2e"); 
            } else { editor.find('.result').text("-"); }
        }, this)); 
            	
    	$(".footprint-unit").change($.proxy(function(e){
    		if($(e.target).val() != "kg") {
    			$(".weight-context").removeClass("hidden");
    		} else {
    			$(".weight-context").addClass("hidden");
    		}
    		editor = $('#edit-stop-footprint');
            var quantity = editor.find('input[name="qty"]').val(); 
            var unit     = editor.find('.footprint-unit').val(); 
            var weight   = (unit == "kg") ? 1 : Math.max(editor.find('input[name="weight"]').val(), 0); 
            var factor   = editor.find('input[name="co2e"]').val(); 

            if (!isNaN(quantity * factor * weight)){ 
                var output = quantity * weight * factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kg', 2);
                editor.find('.result').text(scaled.value + " " + scaled.unit + " CO2e"); 
            } else { editor.find('.result').text("-"); }            
    	}, this));
        // trigger event on load
        $("#edit-stop-footprint input").trigger('keyup');

    // impact calculator for hops
    } else {
        $("#edit-hop-footprint input").keyup($.proxy(function(e){ 
            editor = $('#edit-hop-footprint');
            var qty   = editor.find('input[name="qty"]').val();
            var distance = editor.find('input[name="distance"]').val(); 
            var factor   = editor.find('input[name="co2e"]').val(); 
            var unit     = 'kg';

            if(!isNaN(qty * distance * factor)){ 
                var output = qty * distance * factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kg', 2);
                editor.find('.result').text(scaled.value + " " + scaled.unit + " CO2e"); 
            } else { editor.find('.result').text("-"); }            
        }, this)); 

    	// Transport is a special case value as impact, but save name
    	$("#transportation-type").unbind("change").change($.proxy(function(e){ 
            $('#edit-hop-footprint input[name="co2e"]').val($(this + ':selected').val()); 
            var unit = false;
            var transnm = $('#edit-hop-footprint select[name="transportcat"] option:selected').text();
            for(var k in this.editor.transport_catalog) {
                var item = this.editor.transport_catalog[k];
                if(item.name == transnm) {
                    if(item.unit.match(/person|passenger|pax/i))
                        unit = "pax";
                }
            }
            editor = $('#edit-hop-footprint');
            var qty = editor.find('input[name="qty"]').val();
            var distance = editor.find('input[name="distance"]').val(); 
            var factor = editor.find('input[name="co2e"]').val(); 
            var unit = unit || 'kg';
            
            // update unit
            $('#edit-hop-footprint input[name="unit"]').val(unit);

            if (!isNaN(qty * distance * factor)){ 
                var output = qty * distance * factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kg', 2);
                editor.find('.result').text(scaled.value + " " + scaled.unit + " CO2e"); 
            } else { editor.find('.result').text("-"); }
            
    		var kvpairs = $(this.editor.map_view.dialog).find('form').serializeArray();
    		kvpairs.push({'name':'unit', 'value':unit});
            var vals = {};
           	for(var i=0; i<kvpairs.length; i++) {
    			if(kvpairs[i].name == "transportcat") {
                    vals['transport'] = transnm;
                } else {
                    vals[kvpairs[i].name] = kvpairs[i].value;
                }
    		}
            this.editor.updateFeature(ref, vals, true);
    	}, {"ref": ref, "editor": this}));
        $("#edit-hop-footprint input").trigger('keyup');
    }
    
    var s = {"ref": ref, "editor": this, "feature": ftr};

    $(this.map_view.dialog).find('.connect-button').click($.proxy(function(e) {
        this.editor.map_view.hideDialog();
        this.feature.renderIntent = "connecting";
        var sl = this.editor.map.getStopLayer(this.feature.attributes.supplychain_instance_id);
        sl.drawFeature(this.feature);
        this.editor.connect_from = this.feature;
    }, s));
    
    $(this.map_view.dialog).find('.delete-button').click($.proxy(function(e) {
        var supplychain = this.editor.map.findSupplychain(this.ref.supplychain_id);
        if(this.ref instanceof Sourcemap.Stop) {
            supplychain.removeStop(this.ref);
        } else if(this.ref instanceof Sourcemap.Hop) {
            supplychain.removeHop(this.ref);
        }
        this.editor.map_view.hideDialog(true);
        Sourcemap.broadcast('supplychain-updated', supplychain);
    }, s));

    var cb = function(e) {
        //TODO: maybe move this down and add a spinner or disable the map/editor?
    }
}

Sourcemap.Map.Editor.prototype.updateFeature = function(ref, updated_vals, noremap) {
    var geocoding = false;
    var vals = updated_vals || {};

    for(var k in vals) {
        var val = vals[k];
        if((ref instanceof Sourcemap.Stop) && k === "address" && (val != ref.getAttr("address", false))) {
            ref.setAttr(k, val);
            // if address is set, move the stop.
            geocoding = true;
            Sourcemap.Stop.geocode(ref.getAttr("address"), $.proxy(function(res) {
                var pl = res && res.results ? res.results[0] : false;
                if(pl) {
                    this.stop.setAttr("address", pl.placename);
                    var new_geom = new OpenLayers.Geometry.Point(pl.lon, pl.lat);
                    new_geom = new_geom.transform(
                        new OpenLayers.Projection('EPSG:4326'),
                        new OpenLayers.Projection('EPSG:900913')
                    );
                    this.stop.geometry = (new OpenLayers.Format.WKT()).write(new OpenLayers.Feature.Vector(new_geom));
                    var scid = this.stop.supplychain_id;
                    this.editor.map_view.updateStatus("Moved stop to '"+pl.placename+"'...", "good-news");
                } else {
                    $(this.edit_form).find('input,textarea,select').removeAttr("disabled");
                    this.editor.map_view.updateStatus("Could not geocode...", "bad-news");
                    this.stop.attributes.address = "Invalid Address";
                }
  
                if(this.ref) {
                    this.ref.setAttr(k, val);
                }
                //this.editor.map.broadcast('supplychain-updated', this.editor.map.supplychains[this.stop.supplychain_id]);
                this.editor.map.broadcast('supplychain-updated', this.editor.map.supplychains[this.stop.supplychain_id]);
                this.editor.map.controls.select.select(this.editor.map.stopFeature(ref));
            }, {"stop": ref, "editor": this}));
        } else {
            ref.setAttr(k, val);
        }
    }
    if(!geocoding) {
        this.map.broadcast('supplychain-updated', this.map.supplychains[ref.supplychain_id], noremap);
    }
}

Sourcemap.Map.Editor.prototype.updateCatalogListing = function(o) {
    if(!this.catalog_search_xhr) this.catalog_search_xhr = {};
    if(this.catalog_search_xhr[o.catalog]) this.catalog_search_xhr[o.catalog].abort();
    this.catalog_search_xhr[o.catalog] = $.ajax({"url": "services/catalogs/"+o.catalog, "data": o.params || {}, 
        "success": $.proxy(function(json) {
            var cat_html = $('<ul class="catalog-items"></ul>');
            var alt = "";
            for(var i=0; i<json.results.length; i++) {
    			if(!(json.results[i].co2e)) { continue;}
                // Todo: Template this                
                var cat_content = '<div class="cat-item-footprints">'                    
                cat_content +=  json.results[i].co2e ? '<div class="cat-item-co2e"></div>' : '';
                cat_content += '<div class="clear"></div></div>'; 
                cat_content += '<div class="cat-item-name">'+_S.ttrunc(json.results[i].name, 30)+' <span class="cat-value">('+
    							Math.round(100*json.results[i].co2e)/100+' kg co2e)</span></div>';

    			cat_content += '<div class="clear"></div>';                    
                
                var new_li = $('<li class="catalog-item"></li>').html(cat_content);                   
                
                $(new_li).click($.proxy(function(evt) {
                    this.editor.applyCatalogItem(this.catalog, this.item, this.ref);
                }, {"item": json.results[i], "editor": this.editor, "ref": o.ref, "catalog": o.catalog}));
                cat_html.append(new_li);
            }
            o.results = json.results;
            o.params = json.parameters;
            $(this.editor.map_view.dialog).find('.catalog-content').html(cat_html);

            $(this.editor.map_view.dialog).find('.catalog-pager').empty();
            // pager prev
            if(o.params.o > 0) {
                var prev_link = $('<span class="catalog-pager-prev">&laquo; prev</span>').click($.proxy(function() {
                    var o = {};
                    o.editor = this;
                    o.ref = this.ref;
                    o.params = Sourcemap.deep_clone(this.o.params);
                    o.params.o = (parseInt(o.params.o) || 0);
                    if(o.params.o >= o.params.l) o.params.o -= o.params.l;
                    o.params.l = o.params.l;
                    o.catalog = this.o.catalog;
                    this.editor.updateCatalogListing(o);
                }, {"o": o, "editor": this}));
                $(this.editor.map_view.dialog).find('.catalog-pager').append(prev_link);
            }

            // pager next
            if(o.results.length == o.params.l) {
                var next_link = $('<span class="catalog-pager-next">next &raquo;</span>').click($.proxy(function() {
                    var o = {};
                    o.editor = this.editor;
                    o.ref = this.o.ref;
                    o.params = Sourcemap.deep_clone(this.o.params);
                    o.params.o = (parseInt(o.params.o) || 0) + o.params.l;
                    o.catalog = this.o.catalog;
                    this.editor.updateCatalogListing(o);
                }, {"o": o, "editor": this.editor}));
                $(this.editor.map_view.dialog).find('.catalog-pager').append(next_link);
            }

            // search bar
            $(this.editor.map_view.dialog).find('#catalog-search-field').keyup($.proxy(function(evt) {
                if($(evt.target).val().length < 3) return;
                var q = $(evt.target).val();
                var o = {};
                o.editor = this.editor;
                o.ref = this.o.ref;
                o.params = Sourcemap.deep_clone(this.o.params);
                o.params.o = 0;
                o.params.q = q;
                o.catalog = this.o.catalog;
                this.editor.updateCatalogListing(o);
            }, {"o": o, "editor": this.editor}));
        }, {"editor": this, "o": o}), "failure": $.proxy(function() {
            $(this.editor.map_view).find("#edit-catalog").html('<h3 class="bad-news">The catalog is currently unavailable.</h3>');
        }, this)
    });
}

Sourcemap.Map.Editor.prototype.updateMedia = function(ref, editor) {
    	var mediatype = $("#media-content-type").val();
        var message = "";

    	$("#media-content-value").attr("name", mediatype);
    	$("#media-content-value").attr("value", ref.getAttr($("#media-content-type").val(), ""));

    	if(mediatype == "youtube:link") {
            // if we have a valid youtube URL
            if ($('#media-content-value').val().match(/http:\/\/(?:www\.)?youtube.*watch\?v=([a-zA-Z0-9\-_]+)/)){
                var message = '<img src="http://img.youtube.com/vi/'+ref.getAttr("youtube:link","").substr(31)+'/0.jpg" />';
            }
            else{
                var message = '<p>You can insert a Youtube movie or a Flickr slide show.<br/>' 
    					+'A Youtube link looks like this: http://www.youtube.com/watch?v=wqeDfKY37Gk</p>';				
            }
    	} 
        if(mediatype == "flickr:setid") {
            // if we have a valid flickr set ID
            if ($('#media-content-value').val().length > 16){
                var message = '<iframe align="center" src="http://www.flickr.com/slideShow/index.gne?set_id='
                + ref.getAttr("flickr:setid","") +
                '&" frameBorder="0" width="500" scrolling="no" height="500"></iframe>';
            }
            else{
                var message = '<p>You can insert a Youtube movie or a Flickr slide show.<br/>' 
    					+'A Flickr set ID is the sequence of numbers at the end of a set URL.</p>';
            }
    	}
    	
        $("#edit-media .media-preview").html(message);
}


Sourcemap.Map.Editor.prototype.showCatalog = function(o) {
    var o = o || {};
    o.q = o.q ? o.q : '';
    o.catalog = o.catalog ? o.catalog : "osi";
    var tscope = {"editor": this, "o": o, "ref": o.ref};
    Sourcemap.template('map/edit/catalog', function(p, txt, th) {  
        $("#edit-catalog").html(th);  
        this.editor.updateCatalogListing(this.o);
    }, tscope, tscope);
}

Sourcemap.Map.Editor.prototype.applyCatalogItem = function(cat, item, ref) {
    // TODO: add the unit
    var catalog_map = {
        "osi": {
            "name": ["title"],
            "co2e": true,
            "waste": true,
            "water": true,
            "energy": true
        }
    }
    var vals = {};
    for(var k in item) {
        if(catalog_map[cat] && catalog_map[cat][k]) {
            if(catalog_map[cat][k] instanceof Array) {
                var map_to = catalog_map[cat][k];
                for(var i=0; i<map_to.length; i++) {
                    vals[map_to[i]] = item[k];
                }
            } else if(catalog_map[cat][k] instanceof Function) {
                var map_with = catalog_map[cat][k];
                map_with(ref, vals);
            } else if(catalog_map[cat][k]) {
    			vals[k] = item[k];
    		}
        }
    }
    //var ftr = this.map.findFeaturesForStop(this.editing.supplychain_id,this.editing.instance_id).stop;
    //var ref = ftr.stop.attributes.ref;
    //attr = Sourcemap.deep_clone(attr);
    //for(var k in ref.attributes) {
      //  if(attr[k] == undefined) attr[k] = ref.getAttr(k);
    //}
    	//	 var kvpairs = $(this.editor.map_view.dialog).find('form').serializeArray();
    	  //   var vals = {};
    	  //   for(var i=0; i<kvpairs.length; i++) vals[kvpairs[i].name] = kvpairs[i].value;




    this.updateFeature(this.editing, vals);
    this.showEdit(this.map.findFeaturesForStop(this.editing.supplychain_id,this.editing.instance_id).stop);     
    $("#editor-tabs").tabs('select', 2);
    //$("#editor-tabs").tabs('select', 3);
    
}
