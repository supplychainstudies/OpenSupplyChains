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

Sourcemap.Map.Base = function(o) {
    this.broadcast('map_base:instantiated', this);
    var o = o || {};
    Sourcemap.Configurable.call(this, o);
    this.instance_id = Sourcemap.instance_id("sourcemap-base");
}

Sourcemap.Map.Base.prototype = new Sourcemap.Configurable();

Sourcemap.Map.Base.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.Base.prototype.defaults = {
    "auto_init": true,
    "map_element_id": 'sourcemap-map-view',
    "banner": true, "watermark": true, "magic_word_list": [
        "youtube:link", "vimeo:link", "soundcloud:id", "twitter:search", "flickr:setid"
    ], "tpl_base_path": Sourcemap.TPL_PATH, "tileset":"cloudmade",
    "tour_order_strategy": "upstream", "position": "0|0|0", "error_color": '#ff0000',
    "locate_user": false, "user_loc": false, "user_loc_color": "#ff0000", "tour": false, 
    "attr_missing_color": Sourcemap.Map.prototype.defaults.default_feature_color,
    "visualization_mode": null, "visualizations": ["co2e","weight","water","energy"],
    "visualization_colors": {"co2e": "#c95f3d", "weight": "#804000", "water": "#1c9acd","energy":"#e2a919"},
    "legend": true, "locked":true,
    "viz_attr_map": {
        "weight": function(st) {
            var val = 0;
            var qty = parseFloat(st.getAttr("qty", 1));
    		var unt = st.getAttr("unit","kg") == "kg" ? 1 : 0;			
            var wgt = parseFloat(st.getAttr("weight"));
            if(!isNaN(qty) && !isNaN(wgt)) val = qty * wgt;
            return val;
        },
        "water": function(st) {
            var val = 0;
            var qty = parseFloat(st.getAttr("qty", 1));
    		var unt = st.getAttr("unit","L") == "L" ? 1 : 0;
            var wgt = parseFloat(st.getAttr("weight"));
            if(st instanceof Sourcemap.Hop) {
                wgt =  wgt * parseFloat(st.gc_distance());
            }
            var fac = parseFloat(st.getAttr("water", 0));
            if(!isNaN(qty) && !isNaN(fac)) val = wgt* qty * fac;
            return val;
        }, 
        "energy": function(st) {
            var val = 0;
            var qty = parseFloat(st.getAttr("qty", 1));
    		var unt = st.getAttr("unit","kWh") == "kWh" ? 1 : 0;
            var wgt = parseFloat(st.getAttr("weight"));
            if(st instanceof Sourcemap.Hop) {
                wgt = wgt * parseFloat(st.gc_distance());
            }
            var fac = parseFloat(st.getAttr("energy", 0));
            if(!isNaN(qty) && !isNaN(fac)) val = wgt* qty * fac;
            return val;
        },
        "co2e": function(st) {
            var val = 0;
            var qty = parseFloat(st.getAttr("qty", 1));
    		var unt = st.getAttr("unit","kg") == "kg" ? 1 : 0;
            var wgt = parseFloat(st.getAttr("weight"));
            if(st instanceof Sourcemap.Hop) {
                wgt = wgt * parseFloat(st.gc_distance());
            }
            var fac = parseFloat(st.getAttr("co2e", 0));
            if(!isNaN(qty) && !isNaN(fac)) val = qty * wgt* fac;
            return val;
        },
        "valueatrisk": function(st) {
            return st.getAttr("varort", 1);
        }

    }
}

Sourcemap.Map.Base.prototype.init = function() {
    this.magic_word_list = this.options.magic_word_list;
    this.viz_attr_map = this.options.viz_attr_map;
    this.initMap();
    this.initDialog();
    this.initEvents();
}

Sourcemap.Map.Base.prototype.initMap = function() {
    this.map = new Sourcemap.Map(this.options.map_element_id);
    this.setActiveArea();
    this.map.activeStatus = false;
    
    Sourcemap.listen('supplychain:loaded', $.proxy(function(evt, smap, sc) {
    	this.toggleTileset(sc);
    	var initpos = this.options.position.split("|");
    	var p = new OpenLayers.LonLat(initpos[1], initpos[0]);
        p.transform(new OpenLayers.Projection("EPSG:4326"), this.map.map.getProjectionObject());
      	if(this.options.position == '0|0|0') {
    		if(sc.stops.length) {	
                this.map.zoomToExtent(this.map.getFeaturesExtent(), false);
    		} else {	
    			this.map.map.setCenter(p, this.map.map.minZoomLevel);		
    		}
        } else {
     		this.map.map.setCenter(p, initpos[2]);
        }
		if(!(sc.stops.length) && sc.editable) {	this.showEditor(); }
		this.loadExternals(sc);
		
		// Process Hash
		var type = window.location.hash.substring(1).split('-')[0];
		if(type != '') {
			if(type == 'stop') {
				var sid = window.location.hash.substring(1);				
				var targetftr = this.map.findFeaturesForStop(sc.instance_id, sid).stop;
			} else if(type == 'hop') {
				//var fid = 'stop-'+window.location.hash.substring(1).split('-')[1];
				//var tid = 'stop-'+window.location.hash.substring(1).split('-')[2];
                var hid = window.location.hash.substring(1);
				var targetftr = this.map.findFeaturesForHopfromHopID(sc.instance_id, hid).hop; 	
			} else if(type == 'cluster') {
                var cid = window.location.hash.substring(1);
                var targetftr = this.map.findFeaturesForCluster(sc.instance_id, cid);
            }
			if(typeof(targetftr) != 'undefined' && targetftr != false) {
				this.map.broadcast('map:feature_selected', this.map, targetftr); 
			}
    	}
        // TODO : Load twice for legend-gradient @map.js , make it for effeciency
        for(var k in this.map.supplychains)
           	this.map.mapSupplychain(k);
            
    }, this));
    $(this.map.map.div).css("position", "relative");

    // TODO: check this update punch
    Sourcemap.listen('map-base-calc-update', $.proxy(function(evt, metric, value) {
        if(value === undefined || value === null) {
            var range = this.calcMetricRange(metric);
            value = range.total;
        }
        var unit = "kg";
        if(metric === "water") unit = "L";
		if(metric === "energy") unit = "kWh";
        var scaled = Sourcemap.Units.scale_unit_value(value, unit, 2);

        this.map.dockControlEl(metric).find('.value').text(scaled.value);
        this.map.dockControlEl(metric).find('.unit').text(scaled.unit);
    	
    }, this));
}

Sourcemap.Map.Base.prototype.initEvents = function() {
    var firstLoad = true;
    Sourcemap.listen('map:supplychain_mapped', $.proxy(function(evt, map, sc) {
        
        if (firstLoad){
            // zoomToExtent upon first load.  this needs to happen here, since 
            // we don't know the geometry of the hops until they are mapped.
            this.map.zoomToExtent(this.map.getFeaturesExtent(), true);

            firstLoad = false;
        }
		if(!(sc.stops.length) && sc.editable) {	this.showEditor(); }
        if(!this.map || this.map !== map) return;
        if(this.options.banner && !($("#banner").length)) this.initBanner();
        // TODO: do calculations here
    	this.updateFilterDisplay(sc);

        for(var vi=0; vi<this.options.visualizations.length; vi++) {
            var v = this.options.visualizations[vi];
            var range = null;
            var attr_nm = this.viz_attr_map[v];
            for(var k in this.map.supplychains) {
                var sc = this.map.supplychains[k];
                if(range === null) range = sc.attrRange(attr_nm);
                else {
                    var tmprange = sc.attrRange(viz_nm);
                    if(tmprange.min !== null)
                        range.min = Math.min(range.min, tmprange.min);
                    if(tmprange.max !== null)
                        range.max = Math.max(range.max, tmprange.max);
                    if(tmprange.total !== null) {
                        range.total += tmprange.total;
                    }
                }
            }
            Sourcemap.broadcast('map-base-calc-update', v, range.total);
        }
		$("circle").hover($.proxy(function(event) {
			var thefeature = event.currentTarget._featureId; 
			var tieredsc = new Sourcemap.Supplychain.makeTiers(sc);
			// first iterate through the openlayers map object to find the right circle
			for (var x in this.map.map.layers) {
				for (var y in this.map.map.layers[x].features) {
					if (this.map.map.layers[x].features[y].id == thefeature) {
						// Found the right openlayers feature. get data.
						for (var z in tieredsc.stops) {
							if (tieredsc.stops[z].instance_id == this.map.map.layers[x].features[y].attributes.stop_instance_id) {
								Sourcemap.Map.Base.isolateNetworkEffect(this, tieredsc, 0.1, tieredsc.stops[z].instance_id);
								break;
							}
						}
						break;
					}
				}
			}
			//var id = $(this).attr("id");
			//for () {
				
			//}
		}, this));
		$("circle").mouseover($.proxy(function(event) {
			Sourcemap.Map.Base.defaultEffect();
			var thefeature = event.currentTarget._featureId; 
			var tieredsc = new Sourcemap.Supplychain.makeTiers(sc);
			// first iterate through the openlayers map object to find the right circle
			for (var x in this.map.map.layers) {
				for (var y in this.map.map.layers[x].features) {
					if (this.map.map.layers[x].features[y].id == thefeature) {
						// what if its a cluster? Make sure to iterate through all the cluster stops
						if (typeof(this.map.map.layers[x].features[y].cluster) != "undefined") {
							for (var a in this.map.map.layers[x].features[y].cluster) {
								for (var z in tieredsc.stops) {
									if (typeof(this.map.map.layers[x].features[y].cluster[a].attributes) != "undefined") {
										if (tieredsc.stops[z].instance_id == this.map.map.layers[x].features[y].cluster[a].attributes.stop_instance_id) {
											Sourcemap.Map.Base.isolateNetworkEffect(this, tieredsc, 0.1, tieredsc.stops[z].instance_id);
											break;
										}
									}
								}								
							}
						} else {
							// Found the right openlayers feature. get data.
							for (var z in tieredsc.stops) {
								if (tieredsc.stops[z].instance_id == this.map.map.layers[x].features[y].attributes.stop_instance_id) {
									Sourcemap.Map.Base.isolateNetworkEffect(this, tieredsc, 0.1, tieredsc.stops[z].instance_id);
									break;
								}
							}
							break;
						}
					}
				}
			}
			//var id = $(this).attr("id");
			//for () {
				
			//}
		}, this));
		$("circle").mouseout($.proxy(function() {
			Sourcemap.Map.Base.defaultEffect();
		}, this));
		/*
	    Sourcemap.listen('map:feature_hover', $.proxy(function(evt, map, ftr) {
			console.log("meow");
	        var x = new Sourcemap.Supplychain.makeTiers(sc);		
	    }, this));
		*/
    }, this));

    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
        if(ftr.cluster) {
            window.location.hash = ftr.attributes.cluster_instance_id; // + ftr.attributes.supplychain_instance_id;
            this.showClusterDetails(ftr);
        } else if(ftr.attributes.stop_instance_id && (!(map.editor) || this.options.locked)) {
            this.showStopDetails(
                ftr.attributes.stop_instance_id, ftr.attributes.supplychain_instance_id
            );
            window.location.hash = ftr.attributes.stop_instance_id; // + ftr.attributes.supplychain_instance_id;
        } else if (ftr.attributes.hop_instance_id && (!(map.editor) || this.options.locked)) {			
            window.location.hash = ftr.attributes.hop_instance_id; // + ftr.attributes.supplychain_instance_id;
            this.showHopDetails(
                ftr.attributes.hop_instance_id, ftr.attributes.supplychain_instance_id
            );
        }
    }, this));

    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
        if(ftr.cluster) {
            this.showClusterDetails(ftr);
        } else if(ftr.attributes.stop_instance_id && (!(map.editor) || this.options.locked)) {
            this.showStopDetails(
                ftr.attributes.stop_instance_id, ftr.attributes.supplychain_instance_id
            );
        } else if (ftr.attributes.hop_instance_id && (!(map.editor) || this.options.locked)) {			
            this.showHopDetails(
                ftr.attributes.hop_instance_id, ftr.attributes.supplychain_instance_id
            );
        }
    }, this));
	
    Sourcemap.listen('map:feature_unselected', $.proxy(function(evt, map, ftr) {
        this.hideDialog();
    }, this));
    
    this.map.map.events.register("moveend", this.map.map, $.proxy(function(e) {
        // I would prefer to use "resize" here, but it doesn't work.
        this.setActiveArea();
    }, this));
/*
    this.map.map.events.register('zoomend', this.map.map.events, $.proxy(function(e) {
        this.toggleVisualization();
    }, this));
*/  
    /* disabled until we decide on show/hide behavior
    this.map.map.events.register("mousemove", this.map.map.events, $.proxy(function(e) {
        var activeStatus = false;
        if (e.offsetY < this.map.activeArea.top || e.offsetY > (this.map.activeArea.bottom + this.map.activeArea.h)){
            var activeStatus = false;
        } else {
            var activeStatus = true;
            this.stopControlTimer();
            this.startControlTimer();
        }

        // Check if active status has changed
        if (activeStatus != this.map.activeStatus){
            if (activeStatus == true){
                this.enableControlTimer();
            } else {
                this.showControls();
                this.disableControlTimer();
            }
            this.map.activeStatus = activeStatus;
        }
    }, this));
    */
}

Sourcemap.Map.Base.svgToSc = function (thismap, sc, svgid) {
	// first iterate through the openlayers map object to find the right circle
	for (var x in thismap.map.map.layers) {
		for (var y in thismap.map.map.layers[x].features) {
			if (typeof(thismap.map.map.layers[x].features[y].geometry) != "undefined") {
				// Case: Arrows and Circles
				if (thismap.map.map.layers[x].features[y].geometry.id == svgid) {
					// Found the right openlayers feature. get data.
					// Case: Circles
					if (typeof(thismap.map.map.layers[x].features[y].attributes.stop_instance_id) != "undefined") {
						return (thismap.map.map.layers[x].features[y].attributes.stop_instance_id).toString();	
					} 
					// Case: Arrows
					else if (typeof(thismap.map.map.layers[x].features[y].attributes.hop_instance_id) != "undefined") {
						return (thismap.map.map.layers[x].features[y].attributes.hop_instance_id).toString();	
					}
					// Case: Cluster 
					else if (typeof(thismap.map.map.layers[x].features[y].cluster) != "undefined") {
						var cluster_list = new Array();
						var cluster_count = 0;
						for (var c in thismap.map.map.layers[x].features[y].cluster) {
							if (typeof(thismap.map.map.layers[x].features[y].cluster[c].attributes) != "undefined") {
								cluster_list[cluster_count] = thismap.map.map.layers[x].features[y].cluster[c].attributes.stop_instance_id;		
								cluster_count++;
							}
						}
						return cluster_list;
					}
					return "not found";
					break;
				} 
				// Case: Text 
				else if (svgid.search("_label") && thismap.map.map.layers[x].features[y].id == svgid.replace("_label","")) {
					// Found the right openlayers feature. get data.
					if (typeof(thismap.map.map.layers[x].features[y].attributes.stop_instance_id) != "undefined") {
						return (thismap.map.map.layers[x].features[y].attributes.stop_instance_id).toString();	
					} else if (typeof(thismap.map.map.layers[x].features[y].cluster) != "undefined") {
						var cluster_list = new Array();
						var cluster_count = 0;
						for (var c in thismap.map.map.layers[x].features[y].cluster) {
							if (typeof(thismap.map.map.layers[x].features[y].cluster[c].attributes) != "undefined") {
								cluster_list[cluster_count] = thismap.map.map.layers[x].features[y].cluster[c].attributes.stop_instance_id;		
								cluster_count++;
							}
						}
						return cluster_list;	
					}
					return "not found";
					break;
				}				
				// Case: Lines
				else if (typeof(thismap.map.map.layers[x].features[y].geometry.components) != "undefined") {
					for (var comp in thismap.map.map.layers[x].features[y].geometry.components)	{
						if (thismap.map.map.layers[x].features[y].geometry.components[comp].id == svgid && typeof(thismap.map.map.layers[x].features[y].attributes.hop_instance_id) != "undefined") {
							return (thismap.map.map.layers[x].features[y].attributes.hop_instance_id).toString();	
						}
					}			
				}
			}
		}
	}
	return "not found";
}
Sourcemap.Map.Base.filterMap = function () {
	
}
Sourcemap.Map.Base.defaultEffect = function () {
    $("circle")
    .css("opacity",1);
    $("text")
    .css("opacity",1);
	$("polyline")
    .css("opacity",1);
	$("polygon")
    .css("opacity",1);
}
Sourcemap.Map.Base.isolateNetworkEffect = function (thismap, sc, opacity, i) {
	opacity = 0.15;
            $("circle")
            .filter(function(d){
                    var updown = update_updown(i);
					var theid = $(this).attr("id");
					var x = Sourcemap.Map.Base.svgToSc(thismap, sc, theid);
					if (x != "not found" && typeof(x) == "string") {
						for (var y in sc.stops) {
							if (sc.stops[y].instance_id == x) {
								if (parseFloat($(this).css("opacity")).toFixed(2) != 0.99) {
									return check_stops(sc.stops[y].instance_id,i, updown);
								}
							}
						}
					} 
					// Case clusters
					else if (x != "not found" && typeof(x) == "object") {
						var bool_stops = true; 
						for (var cs in x) {
							if (typeof(x[cs]) == "string") {
								for (var y in sc.stops) {
									if (sc.stops[y].instance_id == x[cs]) {
										bool_stops = bool_stops && check_stops(sc.stops[y].instance_id,i, updown);
									}
								}
							}
						}
						if (parseFloat($(this).css("opacity")).toFixed(2) != 0.99) {
							return bool_stops;
						}
					}
					return false;				                  
	        })
            .css("opacity",opacity);

			// 			
            $("circle")
            .filter(function(d){
               if (parseFloat($(this).css("opacity")).toFixed(2) != opacity) 
					return true;
				else 
					return false;			                
	        })
            .css("opacity",0.99);

            $("text")
            .filter(function(d){
                    var updown = update_updown(i);
					var theid = $(this).attr("id");
					var x = Sourcemap.Map.Base.svgToSc(thismap, sc, theid);
					if (x != "not found" && typeof(x) == "string") {
						for (var y in sc.stops) {
							if (sc.stops[y].instance_id == x) {
								if (parseFloat($(this).css("opacity")).toFixed(2) != 0.99) {
									return check_stops(sc.stops[y].instance_id,i, updown);
								}
							}
						}
					}
					
					// Case clusters
					else if (x != "not found" && typeof(x) == "object") {
						var bool_stops = true; 
						for (var cs in x) {
							if (typeof(x[cs]) == "string") {
								for (var y in sc.stops) {
									if (sc.stops[y].instance_id == x[cs]) {
										bool_stops = bool_stops && check_stops(sc.stops[y].instance_id,i, updown);
									}
								}
							}
						}
						if (parseFloat($(this).css("opacity")).toFixed(2) != 0.99) {
							return bool_stops;
						}
					}
					return false;				                  
	        })
            .css("opacity",opacity);

            $("text")
            .filter(function(d){
               if (parseFloat($(this).css("opacity")).toFixed(2) != opacity) 
					return true;
				else 
					return false;			                
	        })
            .css("opacity",0.99);

			$("polyline")
			.filter(function(d){
                    var updown = update_updown(i);
					var theid = $(this).attr("id");
					var x = Sourcemap.Map.Base.svgToSc(thismap, sc, theid);
					if (x != "not found") {
						for (var y in sc.hops) {
							if (sc.hops[y].instance_id == x) {
								if (parseFloat($(this).css("opacity")).toFixed(2) != 0.99) {
									return check_hops(sc.hops[y],i, updown);
								}
							}
						}
					}
					return false;				                  
	        })
            .css("opacity",opacity);

            $("polyline")
            .filter(function(d){
               if (parseFloat($(this).css("opacity")).toFixed(2) != opacity) 
					return true;
				else 
					return false;			                
	        })
            .css("opacity",0.99);

			$("polygon")
			.filter(function(d){
                    var updown = update_updown(i);
					var theid = $(this).parent().attr("id");
					if (theid.search('Point')) {
						var x = Sourcemap.Map.Base.svgToSc(thismap, sc, theid);
						if (x != "not found") {
							for (var y in sc.hops) {
								if (sc.hops[y].instance_id == x) {
									if (parseFloat($(this).css("opacity")).toFixed(2) != 0.99) {
										return check_hops(sc.hops[y],i, updown);
									}
								}
							}
						}
						return false;	
					}			             
	        })
			//.transition()
            .css("opacity",opacity);

            $("polygon")
            .filter(function(d){
               if (parseFloat($(this).css("opacity")).toFixed(2) != opacity) 
					return true;
				else 
					return false;			                
	        })
            .css("opacity",0.99);

	function update_updown(select)
    {        
        var upstream = [];
        var downstream = [];
		var x = 0;
		for (x in sc.tier_list) {
			if (sc.tier_list[x].instance == select) {				
				break;
			}
		}
		if (typeof(sc.tier_list[x].instance) != "undefined") {
        	upstream.push(sc.tier_list[x].instance);
	        downstream.push(sc.tier_list[x].instance);
	        //downstream ~max
	        (function(){
	        for(var j=0,down_max=downstream.length;j<down_max;j++){
	            for(var h=0,max=sc.hop_list.length;h<max;h++){                        
	                if(sc.hop_list[h].from==downstream[j]){
	                    //prevent circular supplychain
	                    if(jQuery.inArray(sc.hop_list[h].to,downstream)>0)
	                        continue;
	                    downstream.push(sc.hop_list[h].to);
	                    down_max = downstream.length; 
	                }
	            }
	        }
	        })(); // end of funciton
	        //upstream
	        (function(){
	        for(var j=0,up_max=upstream.length;j<up_max;j++){
	            for(var h=0,max=sc.hop_list.length;h<max;h++){                        
	                if(sc.hop_list[h].to==upstream[j]){
	                    //prevent circular supplychain
	                    if(jQuery.inArray(sc.hop_list[h].from,upstream)>0)
	                        continue;
	                    upstream.push(sc.hop_list[h].from);
	                    up_max = upstream.length; 
	                }
	            }
	        }
	      })(); // end of function
		}
		var updown = {};
		return {'upstream':upstream, 'downstream':downstream};
    }

    function check_hops(hop,select, updown)
    {
	// hop should be a whole hop object. It should be the hop that is being tested for whether it included in the same branch that stop "select"
	// select is the stop id of the stop that has been moused over. it should look like "stop-1"
		var upstream = updown.upstream;
		var downstream = updown.downstream;
		var x = 0;
		for (x in sc.tier_list) {
			if (sc.tier_list[x].instance == select) {				
				break;
			}
		}
        // hops that connect to select stop
        if(hop.from_stop_id==sc.tier_list[x].instance||hop.to_stop_id==sc.tier_list[x].instance)
            return false;
        if(jQuery.inArray(hop.from_stop_id,upstream)>0){    
            if(jQuery.inArray(hop.to_stop_id,upstream)>0)
                return false;
        }
        if(jQuery.inArray(hop.from_stop_id,downstream)>0){    
            if(jQuery.inArray(hop.to_stop_id,downstream)>0)
                return false;
        }
        return true;    
    }
	
    function check_stops(i,select, updown)
    {
	// i is the stop id of the stop that is being tested for whether it included in the same branch that stop "select"
	// select is the stop id of the stop that has been moused over. it should look like "stop-1"	
	var upstream = updown.upstream;
	var downstream = updown.downstream;
        // false : not change opacity 
		var x = 0;
		for (x in sc.tier_list) {
			if (sc.tier_list[x].instance == i) {				
				break;
			}
		}
        if(i==select)
            return false;
        if(jQuery.inArray(sc.tier_list[x].instance,downstream)>0)
            return false;
        if(jQuery.inArray(sc.tier_list[x].instance,upstream)>0) 
            return false;
        //else return true
        return true;         
    }

}

Sourcemap.Map.Base.prototype.setActiveArea = function(){
    // The active area is the part of the viewport that isn't covered by menus. 
    
    var topOffset = $('#banner').height();
    var bottomOffset = $('#sourcemap-dock').height();

    var wholeArea = this.map.map.getSize();
    var activeArea = {
        'h': wholeArea.h - topOffset - bottomOffset,
        'w': wholeArea.w,
        'top': topOffset + 20,
        'bottom': bottomOffset + 20 
    }
   
    this.map.activeArea = activeArea;
}

Sourcemap.Map.Base.prototype.initBanner = function(sc) {
    this.banner_div = $(this.map.map.div).find('#banner').length ? 
        $(this.map.map.div).find('#banner') : false;
    if(!this.banner_div) {
        if(!$.browser.msie){
        this.banner_div = $('<div id="banner"></div>');
        }
        else{
            this.banner_div = $('<div id="banner" style="background:#f0f0f0"></div>');
        }
        $(this.map.map.div).append(this.banner_div);
    }
    if(!sc) {
        // TODO: this is bad, but it's worst case
        sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }
    }
    var cb = $.proxy(function(p, tx, th) {
        $(this.banner_div).html(th);
        $(this.banner_div).find('.banner-share-link').click(function(){
            $.scrollTo('#sidebar', 600);
        });

        $(this.banner_div).find('.banner-lock-link').click($.proxy(function(){
	    	if(this.options.locked) {
				this.showEditor();
			} else {
				this.hideEditor();
			}
        }, this));

        $(this.banner_div).find('.banner-favorite-link').click($.proxy(function() { 
            this.favorite();
        }, this));
         $.ajax({"url": 'services/favorites', "type": "GET",
            "success": $.proxy(function(resp) {
               for(var k in resp) {
                   if(resp[k].id == sc.remote_id) {
                       $("#banner-favorite").addClass("marked");
                   }
               }                   
            }, this),
            "error": $.proxy(function(resp) {
                if(resp.status == 403) {
                    $("#banner-favorite a.banner-favorite-link")
                        .attr("href", "register");
                }
            },this)
        });
		$(this.banner_div).find('#map-search').keyup($.proxy(function() { 
            this.searchFilterMap();
        }, this));
        $(this.banner_div).find("#map-search").blur(function(){
            setTimeout(function(){
                if(window.pageYOffset !== 0) return;
                window.scrollTo(0,window.pageYOffset + 1);
            },100);
        });

        // truncate here
        var bannerwidth = $(this.banner_div).width();
        var sumwidth=0;
        $(this.banner_div).find("#banner-content").find("div:not(#banner-summary):visible").each(function(){
            sumwidth += $(this).width() + 44;
        });

        var summarywidth = bannerwidth - sumwidth; 
        $(this.banner_div).find("#banner-summary").css("max-width",summarywidth);
        $(this.banner_div).find("#banner-summary").css("width",summarywidth);
        Sourcemap.truncate_one_string("#banner-summary");
    }, this);

	var s = {"sc":sc, "lock":this.options.locked};
    Sourcemap.template('map/banner', cb, s);

   

    if(this.options.watermark) {
        this.watermark = $('<a href="/"><div id="watermark"></div></a>');
        $(this.map.map.div.extras).append(this.watermark);
    }
    return this;
}

Sourcemap.Map.Base.prototype.getControls = function(){
    // return a list of "controls" that should be hidden
    var controls = [
        $('#banner'),
        $('#sourcemap-dock'),
        $('#sourcemap-gradient')
    ];
    return controls;
}

Sourcemap.Map.Base.prototype.toggleControls = function(){
    // include all elements that should be toggled
    $(this.getControls()).each(function(){
        $(this).fadeToggle();
    });
}

Sourcemap.Map.Base.prototype.showControls = function(){
    $(this.getControls()).each(function(){
        $(this).fadeIn();
    });
}

Sourcemap.Map.Base.prototype.hideControls = function(){
    $(this.getControls()).each(function(){
        $(this).fadeOut();
    });
}

Sourcemap.Map.Base.prototype.startControlTimer = function(){
    if (this.controlTimerEnabled){
        this.controlTimer = window.setTimeout($.proxy(function() {
            this.hideControls();
        }, this),1000);
    }
}

Sourcemap.Map.Base.prototype.stopControlTimer = function(){
    clearTimeout(this.controlTimer);
}

Sourcemap.Map.Base.prototype.disableControlTimer = function(){
    this.stopControlTimer();
    this.controlTimerEnabled = false;
}

Sourcemap.Map.Base.prototype.enableControlTimer = function(){
    this.controlTimerEnabled = true;
}

Sourcemap.Map.Base.prototype.initDialog = function() {   
    // set up dialog
    if(!this.dialog) {
        if(!$.browser.msie){ 
        this.dialog = $('<div id="dialog"></div>');
        }else{
            this.dialog = $('<div id="dialog" style="background:white"></div>');
        }        
        $(this.map.map.div).append(this.dialog);
    } else $(this.dialog).empty();
    $(this.dialog).removeClass("called-out");
    this.dialog_content = $('<div id="dialog-content"></div>');
    this.dialog.append(this.dialog_content);

}

Sourcemap.Map.Base.prototype.loadExternals = function(sc) {
	/* Load Geojson feeds */
	if(sc.attributes["sm:ext:geojson"]) {
		if(typeof(sc.attributes["sm:ext:geojson"]) == "string") {
			var geofeeds = [sc.attributes["sm:ext:geojson"]];
		} else { var geofeeds = sc.attributes["sm:ext:geojson"]; }
	
		for(var i in geofeeds) {
			var geojson = new OpenLayers.Layer.GML(geofeeds[i], geofeeds[i], {
		            format: OpenLayers.Format.GeoJSON, 
		            projection: new OpenLayers.Projection("EPSG:4326")
			});
		}
		this.map.map.addLayers([geojson]);
	}
}

Sourcemap.Map.Base.prototype.updateStatus = function(msg, cls) {
    $(this.banner_div).children().clearQueue();
    var cls = cls || false;
    var newmsg = $('<div></div>').addClass("msg").text(msg);
    if(cls) newmsg.addClass(cls);
    $('.map-status').text('').empty().append(newmsg);
    $('.map-status .msg').fadeTo(5000, 0);
    return this;
}

Sourcemap.Map.Base.prototype.showEditor = function() {
	this.options.locked = false;
	$('#banner-lock').removeClass('locked');
	$('.editable-options').css('display','block');
	$('.addstop').css("display","block");	
	if(this.dialog) {
		this.hideDialog();
	}
}
Sourcemap.Map.Base.prototype.hideEditor = function() {
	this.options.locked = true;
	$('#banner-lock').addClass('locked');
	$('.editable-options').css('display','none');
	$('.addstop').css("display","none");
	if(this.dialog) {
		this.hideDialog();
	}
}
Sourcemap.Map.Base.prototype.showDialog = function(mkup) {
    if(this.dialog) {
        this.initDialog();
        $(this.dialog_content).html(mkup);
        $(this.dialog_content).find(".close").click($.proxy(function() { this.hideDialog(); }, this));
        
        var fade = $(this.dialog).css("display") == "block" ? 0 : 100;
        $(this.dialog).fadeIn(fade, function() {});
    }
}

Sourcemap.Map.Base.prototype.hideDialog = function(notrigger) {
    if(this.dialog) {
        $(this.dialog).hide();
        if(!notrigger) {
            this.map.controls["select"].unselectAll();
			//this.searchFilterMap();
            Sourcemap.broadcast('sourcemap-base-dialog-close', 
                this, this.map.editor ? $(this.dialog).find("form").serializeArray() : false
            );
        }
        this.dialog_content.empty();
    }
}

Sourcemap.Map.Base.prototype.showStopDetails = function(stid, scid) {   
    // load stop details template and show in detail pane
    var sc = this.map.supplychains[scid];
    var stop = sc.findStop(stid);
    var f = this.map.stopFeature(stop);

    for(var i in this.magic_word_list) {
    	if(stop.getAttr(this.magic_word_list[i], false)) {
    		if(!(stop.magic)) { stop.magic = {}; }
    		stop.magic[this.magic_word_list[i]] = stop.getAttr(this.magic_word_list[i], false);
    	}
    }
    
    // load template and render TODO: make this intelligible
    Sourcemap.template('map/details/stop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);
			Sourcemap.Map.Base.setDetails();   
            // Sets up zoom on click
            $(this.base.dialog_content).find('.dot')
                .css({'cursor': 'pointer'})
                .click($.proxy(function(evt) {
                    this.base.map.map.moveTo(this.base.getFeatureLonLat(this.feature));
                    this.base.map.map.zoomTo(this.base.map.map.maxZoomLevel);
                }, this));
  
        }, 
        {"stop": stop, "supplychain": sc, 'base': this, "feature":f},
        {"base": this, "stop": stop, "supplychain": sc, "feature": f},
        this.options.tpl_base_path
    );
    // this.map.map.panTo(this.getFeatureLonLat(f));

}

Sourcemap.Map.Base.prototype.showClusterDetails = function(cluster) {    
            $(this.dialog_content).empty();
            var cluster_id = cluster.attributes.cluster_instance_id;
            var chtml = $("<div id='"+cluster_id+"' class='cluster'></div>");

            for(var i in cluster.cluster) {
                if(cluster.cluster[i].attributes==undefined)
                    break;
                var title = cluster.cluster[i].attributes.title ?
                    cluster.cluster[i].attributes.title.substring(0,36) : "";
                title += title.length == 36 ? "..." : "";
                var address = cluster.cluster[i].attributes.placename || cluster.cluster[i].attributes.address;
                address = address ? address.substring(0,46) : "";
                address += address.length == 46 ? "..." : "";
                
                var stop_id = cluster.cluster[i].attributes.stop_instance_id;

                var new_citem = $("<div id='target-"+stop_id+"' class='cluster-item'>"
    								+"<div class='dot' style='background:"
    								+cluster.cluster[i].attributes.color+"'></div><a><h2>"
    								+title+"</h2><h3 class='placename'>"
    								+address+"</h3></a></div>"
    							);
                chtml.append(new_citem);        
            }
            this.showDialog(chtml);
            $(this.dialog).find(".cluster-item").click($.proxy(function(evt) {
                var sid = $(evt.currentTarget).attr("id").substring(7);
                var scid = null;
                for(scid in this.map.supplychains) break;
                var sftr = this.map.stop_features[scid][sid].stop;
                this.map.broadcast('map:feature_selected', this.map, sftr); 

            },{"map":this.map, "cluster":cluster}));
}

Sourcemap.Map.Base.prototype.showHopDetails = function(hid, scid) {
    var sc = this.map.supplychains[scid];
    var hop = sc.findHop(hid);
    var f = this.map.hopFeature(scid, hid);

    for(var i in this.magic_word_list) {
    	if(hop.getAttr(this.magic_word_list[i], false)) {
    		if(!(hop.magic)) { hop.magic = {}; }
    		hop.magic[this.magic_word_list[i]] = hop.getAttr(this.magic_word_list[i], false);
    	}
    }
        
    // load template and render TODO: make this intelligible
    Sourcemap.template('map/details/hop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);
			Sourcemap.Map.Base.setDetails();                
        }, 
        {"hop": hop, "supplychain": sc, 'base': this},
        {"base": this, "hop": hop, "supplychain": sc},
        this.options.tpl_base_path
    );

    // this.map.map.panTo(this.getFeatureLonLat(f));
}

Sourcemap.Map.Base.prototype.setDetails = function() {
	var h = this.base.map.activeArea.h -55;
	// First, find out how much height is already occupied 
	$(this.base.dialog_content).find('.placename').each(function() {
		h = h - parseInt($(this).css('height').replace("px","")) - parseInt($(this).css('padding-top').replace("px","")) - parseInt($(this).css('padding-bottom').replace("px","")) - parseInt($(this).css('margin-top').replace("px","")) - parseInt($(this).css('margin-bottom').replace("px",""));
	});
	$(this.base.dialog_content).find('.title').each(function() {
		h = h - parseInt($(this).css('height').replace("px","")) - parseInt($(this).css('padding-top').replace("px","")) - parseInt($(this).css('padding-bottom').replace("px","")) - parseInt($(this).css('margin-top').replace("px","")) - parseInt($(this).css('margin-bottom').replace("px",""));
	});
	$(this.base.dialog_content).find('.accordion-title').each(function() {
		h = h - parseInt($(this).css('height').replace("px","")) - parseInt($(this).css('padding-top').replace("px","")) - parseInt($(this).css('padding-bottom').replace("px","")) - parseInt($(this).css('margin-top').replace("px","")) - parseInt($(this).css('margin-bottom').replace("px",""));
	});
	// Each accordion body can be the size of the leftover space
    var accordion_body = $(this.base.dialog_content).find('.accordion-body')
    var len = accordion_body.length;
	accordion_body.each(function(index) {
		var thissize = parseInt($(this).css('height').replace("px","")) + parseInt($(this).css('padding-bottom').replace("px","")) + parseInt($(this).css('padding-top').replace("px",""));
		if (thissize > h) {	
			var newsize = h - parseInt($(this).css('padding-bottom').replace("px","")) - parseInt($(this).css('padding-top').replace("px",""));
			$(this).css('height',newsize+"px");
			$(this).css('overflow',"auto");
		}  
		$(this).hide();
        if (index == len-1)
            $(this).addClass('last');
	});
	// h is all the room we have to open stuff in
	var reduced_height = h;
	// If there's a media accordion (and there's enough room to show it), show it
	$(this.base.dialog_content).find('#dialog-media').each($.proxy(function(num,evt) {	
		var e = evt;
		//console.log(evt);
		var val = parseInt($(e).css('height').replace('px',"")) + parseInt($(e).css('padding-top').replace('px',"")) + parseInt($(e).css('padding-bottom').replace('px',""));		
		if (reduced_height >= val) {
			reduced_height = reduced_height - val;
			$(e).prev().find('.arrow').addClass("arrowopen");
			$(e).show();
			
			// Count number of media. if there is only one, don't show navigation
			var count = 0;
			$(this.base.dialog_content).find('.navigation-item').each($.proxy(function(evt) {
				count++;
            }, this));			
			if (count == 1) {
				$(this.base.dialog_content).find("#dialog-media-navigation").css("display","none");
			}
			// the first media object should play
			$(this.base.dialog_content).find('.navigation-item').first().each($.proxy(function(num,evt) {
                var target = evt.id.split('-').pop().replace(":","-");
				if(target == "youtube-link") { 
		            $("#dialog-media-content").html(Sourcemap.MagicWords.content.youtube.link(this.stop.magic["youtube:link"]));
		        } else if(target == "vimeo-link") {
		            $("#dialog-media-content").html(Sourcemap.MagicWords.content.vimeo.link(this.stop.magic["vimeo:link"]));
		        } else if(target == "soundcloud-id") {
		            $("#dialog-media-content").html(Sourcemap.MagicWords.content.soundcloud.id(this.stop.magic["soundcloud:id"]));
		        } else if(target == "twitter-search") {	
		            $("#dialog-media-content").html(Sourcemap.MagicWords.content.twitter.search(this.stop.magic["twitter:search"]));
		        } else if(target == "flickr-setid") {
		            $("#dialog-media-content").html('<div id="flickr-photoset-' + this.stop.magic["flickr:setid"] + '">' + Sourcemap.MagicWords.content.flickr.setid.call(this.embed, this.stop.magic["flickr:setid"]) + '</div> ');
		        }
            }, this));
		}					
	}, this));
	$(this.base.dialog_content).find('#dialog-description').each(function() {	
		var val = parseInt($(this).css('height').replace('px',"")) + parseInt($(this).css('padding-top').replace('px',"")) + parseInt($(this).css('padding-bottom').replace('px',""));		
		if (reduced_height >= val) {
			reduced_height = reduced_height - val;
			$(this).prev().find('.arrow').addClass("arrowopen");
			$(this).show();
		}					
	});
	$(this.base.dialog_content).find('#dialog-footprint-body').each(function() {	
		var val = parseInt($(this).css('height').replace('px',"")) + parseInt($(this).css('padding-top').replace('px',"")) + parseInt($(this).css('padding-bottom').replace('px',""));		
		if (reduced_height >= val) {
			reduced_height = reduced_height - val;
			$(this).prev().find('.arrow').addClass("arrowopen");
			$(this).show();
		}					
	});
	$(this.base.dialog_content).find('.accordion .accordion-title').click($.proxy(function(evt) {
		var e = evt.target;
		// figure out if the accordion is already open
		var open = $(e).next().is(":visible");
		// Close everything visible
		$('.accordion-body:visible').each(function() {
			if ($(this).attr("id") == "dialog-media")
				$(this).hide();
			else 
				$(this).slideToggle('fast');
		});
		
		$(this.base.dialog_content).find('.accordion-title').find('.arrow').removeClass('arrowopen');
		// if it wasnt open. open it
		if (open == false) {
			$(e).next().slideToggle('fast');
			$(e).find('.arrow').addClass('arrowopen');
			if ($(e).next().attr("id") == "dialog-media") {
				// Count number of media. if there is only one, don't show navigation
				var count = 0;
				$(e).next().find('.navigation-item').each($.proxy(function(evt) {
					count++;
	            }, this));			
				if (count == 1) {
					$(this.base.dialog_content).find("#dialog-media-navigation").css("display","none");
				}
				// the first media object should play
				$(e).next().find('.navigation-item').first().each($.proxy(function(num,evt) {
	                var target = evt.id.split('-').pop().replace(":","-");
					if(target == "youtube-link") { 
			            $("#dialog-media-content").html(Sourcemap.MagicWords.content.youtube.link(this.stop.magic["youtube:link"]));
			        } else if(target == "vimeo-link") {
			            $("#dialog-media-content").html(Sourcemap.MagicWords.content.vimeo.link(this.stop.magic["vimeo:link"]));
			        } else if(target == "soundcloud-id") {
			            $("#dialog-media-content").html(Sourcemap.MagicWords.content.soundcloud.id(this.stop.magic["soundcloud:id"]));
			        } else if(target == "twitter-search") {	
			            $("#dialog-media-content").html(Sourcemap.MagicWords.content.twitter.search(this.stop.magic["twitter:search"]));
			        } else if(target == "flickr-setid") {
			            $("#dialog-media-content").html('<div id="flickr-photoset-' + this.stop.magic["flickr:setid"] + '">' + Sourcemap.MagicWords.content.flickr.setid.call(this.embed, this.stop.magic["flickr:setid"]) + '</div> ');
			        }
	            }, this));
			}
		}				
		return false;
	}, this));
	// Sets up content-nav behavior
    $(this.base.dialog_content).find('.navigation-item').click($.proxy(function(evt) {
        var target = evt.target.id.split('-').pop().replace(":","-");
		$("#dialog-media").find(".navigation-item").removeClass("selected");
		$(evt.target).addClass("selected");
        // for multiple media item
		//$("#dialog-media").children("iframe, object, embed, div.media-object").css("left","-1000px");
		//$("#dialog-media").children("."+target).css("left","0");
		
		if(target == "youtube-link") { 
            $("#dialog-media-content").html(Sourcemap.MagicWords.content.youtube.link(this.stop.magic["youtube:link"]));
        } else if(target == "vimeo-link") {
            $("#dialog-media-content").html(Sourcemap.MagicWords.content.vimeo.link(this.stop.magic["vimeo:link"]));
        } else if(target == "soundcloud-id") {
            $("#dialog-media-content").html(Sourcemap.MagicWords.content.soundcloud.id(this.stop.magic["soundcloud:id"]));
        } else if(target == "twitter-search") {	
            $("#dialog-media-content").html(Sourcemap.MagicWords.content.twitter.search(this.stop.magic["twitter:search"]));
        } else if(target == "flickr-setid") {
            $("#dialog-media-content").html('<div id="flickr-photoset-' + this.stop.magic["flickr:setid"] + '">' + Sourcemap.MagicWords.content.flickr.setid.call(this.embed, this.stop.magic["flickr:setid"]) + '</div> ');
        }
    }, this));
}

Sourcemap.Map.Base.prototype.showLocationDialog = function(msg) {
    var msg = msg ? msg : false;
    Sourcemap.template("map/location", function(p, txt, th) {
        this.showDialog(th, true);
        $(this.dialog).find('#update-user-loc').click($.proxy(function(evt) {
            var new_loc = $(this.base.dialog).find('#new-user-loc').val();
            if(this.base.user_loc && (new_loc === this.base.user_loc.placename)) {
                // pass, no change
                this.base.mapUserLoc();
                this.base.hideDialog();
            } else {
                $.ajax({"url": 'services/geocode', "type": "GET",
                    "success": $.proxy(function(resp) {
                        if(resp && resp.results) {
                            this.base.user_loc = resp.results[0];
                            this.base.showLocationConfirm();
                        } else {
                            // no results!
                            this.base.showLocationDialog('Sorry, that location could not be found.');
                        }
                    }, this),
                    "error": function(resp) {
                    }, "data": {"placename": new_loc},
                    "processData": true
                });
            }
        }, {"base": this}));
    }, {"base": this, "err_msg": msg, "user_loc": this.user_loc}, this)
}

Sourcemap.Map.Base.prototype.showLocationConfirm = function() {
    Sourcemap.template('map/location/confirm', function(p, tx, th) {
        this.showDialog(th, true);
        $(this.dialog).find('#user-loc-accept').click($.proxy(function(evt) {
            this.hideDialog();
            this.mapUserLoc();
        }, this));
        $(this.dialog).find('#user-loc-reject').click($.proxy(function(evt) {
            this.showLocationDialog();
        }, this));
    }, this, this);
}

Sourcemap.Map.Base.prototype.mapUserLoc = function() {
    var user_stop = new Sourcemap.Stop();
    user_stop.setAttr({
        "color": this.options.user_loc_color,
        "name": "You", "placename": this.user_loc.placename
    });
    var wkt = new OpenLayers.Format.WKT();
    user_stop.geometry = wkt.read(
        'POINT('+this.user_loc.longitude+' '+this.user_loc.latitude+')'
    ).geometry;
    user_stop.geometry = wkt.write((new OpenLayers.Feature.Vector(user_stop.geometry.transform(
        new OpenLayers.Projection('EPSG:4326'), this.map.map.getProjectionObject()
    ))));
    var scid = null;
    for(scid in this.map.supplychains) break;
    this.getStopLayer(scid).addFeatures(this.map.mapStop(user_stop, scid));
    return this;
}

Sourcemap.Map.Base.prototype.toggleTileset = function(sc) {
    var tileset = sc.attributes["sm:ui:tileset"] || this.options.tileset;
    var cloudmade = {
    	"stop_style": {
    		"default": { "strokeColor": "${scolor}", "fontColor": "${fcolor}", "strokeWidth":"${swidth}" },
    		"cluster": { "strokeColor": "${scolor}", "fontColor": "${fcolor}", "strokeWidth":"${swidth}" }
    	},
    	"hop_style": {
    		"default": { "strokeColor": "${color}"},
    		"arrow": { "strokeColor": "${color}", "fontColor": "${fcolor}" }
    	}
    }
    var satellite = {
    	"stop_style": {
    		"default": { "strokeColor": "#ffffff", "fontColor": "#ffffff" },
    		"cluster": { "strokeColor": "#ffffff", "fontColor": "#ffffff" }
    	},
    	"hop_style": {
    		"default": { "strokeColor": "#ffffff"},
    		"arrow": { "strokeColor": "#ffffff", "fontColor": "#ffffff" }
    	}
    }
    var terrain = {
    	"stop_style": {
    		"default": { "strokeColor": "${scolor}", "fontColor": "${fcolor}", "strokeWidth":"${swidth}" },
    		"cluster": { "strokeColor": "${scolor}", "fontColor": "${fcolor}", "strokeWidth":"${swidth}" }
    	},
    	"hop_style": {
    		"default": { "strokeColor": "${color}"},
    		"arrow": { "strokeColor": "${color}", "fontColor": "${fcolor}" }
    	}
    }
    if(tileset == "cloudmade") {
    	$.extend(true, this.map.options, cloudmade); 
    }
    else if(tileset == "satellite") { 
    	$.extend(true, this.map.options, satellite); 
    } 
    else if(tileset == "terrain") { 
    	$.extend(true, this.map.options, terrain); 
    }

    // handle overlays upon color scheme changes
    if(this.options.watermark){
        $("#watermark").css("display","block");
        $("#watermark").removeClass("cloudmade satellite terrain").addClass(tileset);
    }

    this.map.setBaseLayer(tileset);
    
}
Sourcemap.Map.Base.prototype.decorateFeatures = function(dec_fn, features) {
    for(var i=0; i<features.length; i++) {
        if(dec_fn instanceof Function) {
            dec_fn(features[i], this);
        } else {
            if(features[i].cluster) {
            }
            for(var k in dec_fn) {
                if(features[i].attributes[k]) {
                    var decv = dec_fn[k];
                    if(decv instanceof Function) {
                        decv(features[i]);
                    } else {
                        features[i].attributes[k] = decv;
                    }
                }
            }
        }
    }
    return this;
}

Sourcemap.Map.Base.prototype.decorateStopFeatures = function(dec_fn) {
    var st_ftrs = this.map.getStopFeatures();
    return this.decorateFeatures(dec_fn, st_ftrs);
}

Sourcemap.Map.Base.prototype.decorateHopFeatures = function(dec_fn) {
    var h_ftrs = this.map.getHopFeatures();
    return this.decorateFeatures(dec_fn, h_ftrs);
}

Sourcemap.Map.Base.prototype.sizeFeaturesOnAttr = function(attr_nm, vmin, vmax, vtot, smin, smax, active_color) {   //Sets size/color of stops relative to impact (e.g. CO2 output etc..) 
	//vmin is smallest impact on map
	//vmax is largest impact on map
	//vtot is sum of all impacts on map
	//smin is minimum stop size (in area)
	//smax is maximum stop size  
    var active_color = active_color || this.options.attr_missing_color;
    var smin = smin == undefined ? this.map.options.min_stop_size : parseInt(smin);
    if(!smin) smin = this.map.options.min_stop_size;
    var smax = smax == undefined ? this.map.options.max_stop_size : parseInt(smax);

    if(!smax) smax = this.map.options.max_stop_size;
    var dec_fn = $.proxy(
		function(f, mb) {        
				//The val variable should be the polution value for this stop
		        var attr_nm = this.basemap.viz_attr_map[this.attr_nm];
		        if(f.cluster) {    //Why we divide this into two segments is unclear
		            var val = 0;
		            for(var c in f.cluster) {
                        if (c != "filter" && c != "indexOf" && typeof(f.cluster[c].attributes.ref) != "undefined"){
                            if(attr_nm instanceof Function){
                                var ref = f.cluster[c].attributes.ref;
								var newval = attr_nm(ref);
								if (!isNaN(newval)) 
                                	val += newval;
                            }
                            else 
                                val += parseFloat(f.cluster[c].attributes.ref.attributes[this.attr_nm]);
                        }
		            }
		            if(!isNaN(val) && val != 0) {
		                // scale  
						if (this.attr_nm == "valueatrisk") {
							fraction = val/this.vmax;
						} else {
							fraction = val/this.vtot;
						}
		                f.attributes.size = Math.max(Math.sqrt(fraction)*smax, smin); 
		                var fsize = 18;
		                f.attributes.fsize = fsize+"px";     
		                f.attributes.fcolor = this.color
		                f.attributes.yoffset = -1*(f.attributes.size+fsize);
		                var unit = "kg";
						if(this.attr_nm === "valueatrisk") { unit = ""; }
		                if(this.attr_nm === "water") { unit = "L"; } 
						if(this.attr_nm === "energy") { unit = "kWh"; }    
						if (unit != "") {
							var scaled = Sourcemap.Units.scale_unit_value(val, unit, 2);
						}
						else {
							scaled = {};
							scaled.value = val;
							scaled.unit = "";
						}            
		                f.attributes.label = parseFloat(scaled.value).toFixed(1) + " " + scaled.unit;
		            } else { f.attributes.label = ""; }
		        } else if(attr_nm && ((attr_nm instanceof Function) || (f.attributes[attr_nm] !== undefined))) {
		            if(attr_nm instanceof Function) val = attr_nm(f.attributes.ref);
		            else {
						//val = f.attributes[this.attr_nm];
						val =  attr_nm(f);
					}
					//val = f.attributes[this.attr_nm];
		            val = parseFloat(val);
		            if(!isNaN(val) && val != 0) { 
						if (this.attr_nm == "valueatrisk") {
							fraction = val/this.vmax;
						} else {
							fraction = val/this.vtot;
						}
		                f.attributes.size = Math.max(Math.sqrt(fraction)*smax, smin); 
		                var fsize = 18;
		                f.attributes.fsize = fsize+"px";     
		                f.attributes.fcolor = this.color
		                f.attributes.yoffset = -1*(f.attributes.size+fsize);                
		                var unit = "kg";
						if(this.attr_nm === "valueatrisk") { unit = ""; }
		                if(this.attr_nm === "water") { unit = "L"; }
						if(this.attr_nm === "energy") { unit = "kWh"; }
						if (unit != "") {
							var scaled = Sourcemap.Units.scale_unit_value(val, unit, 2);
						}
						else {
							scaled = {};
							scaled.value = val;
							scaled.unit = "";
						} 
						f.attributes.label = parseFloat(scaled.value).toFixed(1) + " " + scaled.unit;	      
		    			if(f.attributes.hop_component && f.attributes.hop_component == "hop") {
		    				f.attributes.label = "";
		    			}	              
		            } else { f.attributes.label = ""; }
		        } 
		        f.attributes.size = f.attributes.size || smin;
		        f.attributes.yoffset = f.attributes.yoffset || 0;
		        f.attributes.label = f.attributes.label || "";
		        f.attributes.color = this.color 
		        f.attributes.scolor = this.color 
		    }, 
	{"vmin": vmin, "vmax": vmax, "vtot": vtot, "smin": smin, "smax": smax, "attr_nm": attr_nm, "basemap": this, "color": active_color});
    return this.decorateStopFeatures(dec_fn) && this.decorateHopFeatures(dec_fn);
}

Sourcemap.Map.Base.prototype.searchFilterMap = function() {
	var operators = new Array(
		">=",
		"<=",
		"<",
		">",
		"=",
		"is"
		);
	var query = $(this.banner_div).find('#map-search').val();
	if (query == "") {
		return;
	}
	var queries = new Array();
	var splitthis = query.toString().split(" and ");
	var querystring  = splitthis.join("+&&+");
	var splitthis = querystring.toString().split(" or ");
	var querystring  = splitthis.join("+||+");	
	var linkedqueries = querystring.toString().split("+");
	//query = query.replace(" and "," && ").replace(" or ", " || ");	
    for(var scid in this.map.stop_features) {
        for(var k in this.map.stop_features[scid]) {
			var queries = linkedqueries.slice(0);
            var s = this.map.stop_features[scid][k];
            s = s.stop ? s.stop : s;
			var match = false;
			// figure out each condition
			for (var n in queries) {
				// which attribute
				if (typeof(queries[n]) == "string" && queries[n] !="&&" && queries[n] != "||") {
					var statement = new Array();
					for (var l in operators) {
						if (typeof(operators[l]) == "string") {
							if (queries[n].toString().search(operators[l].toString()) != -1) {						
								statement = queries[n].toString().split(operators[l]);							 
								if (typeof(s.attributes[statement[0]]) != "undefined") {
									switch (operators[l]) {
										case ">=":
										if (parseFloat(s.attributes[statement[0]]) >= parseFloat(statement[1])) {
											queries[n] = true;
										} else { queries[n] = false; }
										break;
										case "<=":
										if (parseFloat(s.attributes[statement[0]]) <= parseFloat(statement[1])) {
											queries[n] = true;
										} else { queries[n] = false; }
										break;
										case ">":
										if (parseFloat(s.attributes[statement[0]]) > parseFloat(statement[1])) {
											queries[n] = true;
										} else { queries[n] = false; }
										break;
										case "<":
										if (parseFloat(s.attributes[statement[0]]) < parseFloat(statement[1])) {
											queries[n] = true;
										} else { queries[n] = false; }
										break;
										case "=":
										if (parseFloat(s.attributes[statement[0]]) == parseFloat(statement[1])) {
											queries[n] = true;
										} else { queries[n] = false; }
										break;
										default:
											queries[n] = false;
										break;
									}					
								} else {
									queries[n] = false;
								}
								break;
							}
						}				
					}
					if (queries[n] != true && queries[n] != "&&" && queries[n] != "||") {
						queries[n] = false;
					}
				}
			}
			match = eval(queries.join(" "));
			if (match == false) {
				var query = new RegExp($(this.banner_div).find('#map-search').val(), "i");
				for(var a in s.attributes) {
					if(typeof(s.attributes[a]) == 'string') {
						if(s.attributes[a].search(query) != -1) { match = true; }
					}
				}
			}
			if(match == true) { s.renderIntent = 'default'; }
			else { s.renderIntent = 'disabled'; }
        }

    }

    for(var scid in this.map.hop_features) {
        for(var fromStop in this.map.hop_features[scid]){
            for (var toStop in this.map.hop_features[scid][fromStop]){
                var h = this.map.hop_features[scid][fromStop][toStop];
				var arr = h.arrow ? h.arrow : {};	
				var arr2 = h.arrow2 ? h.arrow2 : h.arrow;			
				var queries = linkedqueries.slice(0);		
                h = h.hop ? h.hop : h;	
				var match = false;
				for (var n in queries) {
					// which attribute
					if (typeof(queries[n]) == "string" && queries[n] !="&&" && queries[n] != "||") {
						var statement = new Array();
						for (var l in operators) {
							if (typeof(operators[l]) == "string") {
								if (queries[n].toString().search(operators[l].toString()) != -1) {						
									statement = queries[n].toString().split(operators[l]);							 
									if (typeof(h.attributes[statement[0]]) != "undefined") {
										switch (operators[l]) {
											case ">=":
											if (parseFloat(h.attributes[statement[0]]) >= parseFloat(statement[1])) {
												queries[n] = true;
											} else { queries[n] = false; }
											break;
											case "<=":
											if (parseFloat(h.attributes[statement[0]]) <= parseFloat(statement[1])) {
												queries[n] = true;
											} else { queries[n] = false; }
											break;
											case ">":
											if (parseFloat(h.attributes[statement[0]]) > parseFloat(statement[1])) {
												queries[n] = true;
											} else { queries[n] = false; }
											break;
											case "<":
											if (parseFloat(h.attributes[statement[0]]) < parseFloat(statement[1])) {
												queries[n] = true;
											} else { queries[n] = false; }
											break;
											case "=":
											if (parseFloat(h.attributes[statement[0]]) == parseFloat(statement[1])) {
												queries[n] = true;
											} else { queries[n] = false; }
											break;
											default:
												queries[n] = false;
											break;
										}					
									} else {
										queries[n] = false;
									}
									break;
								}
							}				
						}
						if (queries[n] != true && queries[n] != "&&" && queries[n] != "||") {
							queries[n] = false;
						}
					}
				}
				match = eval(queries.join(" "));
				if (match == false) {
					for(var a in h.attributes) {
						if(typeof(h.attributes[a]) == 'string') {
							if(h.attributes[a].search(query) != -1) { match = true; }
						}
					}
				}
				if(match == true) { h.renderIntent = 'default'; arr.renderIntent = 'arrow'; arr2.renderIntent = 'arrow';}
				else { h.renderIntent = 'disabled'; arr.renderIntent = 'disabled'; arr2.renderIntent = 'disabled';}
            }
        }
    }
    for(var scid in this.map.cluster_features) {
        for(var l in this.map.cluster_features[scid]) {
	        var c = this.map.cluster_features[scid][l];

			var match = false;						
			lookup:	
			for(var k in c.cluster) {
				var s = c.cluster[k];
				s = s.stop ? s.stop : s;
				if (typeof(s) == "object") {
					var queries = linkedqueries.slice(0);
					for (var n in queries) {
						// which attribute
						if (typeof(queries[n]) == "string" && queries[n] !="&&" && queries[n] != "||") {
							var statement = new Array();
							for (var l in operators) {
								if (typeof(operators[l]) == "string") {
									if (queries[n].toString().search(operators[l].toString()) != -1) {						
										statement = queries[n].toString().split(operators[l]);							 
										if (typeof(s.attributes[statement[0]]) != "undefined") {
											switch (operators[l]) {
												case ">=":
												if (parseFloat(s.attributes[statement[0]]) >= parseFloat(statement[1])) {
													queries[n] = true;
												} else { queries[n] = false; }
												break;
												case "<=":
												if (parseFloat(s.attributes[statement[0]]) <= parseFloat(statement[1])) {
													queries[n] = true;
												} else { queries[n] = false; }
												break;
												case ">":
												if (parseFloat(s.attributes[statement[0]]) > parseFloat(statement[1])) {
													queries[n] = true;
												} else { queries[n] = false; }
												break;
												case "<":
												if (parseFloat(s.attributes[statement[0]]) < parseFloat(statement[1])) {
													queries[n] = true;
												} else { queries[n] = false; }
												break;
												case "=":
												if (parseFloat(s.attributes[statement[0]]) == parseFloat(statement[1])) {
													queries[n] = true;
												} else { queries[n] = false; }
												break;
												default:
													queries[n] = false;
												break;
											}					
										} else {
											queries[n] = false;
										}
										break;
									}
								}				
							}
							if (queries[n] != true && queries[n] != "&&" && queries[n] != "||") {
								queries[n] = false;
							}
						}
					}
				}
				match = eval(queries.join(" "));
				if (match == false) {
					for(var a in s.attributes) {
						if(typeof(s.attributes[a]) == 'string') {
							if(s.attributes[a].search(query) != -1) { match = true;  break lookup;}
						}
					}
				}
				if (match == true) {
					break;
				}
			}
			if(match == true) { c.renderIntent = 'cluster'; }
			else { c.renderIntent = 'disabled'; }
        }
    }
	this.map.redraw();
}

Sourcemap.Map.Base.prototype.toggleVisualization = function(viz_nm) {
	alert("bloo");
    if(this.visualization_mode){
        if(this.visualization_mode != viz_nm) {
            this.disableVisualization();
            this.enableVisualization(viz_nm);
        }
        else{
            this.disableVisualization();
        }
    }
    else{
        this.enableVisualization(viz_nm);
    }
}

Sourcemap.Map.Base.prototype.enableVisualization = function(viz_nm) {
    this.map.controls.select.unselectAll();
    
    switch(viz_nm) {
        //case "energy":
        //    break;
		case "valueatrisk":
		case "energy":
        case "water":
        case "co2e":
        case "weight":
            this.visualization_mode = viz_nm;
            
            attr_nm = this.viz_attr_map[viz_nm];
            var range = null;
            for(var k in this.map.supplychains) {
                var sc = this.map.supplychains[k];
                if(range === null) range = sc.attrRange(attr_nm);
                else {
                    var tmprange = sc.attrRange(viz_nm);
                    if(tmprange.min !== null)
                        range.min = Math.min(range.min, tmprange.min);
                    if(tmprange.max !== null)
                        range.max = Math.max(range.max, tmprange.max);
                    if(tmprange.total !== null) {
                        range.total += tmprange.total;
                    }
                }
            }
            
            // add legend
            if (this.options.legend){
                var legend = $(this.map.map.div).find('#sourcemap-legend');
                if ($(legend).length == 0) {
                    var legend = $('<div id="sourcemap-legend"></div>');
                    legend.addClass(viz_nm);
                    if (this.map.map.baseLayer.name)
                        legend.addClass(this.map.map.baseLayer.name);
                    $(this.map.map.div).append(legend);
                }
            }

            // remove gradient
            var gradient = $(this.map.map.div).find('#sourcemap-gradient');
            if ($(gradient).length == 0) { // if gradient legend not exist
                //great, nothing happend
            } else {
                gradient.remove();
            }

            this.sizeFeaturesOnAttr(viz_nm, range.min, range.max, range.total, null, null, this.options.visualization_colors[viz_nm]);
            this.map.dockToggleActive(viz_nm);
            this.map.redraw();
            break;
    }
}

Sourcemap.Map.Base.prototype.disableVisualization = function() {
    this.visualization_mode = null;

    // disable all dock items
    for(var i=0; i<this.options.visualizations.length; i++) {
        var viz = this.options.visualizations[i];
        this.map.dockToggleInactive(viz);
    }
  
    // remove legend 
    if (this.options.legend){
        var legend = $(this.map.map.div).find('#sourcemap-legend');
        legend.remove();
    }

    for(var k in this.map.supplychains)
        this.map.mapSupplychain(k);
}

Sourcemap.Map.Base.prototype.calcMetricRange = function(metric) {
    var range = null;
    for(var k in this.map.supplychains) {
        var sc = this.map.supplychains[k];
        if(range === null) range = sc.attrRange(metric);
        else {
            var tmprange = sc.attrRange(metric);
            if(tmprange.min !== null)
                range.min = Math.min(range.min, tmprange.min);
            if(tmprange.max !== null)
                range.max = Math.max(range.max, tmprange.max);
            if(tmprange.total !== null) {
                range.total += tmprange.total;
            }
        }
    }
    return range;
}

Sourcemap.Map.Base.prototype.updateFilterDisplay = function(sc) {    
    // TODO: reed will make this better
    // add filter controls to dock
    if(sc.attributes["sm:ui:weight"]) {    
    	if(this.map.dockControlEl('weight').length == 0) {
            this.map.dockAdd('weight', {
                "title": 'Weight',
                "toggle": true,
                "panel": 'filter',
                "callbacks": {
                    "click": $.proxy(function() {
                        this.toggleVisualization("weight");
                    }, this)
                }
            });
    	}
    } else {
    	this.map.dockRemove('weight');
    }

    if(sc.attributes["sm:ui:co2e"]) {
    	if(this.map.dockControlEl('co2e').length == 0) {	
            this.map.dockAdd('co2e', {
                "title": 'Carbon',
                "content": "<span class=\"value\">-.-</span> <span class=\"unit\">kg</span> CO2e",
                "toggle": true,
                "panel": 'filter',
                "callbacks": {
                    "click": $.proxy(function() {
                        this.toggleVisualization("co2e");
                    }, this)
                }
            });
    	}
    } else {
    	this.map.dockRemove('co2e');
    }

    if(sc.attributes["sm:ui:water"]) {   
    	if(this.map.dockControlEl('water').length == 0) {	
            this.map.dockAdd('water', {
                "title": 'Water',
                "content": "<span class=\"value\">-.-</span> <span class=\"unit\">L</span> H2O",
                "toggle": true,
                "panel": 'filter',
                "callbacks": {
                    "click": $.proxy(function() {
                        this.toggleVisualization("water");
                    }, this)
                }
            });
    	}
    } else {
    	this.map.dockRemove('water');
    }

	if(sc.attributes["sm:ui:energy"]) {   
    	if(this.map.dockControlEl('energy').length == 0) {	
            this.map.dockAdd('energy', {
                "title": 'Energy',
                "content": "<span class=\"value\">-.-</span> <span class=\"unit\">kWh</span>",
                "toggle": true,
                "panel": 'filter',
                "callbacks": {
                    "click": $.proxy(function() {
                        this.toggleVisualization("energy");
                    }, this)
                }
            });
    	}
    } else {
    	this.map.dockRemove('energy');
    }

	if(sc.attributes["sm:ui:valueatrisk"]) {   
    	if(this.map.dockControlEl('valueatrisk').length == 0) {	
            this.map.dockAdd('hviz', {
                "title": 'Value at Risk',
                "content": "VARORT",
                "toggle": true,
                "panel": 'filter',
                "callbacks": {
                    "click": $.proxy(function() {
                        this.toggleVisualization("valueatrisk");
                    }, this)
                }
            });
    	}
    } else {
    	this.map.dockRemove('valueatrisk');
    }

}
Sourcemap.Map.Base.prototype.favorite = function() {
    for(var k in this.map.supplychains) {
        var sc = this.map.supplychains[k]; break;
    }
// check for delete
     if($("#banner-favorite").hasClass("marked")) {
         $.ajax({"url": 'services/favorites/'+sc.remote_id, "type": "DELETE",
                "success": $.proxy(function(resp) {
                    if(resp) {
                        $("#banner-favorite").removeClass("marked");
                    } 
                }, this)
            });
     } else {
         $.ajax({"url": 'services/favorites', "type": "POST",
                "success": $.proxy(function(resp) {
                    if(resp) {
                        $("#banner-favorite").addClass("marked");
                    } else { }
                }, this),
                "error": function(resp) {
                }, "data": {"supplychain_id":parseInt(sc.remote_id)}
            });
    }
}


// Returns LonLat coordinates from a feature
Sourcemap.Map.Base.prototype.getFeatureLonLat = function(ftr) {
    var ll = null;
    if(ftr.geometry && ftr.geometry instanceof OpenLayers.Geometry.Point) {
        ll = new OpenLayers.LonLat(ftr.geometry.x, ftr.geometry.y);
    } else if(ftr.geometry && ftr.geometry instanceof OpenLayers.Geometry.MultiLineString) {
        var ctr = ftr.geometry.getBounds().getCenterLonLat();
        ll = ctr;
    }
    return ll;
}


// jQuery fxn to center an detailed element
jQuery.fn.detail_center = function () {
    this.css("position","absolute");
    this.css("top", ($(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ($(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

// Jquery shake $.shake
jQuery.fn.shake = function ( ) { this.each(function(init) { var jqNode = $(this); for (var x = 1; x <= 2; x++) { jqNode.animate({ 'right' : '-=15px' },15) .animate({ 'right' : '+=15px' },15) .animate({ 'right' : '+=15px' },15) .animate({ 'right' : '-=15px' },15,"linear",function() { $(this).attr("style","display:"+$(this).css("display")+";"); }); } }); return this; }
