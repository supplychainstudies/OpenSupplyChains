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
            this.map_view.updateStatus("Saved.", "good-news");           
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

    // When map is zoom
    Sourcemap.listen('map:zoomend',$.proxy(function(){
        // When in connect mode
        if(this.connect_from){
            var sl = this.map.getStopLayer(this.connect_from.attributes.supplychain_instance_id);
            sl.drawFeature(this.connect_from);
        }
    },this));
    

    // listen for select clickout events, for connect-to, etc.
    Sourcemap.listen('map:feature_clickout', $.proxy(function(evt) {
        if(this.connect_from){
            this.connect_from = false;
            var sc = false;
            for(var k in this.map.supplychains) { sc = this.map.supplychains[k]; break; };
            this.map.mapSupplychain(sc.instance_id, true);
        }

    }, this));

    Sourcemap.listen('map:feature_unselected', $.proxy(function() {
        // pass
    }, this));

    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
		if(!(this.map_view.options.locked)) {
	        if(this.connect_from) {
                if(ftr.attributes.hop_instance_id) return;
	            var fromstid = this.connect_from.attributes.stop_instance_id;
	            var tostid = ftr.attributes.stop_instance_id;
	            if(fromstid == tostid) return;
	            var sc = this.map.findSupplychain(ftr.attributes.supplychain_instance_id);
	            var fromst = sc.findStop(fromstid);
	            var tost = sc.findStop(tostid);
	            var new_hop = fromst.makeHopTo(tost);
	            sc.addHop(new_hop);
	            this.map.mapHop(new_hop, sc.instance_id);
	            // TODO: review if the selection of the hop is ideal
	            this.connect_from = false;
                //show ftr window                              
	            //this.showEdit(this.map.findFeaturesForHop(sc.instance_id,fromstid,tostid).arrow);                                
                Sourcemap.broadcast('supplychain-updated', sc);
                // select the feature
                //this.map.controls.select.select(this.map.findFeaturesForHop(sc.instance_id,fromstid,tostid).arrow);                
	            this.map.controls.select.select(this.map.hopFeature(new_hop));

	        } else if(ftr.attributes.hop_instance_id) {
	            var ref = this.map.hopFeature(ftr.attributes.supplychain_instance_id, ftr.attributes.hop_instance_id);
	            var supplychain = this.map.findSupplychain(ftr.attributes.supplychain_instance_id);
	            this.showEdit(ftr);
	        } else if(ftr.attributes.stop_instance_id) {
	            var ref = this.map.stopFeature(ftr.attributes.supplychain_instance_id, ftr.attributes.stop_instance_id);
	            var supplychain = this.map.findSupplychain(ftr.attributes.supplychain_instance_id);
	            this.showEdit(ftr);
	        }
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
                            if(!$.browser.msie){ 
    				        this.dialog = $('<div id="dialog"></div>');
                            }else{
                                this.dialog = $('<div id="dialog" style="background:white"></div>');
                            }
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
                        $(this.dialog_content).find("#newpoint-title").keypress(function(e){
                            if(e.keyCode==13){$("#newpoint-button").click();};
                        });
                        $(this.dialog_content).find("#newpoint-placename").keypress(function(e){
                            if(e.keyCode==13){$("#newpoint-button").click();};
                        });
						$(this.dialog_content).find("#newpoint-placename").after('<div class="error"></div>');
    					$(this.dialog_content).find("#newpoint-button").click($.proxy(function() {
                            if(!$("#newpoint-placename").val()){
                                // If no address and title was inputed
                                if(!$("#newpoint-title").val()){
									$("#dialog").find(".error").html('Need a Location');
                                    //$("#dialog").shake();
                                    return;
                                }
                                $("#newpoint-placename").val($("#newpoint-title").val());
                            }
    						var f = this.dialog_content.find('form');
    			            var vals = f.serializeArray();
    			            var attributes = {};
    			            for(var i=0; i<vals.length; i++) {
    			                attributes[vals[i].name] = vals[i].value;
    			            }
    			
    		                var sc = false;
    		                for(var k in this.map.supplychains) { sc = this.map.supplychains[k]; break; }
    						
    						$(this.dialog_content).find("#newpoint-button").attr("disabled","disabled").addClass("disabled");
    						// Geolocate the location
    		                var cb = $.proxy(function(data) {
    		                    if(data && data.results && data.results.length && (data.results[0].lat != 90) && (data.results[0].lat != -90) ) {
                                    // point successfully added to field!
    								this.map.controls.select.unselectAll();
    								var new_geom = new OpenLayers.Geometry.Point(data.results[0].lon, data.results[0].lat);
    			                    new_geom = new_geom.transform(
    			                        new OpenLayers.Projection('EPSG:4326'),
    			                        new OpenLayers.Projection('EPSG:900913')
    			                    );
    			                    var geometry = (new OpenLayers.Format.WKT()).write(new OpenLayers.Feature.Vector(new_geom));
    								var stop = new Sourcemap.Stop(geometry, this.attr);
                                    // set address by resolution level
                                    if(data.results[0].placename!=""){
        		                        stop.setAttr("address", data.results[0].placename);
                                    } else {
                                        stop.setAttr("address", data.results[0].lat+","+data.results[0].lon); 
                                    }
    								this.sc.addStop(stop);
    				                Sourcemap.broadcast('supplychain-updated', sc);			
    								
    		                        stop.attributes.stop_instance_id = stop.instance_id;
    		                        stop.attributes.supplychain_instance_id = stop.supplychain_id;
    				                this.map.mapSupplychain(this.sc.instance_id);                									
    		                        this.map.controls.select.select(this.map.stopFeature(this.sc.instance_id, stop.instance_id));
                                    var dest = Sourcemap.Stop.toLonLat(stop, 'EPSG:4326');
                                    
                                    var lonlat = new OpenLayers.LonLat(dest.lon, dest.lat);
                                    this.map.map.panTo(new OpenLayers.LonLat(dest.lon, dest.lat));
    		                    } else {
                                    // unsuccessful
    								$("#dialog").find(".error").html('Location Not Found');
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
    

/*
	this.map.dockAdd('addCarbon', {
        "title": 'Add Carbon',
        "content": "<input type='checkbox' id='impact-use-co2e' />",
        "ordinal": 5,
        "panel": "carbon-filter"
    });

	this.map.dockAdd('addWater', {
        "title": 'Add Water',
        "content": "<input type='checkbox' id='impact-use-water' />",
        "ordinal": 6,
        "panel": "water-filters"
    });
    */

    // Click-add function
    //this.map.map.addControl(new OpenLayers.Control.MousePosition());
    this.map.map.events.register("click",this.map.map,function(e){
        if(!(Sourcemap.view_instance.options.locked)) {
            var thismap = Sourcemap.view_instance.map;
            Sourcemap.broadcast('map:feature_clickout');

            // If Ctrl+click
            if(e.ctrlKey){
                var position = this.events.getMousePosition(e);
                var pixel = new OpenLayers.Pixel(e.xy.x,e.xy.y);
                var lonlat = this.getLonLatFromPixel(pixel);
                var new_geom = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
                thismap.controls.select.unselectAll();
                
                new_geom = new_geom.transform(
                   new OpenLayers.Projection('EPSG:900913'),
                   new OpenLayers.Projection('EPSG:4326')
                );
                var geometry = (new OpenLayers.Format.WKT()).write(new OpenLayers.Feature.Vector(new_geom));
                var geom = (new OpenLayers.Format.WKT()).read(new_geom);
                var ll = new OpenLayers.LonLat(geom.geometry.x,geom.geometry.y);
                
                $(".control.addstop").click();
                if($("#newpoint-placename").val()==undefined){
                    // After first time
                    // TODO: need a better solution to load latlon into dialog
                    setTimeout(function(){
                        $("#newpoint-placename").val(ll.lat+","+ll.lon);
                        $("#newpoint-title").focus();
                    },100);
                } else {
                    // After secone time
                    $("#newpoint-placename").val(ll.lat+","+ll.lon);
                    $("#newpoint-title").focus();
                }
                thismap.map.panTo(new OpenLayers.LonLat(lonlat.lon, lonlat.lat));
            };// End ctrl
        }
    }); // End click-add function



    // save contents of editor ui on dialog close
    Sourcemap.listen('sourcemap-base-dialog-close', $.proxy(function(b, vs) {
        //if(this.editing) {
        if(false) {
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

    var dragControl = new OpenLayers.Control.DragFeature();
    
    this.map.addControl('stopdrag', new OpenLayers.Control.DragFeature(stopl, {
        "onStart": $.proxy(function(ftr, px) {
            if(ftr.cluster || this.map_view.options.locked) {
                this.map.controls.stopdrag.cancel();
                clearTimeout(this.map.controls.stopdrag.handlers.drag.pressTimer);
            }
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
            ftr.attributes.size -= 2;
            //this.editor.map.controls.select.select(ftr);
            this.editor.map.controls.stopdrag.cancel();
        }, {"editor": this})
    }));

    // Extend the stopdrag handlers in order to allow click + hold
    // mousedown
    
    this.map.controls.stopdrag.handlers.drag.mousedown = function (evt, ftr) {
        var propagate = true;
        this.dragging = false;
		
        if (this.checkModifiers(evt) && OpenLayers.Event.isLeftClick(evt)) {
            this.pressTimer = window.setTimeout($.proxy(function() {
                // timer finished
                this.stopUp = true;
                this.started = true;
                this.start = evt.xy;
                this.last = evt.xy;
                var ftr = this.control.feature;
                ftr.attributes.size += 2;
                ftr.layer.redraw();
                OpenLayers.Element.addClass(
                    this.map.viewPortDiv, "olDragDown"
                );
                return true;
            }, this),500);
            this.down(evt);
            this.callback("down", [evt.xy]);
            OpenLayers.Event.stop(evt);
          
            if(!this.oldOnselectstart) {
                this.oldOnselectstart = (document.onselectstart) ? document.onselectstart : OpenLayers.Function.True;
            }
            document.onselectstart = OpenLayers.Function.False;
          
            propagate = !this.stopDown;
        } else {
            this.started = false;
            this.start = null;
            this.last = null;
        }
        return propagate;
    }  

    // mouseup
    this.map.controls.stopdrag.handlers.drag.mouseup = function (evt) {
        if (this.started) {
            if(this.documentDrag === true && this.documentEvents) {
                this.adjustXY(evt);
                this.removeDocumentEvents();
            }
            //var dragged = (this.start != this.last);
            var dragged = true;
            this.started = false;
            this.dragging = false;
            OpenLayers.Element.removeClass(
                this.map.viewPortDiv, "olDragDown"
            );
            this.up(evt);
            this.callback("up", [evt.xy]);
            if(dragged) {
                // set dragged to true for prevent presswithout dragging
                this.callback("done", [evt.xy]);
            }
            else {
                clearTimeout(this.pressTimer);
                return false;
            }
            document.onselectstart = this.oldOnselectstart;
        }
        else {
            clearTimeout(this.pressTimer);
            return true;
        }
        return true;
    } 
    
    // mouseout
    this.map.controls.stopdrag.handlers.drag.mouseout = function(evt){
        if (this.started && OpenLayers.Util.mouseLeft(evt, this.map.viewPortDiv)) {
            if(this.documentDrag === true) {
                this.addDocumentEvents();
            } else {
                var dragged = (this.start != this.last);
                this.started = false; 
                this.dragging = false;
                OpenLayers.Element.removeClass(
                    this.map.viewPortDiv, "olDragDown"
                );
                this.out(evt);
                this.callback("out", []);
                if(dragged) {
                    this.callback("done", [evt.xy]);
                }
                if(document.onselectstart) {
                    document.onselectstart = this.oldOnselectstart;
                }
            }
        }
        else{
            clearTimeout(this.pressTimer);
            return false;
        }
        return true;
    }

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

    $("#impact-use-energy").change($.proxy(function(evt) {
    	var water = $(evt.target).is(':checked') ? true : false;
        // grab the first supplychain
        var sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }

    	if(water) { sc.attributes["sm:ui:energy"] = true; } 
    	else { delete sc.attributes["sm:ui:energy"]; }

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
            // TODO : sometimes 400 Bad request or blank place name
            if(data && data.results && data.results.length) {
                this.editor.map_view.updateStatus("Updated address...");
                if(data.results[0].placename!=""){
                    this.stop.setAttr("address", data.results[0].placename);
                } else {
                    this.stop.setAttr("address", data.results[0].lat+","+data.results[0].lon);
                }
                Sourcemap.broadcast('supplychain-updated', 
                    this.editor.map.findSupplychain(this.stop.supplychain_id), true
                );
                // if you uncomment this, be prepared to fix some things.
                //this.editor.map.controls.select.select(ftr);
            } else {
                var geom = (new OpenLayers.Format.WKT()).read(this.stop.geometry);
                var new_geom = new OpenLayers.Geometry.Point(geom.geometry.x, geom.geometry.y); 
                new_geom = new_geom.transform(
                    new OpenLayers.Projection('EPSG:900913'),
                    new OpenLayers.Projection('EPSG:4326') 
                );
                geom = (new OpenLayers.Format.WKT()).read(new_geom); 
                var ll = new OpenLayers.LonLat(geom.geometry.x,geom.geometry.y); 
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
	$("#stop-editor").show();
	$("#dialog").width(410);
	//$("#dialog").height("auto");
	$("#dialog").css("right",37);
	$("#newcatalog").hide();
    this.editing = ref;
    //$("#editor-tabs").tabs();
	$('.accordion-body').hide();
	$('.accordion .accordion-title').click(function() {
		var open = $(this).next().is(":visible");
		$('.accordion-body:visible').each(function() {
			if ($(this).attr("id") == "edit-media")
				$(this).hide();
			else
				$(this).slideToggle('fast');
		});
		$('.accordion-title').find('.arrow').removeClass('arrowopen');
		if (open == false) {
			$(this).next().slideToggle('fast');
			$(this).find('.arrow').addClass('arrowopen');
		}				
		return false;
	});

    $('#edit-description').append('<div id="dialog-desc-counter"></div>');
    $("#dialog-description").keyup(function(){
        var maxlength = $(this).attr('maxlength');
        if(maxlength != -1) {
            var val = $(this).val();
            var lettersleft = maxlength - val.length;

            if(lettersleft>1)
                $('#dialog-desc-counter').text(lettersleft+' characters remaining');
            else
                $('#dialog-desc-counter').text(lettersleft+' character remaining');

            if(val.length>maxlength){
                $(this).val(val.slice(0, maxlength));
            }
         }
    });

	$("#footprint-methodology").change($.proxy(function(evt) {
        // grab the first supplychain
        var sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }
		for (x in sc.stops) {
			if (sc.stops[x] == this.editing) {
				sc.stops[x].attributes.footprintmethodology = evt.currentTarget.value;
				break;
			}
		}
    	Sourcemap.broadcast('supplychain-updated', sc);
    }, this));

    $(this.map_view.dialog).find('#catalog').click($.proxy(function() {
        this.q = '';
        this.params = {"name": ''};
        this.showCatalog(this);
    }, this));

    $(this.map_view.dialog).find('.popup-title').click($.proxy(function() {
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
	
	
		$("#edit-stop-footprint").find('input[name="co2e"]').keyup($.proxy(function(e){ 
            
        }, this));
		
        $("#edit-stop-footprint input").keyup($.proxy(function(e){ 
            editor = $('#edit-stop-footprint');
            //var quantity = editor.find('input[name="qty"]').val(); 
            //var unit     = editor.find('.footprint-unit').val(); 
            //var weight   = (unit == "kg") ? 1 : Math.max(editor.find('input[name="weight"]').val(), 0); 
			
			var weight   = Math.max(editor.find('input[name="weight"]').val(), 0); 
            var co2e_factor   = editor.find('input[name="co2e"]').val(); 
			var energy_factor   = editor.find('input[name="energy"]').val();
			var water_factor   = editor.find('input[name="water"]').val();
			if (weight != 0) {
				editor.find('.timesby').text(weight + " kg");
			}
			//took quantity out
            if (!isNaN(co2e_factor * weight)){ 
                var output = weight * co2e_factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kg', 2);
                editor.find('#c02e-impact-result').text(scaled.value + " " + scaled.unit); 
            } else { editor.find('#c02e-impact-result').text("-"); }
			if (!isNaN(energy_factor * weight)){ 
                var output = weight * energy_factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kwh', 2);
                editor.find('#energy-impact-result').text(scaled.value + " " + scaled.unit); 
            } else { editor.find('#energy-impact-result').text("-"); }
			if (!isNaN(water_factor * weight)){ 
                var output = weight * water_factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'L', 2);
                editor.find('#water-impact-result').text(scaled.value + " " + scaled.unit); 
            } else { editor.find('#water-impact-result').text("-"); }
        }, this)); 
        /*    	
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
            var co2e_factor   = editor.find('input[name="co2e"]').val(); 
			var energy_factor   = editor.find('input[name="energy"]').val();
			var water_factor   = editor.find('input[name="water"]').val();
            if (!isNaN(quantity * co2e_factor * weight)){ 
                var output = quantity * weight * co2e_factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kg', 2);
                editor.find('#c02e-impact-result').text(scaled.value + " " + scaled.unit); 
            } else { editor.find('#c02e-impact-result').text("-"); }
			if (!isNaN(quantity * energy_factor * weight)){ 
                var output = quantity * weight * energy_factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'kwh', 2);
                editor.find('#energy-impact-result').text(scaled.value + " " + scaled.unit); 
            } else { editor.find('#energy-impact-result').text("-"); }
			if (!isNaN(quantity * water_factor * weight)){ 
                var output = quantity * weight * water_factor;
                var scaled = Sourcemap.Units.scale_unit_value(output, 'L', 2);
                editor.find('#water-impact-result').text(scaled.value + " " + scaled.unit); 
            } else { editor.find('#water-impact-result').text("-"); }            
    	}, this));
		*/
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
	if (s.ref.getAttr("co2e_reference",0) != 0) {
		$(this.map_view.dialog).find('#reference-co2e').addClass("lock");
		$(this.map_view.dialog).find('#reference-co2e').click(
	        $.proxy(function(e) { 
	            window.open(this.ref.getAttr("co2e_reference",0),"_blank"); 
	        }, s)
	    );
	} else {
		
	}
	if (s.ref.getAttr("energy_reference",0) != 0) {
		$(this.map_view.dialog).find('#reference-energy').addClass("lock");
		$(this.map_view.dialog).find('#reference-energy').click(
	        $.proxy(function(e) { 
	            window.open(this.ref.getAttr("energy_reference",0),"_blank"); 
	        }, s)
	    );
	} else {
		
	}
	if (s.ref.getAttr("water_reference",0) != 0) {
		$(this.map_view.dialog).find('#reference-water').addClass("lock");
		$(this.map_view.dialog).find('#reference-water').click(
	        $.proxy(function(e) { 
	            window.open(this.ref.getAttr("water_reference",0),"_blank"); 
	        }, s)
	    );
	} else {
		
	}
	$(this.map_view.dialog).find('[name^=co2e]').keyup(
        $.proxy(function(e) { 
			$(this.editor.map_view.dialog).find('#reference-co2e').removeClass("lock");
        }, s)
    );
	$(this.map_view.dialog).find('[name^=energy]').keyup(
        $.proxy(function(e) { 
			$(this.editor.map_view.dialog).find('#reference-energy').removeClass("lock");	
        }, s)
    );
	$(this.map_view.dialog).find('[name^=water]').keyup(
        $.proxy(function(e) { 
			$(this.editor.map_view.dialog).find('#reference-water').removeClass("lock");
        }, s)
    );
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
                if(pl  && (pl.lat != 90) && (pl.lat != -90) ) {
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
			if (json.results.length > 0) {
	            for(var i=0; i<json.results.length; i++) {
	                // Todo: Template this 
					json.results[i].uri = 'http://www.footprinted.org/' + json.results[i].uri;             					
						if (json.results[i].co2e) 
							json.results[i].co2e_reference = json.results[i].uri;		 
						if (json.results[i].energy)
							json.results[i].energy_reference =json.results[i].uri;				
						if (json.results[i].water)
							json.results[i].water_reference = json.results[i].uri;
		                var cat_content = '';                    
						cat_content += '<div class="cat-item-text">';
		                cat_content += '<span class="cat-item-name">'+_S.ttrunc(json.results[i].name, 30)+'</span>';
		                cat_content += json.results[i].geography ? '<span class="cat-item-metainfo">Location: '+json.results[i].geography+'</span>': '';
						cat_content += json.results[i].year!="0" ? '<span class="cat-item-metainfo">Year: '+json.results[i].year+'</span>': '';		
						cat_content += '</div>';
						cat_content += '<span class="cat-item-footprints">';
						cat_content +=  '<span class="cat-item-co2e">';
						cat_content +=  json.results[i].co2e ? '<input type="checkbox" id="co2e-factor" />'+ Math.round(100*json.results[i].co2e)/100+' kg' : '&nbsp;';
						cat_content +=  '</span>';
						cat_content +=  '<span class="cat-item-energy">';
						cat_content +=  json.results[i].energy ? '<input type="checkbox" id="energy-factor" />'+ Math.round(100*json.results[i].energy)/100+' kwh' : '&nbsp;';
						cat_content +=  '</span>';
						cat_content +=  '<span class="cat-item-water">';
						cat_content +=  json.results[i].water ? '<input type="checkbox" id="water-factor" />'+Math.round(100*json.results[i].water)/100+' L ' : '&nbsp;';
						cat_content +=  '</span>';
						cat_content +=  '<a class="add-map-button">&nbsp;</a>';
						cat_content +=  '<a class="reference-button" target="_blank" href="' + json.results[i].uri + '">&nbsp;</a>';
						cat_content += '</span>';
		    			cat_content += '<div class="clear"></div>';                    
                
		                var new_li = $('<li class="catalog-item"></li>').html(cat_content);                   
		                $(new_li).find('#co2e-factor').click($.proxy(function(evt) {							
							var sc = false;
					        for(var k in this.editor.map.supplychains) {
					            sc = this.editor.map.supplychains[k];
					            break;
					        }
							for (x in sc.stops) {
								if (sc.stops[x] == this.editor.editing) {
									if (sc.stops[x].attributes.footprintmethodology) { if (sc.stops[x].attributes.footprintmethodology.search("\nCO2e factor referenced from: \n" +this.item.ref) != -1) { break; } }
									sc.stops[x].attributes.footprintmethodology = sc.stops[x].attributes.footprintmethodology + "\nCO2e factor referenced from: \n" +this.item.ref;
									break;
								}
							}
					    	Sourcemap.broadcast('supplychain-updated', sc);
		                    this.editor.applyCatalogItem(this.catalog, this.item, this.ref, this.catalog_map);
		                }, {"item": json.results[i], "editor": this.editor, "ref": o.ref, "catalog": o.catalog, "catalog_map": {"osi": {"name": ["title"],"co2e": true, "unit": true, "co2e_reference": true}}}));
		                $(new_li).find('#energy-factor').click($.proxy(function(evt) {
							var sc = false;
					        for(var k in this.editor.map.supplychains) {
					            sc = this.editor.map.supplychains[k];
					            break;
					        }
							for (x in sc.stops) {
								if (sc.stops[x] == this.editor.editing) {
									if (sc.stops[x].attributes.footprintmethodology) { if (sc.stops[x].attributes.footprintmethodology.search("\nEnergy factor referenced from: \n" +this.item.ref) != -1) { break; } }
									sc.stops[x].attributes.footprintmethodology = sc.stops[x].attributes.footprintmethodology + "\nEnergy factor referenced from: \n" +this.item.ref;
									break;
								}
							}
					    	Sourcemap.broadcast('supplychain-updated', sc);
		                    this.editor.applyCatalogItem(this.catalog, this.item, this.ref, this.catalog_map);
		                }, {"item": json.results[i], "editor": this.editor, "ref": o.ref, "catalog": o.catalog, "catalog_map": {"osi": {"name": ["title"],"energy": true, "unit": true, "energy_reference": true}}}));
		                $(new_li).find('#water-factor').click($.proxy(function(evt) {
							var sc = false;
					        for(var k in this.editor.map.supplychains) {
					            sc = this.editor.map.supplychains[k];
					            break;
					        }
							for (x in sc.stops) {
								if (sc.stops[x] == this.editor.editing) {
									if (sc.stops[x].attributes.footprintmethodology) { if (sc.stops[x].attributes.footprintmethodology.search("\nWater factor referenced from: \n" +this.item.ref) != -1) { break; } }
									sc.stops[x].attributes.footprintmethodology = sc.stops[x].attributes.footprintmethodology + "\nWater factor referenced from: \n" +this.item.ref;
									break;
								}
							}
					    	Sourcemap.broadcast('supplychain-updated', sc);
		                    this.editor.applyCatalogItem(this.catalog, this.item, this.ref, this.catalog_map);
		                }, {"item": json.results[i], "editor": this.editor, "ref": o.ref, "catalog": o.catalog, "catalog_map": {"osi": {"name": ["title"],"water": true, "unit": true, "water_reference": true}}}));                
						$(new_li).find('.add-map-button').click($.proxy(function(evt) {
							var sc = false;
					        for(var k in this.editor.map.supplychains) {
					            sc = this.editor.map.supplychains[k];
					            break;
					        }
							for (x in sc.stops) {
								if (sc.stops[x] == this.editor.editing) {
									if (sc.stops[x].attributes.footprintmethodology) { if (sc.stops[x].attributes.footprintmethodology.search("\nFactors referenced from: \n" +this.item.ref) != -1) { break; } }
									sc.stops[x].attributes.footprintmethodology = sc.stops[x].attributes.footprintmethodology + "\nFactors referenced from: \n" +this.item.ref;
									break;
								}
							}
					    	Sourcemap.broadcast('supplychain-updated', sc);
		                    this.editor.applyCatalogItem(this.catalog, this.item, this.ref, this.catalog_map);
		                }, {"item": json.results[i], "editor": this.editor, "ref": o.ref, "catalog": o.catalog, "catalog_map": {"osi": {"name": ["title"],"co2e": true,"energy": true,"water": true, "unit": true, "co2e_reference": true, "energy_reference": true, "water_reference": true}}}));                

					
		                $(new_li).click($.proxy(function(evt) {
		                    this.editor.applyCatalogItem(this.catalog, this.item, this.ref);
		                }, {"item": json.results[i], "editor": this.editor, "ref": o.ref, "catalog": o.catalog}));
	   				
						cat_html.append(new_li);
	            }
			} else {
				cat_html.html("");
			}
			$(this.editor.map_view.dialog).find('.falseclose').click($.proxy(function() {
				//change this by passing this and adding a class
                $(this).parent().parent().parent().parent().width(411);
				$(this).parent().parent().parent().parent().find('#newcatalog').hide();
				$(this).parent().parent().parent().parent().find('#stop-editor').show();
				}));
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
                }, {"o": o, "editor": this.editor}));
                $(this.editor.map_view.dialog).find('.catalog-pager').append(prev_link);
            } else {
				var prev_link = $('<span class="catalog-pager-prev"></span>');
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
            } else {
				var next_link = $('<span class="catalog-pager-next"></span>');
                $(this.editor.map_view.dialog).find('.catalog-pager').append(next_link);
			}
			
			
			// more (toggle between tiny catalog and full catalog)
            if(o.params.curated == "true") {
                var more = $('<span>(more values)</span>').click($.proxy(function() {
                    var o = {};
                    o.editor = this.editor;
                    o.ref = this.o.ref;
                    o.params = Sourcemap.deep_clone(this.o.params);
                    o.params.curated = false;
                    o.catalog = this.o.catalog;
                    this.editor.updateCatalogListing(o);
                }, {"o": o, "editor": this.editor}));
                $(this.editor.map_view.dialog).find('.catalog-pager').append(more);
            } else {
                var more = $('<span>(curate)</span>').click($.proxy(function() {
                    var o = {};
                    o.editor = this.editor;
                    o.ref = this.o.ref;
                    o.params = Sourcemap.deep_clone(this.o.params);
                    o.params.curated = true;
                    o.catalog = this.o.catalog;
                    this.editor.updateCatalogListing(o);
                }, {"o": o, "editor": this.editor}));
                $(this.editor.map_view.dialog).find('.catalog-pager').append(more);
			}

            // search bar
            $(this.editor.map_view.dialog).find('#catalog-search-field').keyup($.proxy(function(evt) {
				console.log("searching...");
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
            $(this.editor.map_view.dialog).find('#catalog-search').bind("submit",function(event){
                event.preventDefault();
            });
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
                var message = '<p>A Youtube link looks like this: <br/>http://www.youtube.com/watch?v=wqeDfKY37Gk</p>';				
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
                var message = '<p>Flickr set ID is the sequence of numbers at the end of a set URL.</p>';
            }
    	}
    	
        $("#edit-media .media-preview").html(message);
}


Sourcemap.Map.Editor.prototype.showCatalog = function(o) {
    var o = o || {};
    o.q = o.q ? o.q : '';
	o.params = {};
	o.params.curated = true;
    o.catalog = o.catalog ? o.catalog : "osi";
    var tscope = {"editor": this, "o": o, "ref": o.ref};
    Sourcemap.template('map/edit/catalog', function(p, txt, th) {  
	 	//$("#edit-catalog").html(th); //Bianca
		$("#stop-editor").hide();
		$("#dialog").width(1020);
		$("#dialog").css("right",10);
		$("#dialog").css("padding",0);
		$("#newcatalog").show();
        $("#newcatalog").html(th);  
        this.editor.updateCatalogListing(this.o);
    }, tscope, tscope);
}

Sourcemap.Map.Editor.prototype.applyCatalogItem = function(cat, item, ref, catalog_map) {
    // TODO: add the unit
/*
    var catalog_map = {
        "osi": {
            "name": ["title"],
            "co2e": true,
            "waste": true,
            "water": true,
            "energy": true,
			"unit": true
        }
    }
*/
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
    this.updateFeature(this.editing, vals);
    this.showEdit(this.map.findFeaturesForStop(this.editing.supplychain_id,this.editing.instance_id).stop);   
  	
    //$("#editor-tabs").tabs('select', 2);
    //$("#editor-tabs").tabs('select', 3);
}

