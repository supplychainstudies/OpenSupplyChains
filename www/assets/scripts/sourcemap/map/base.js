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
    "banner": true, "watermark": true, "magic_word_sequence": [
        "youtube:link", "vimeo:link", "flickr:setid"
    ], "magic_word_cur_idx": -1, "tpl_base_path": Sourcemap.TPL_PATH,
    "tour_order_strategy": "upstream", "tileswitcher": false,
    "locate_user": false, "user_loc": false, "user_loc_color": "#ff0000",
    "tileset": "cloudmade", // terrain, cloudmade, etc. (check map.js)
    "tour": false, "min_stop_size": 6, "max_stop_size": 48, "error_color": '#ff0000',
    "attr_missing_color": Sourcemap.Map.prototype.defaults.default_feature_color,
    "visualization_mode": null, "visualizations": ["co2e","weight","water"],
    "visualization_colors": {"co2e": "#ffa500", "weight": "#804000", "water": "#000080"},
    "viz_attr_map": {
        "weight": function(st) {
            var val = 0;
            /*if(st.getAttr("qty", false)) {
                if(st.getAttr("unit", false) === "kg") {
                    val = parseInt(st.getAttr("qty", 0));
                }
            }*/
            var qty = parseInt(st.getAttr("qty", 0));
            var fac = parseInt(st.getAttr("weight", 0));
            if(!isNaN(qty) && !isNaN(fac)) val = qty * fac;
            return val;
        },
        "water": function(st) {
            var val = 0;
            var qty = parseInt(st.getAttr("qty", 0));
            var fac = parseInt(st.getAttr("water", 0));
            if(!isNaN(qty) && !isNaN(fac)) val = qty * fac;
            return val;
        }, 
        "co2e": function(st) {
            var val = 0;
            var qty = parseInt(st.getAttr("qty", 0));
            var fac = parseInt(st.getAttr("co2e", 0));
            if(!isNaN(qty) && !isNaN(fac)) val = qty * fac;
            return val;
        }
    }
}

Sourcemap.Map.Base.prototype.init = function() {
    this.magic_word_sequence = this.options.magic_word_sequence;
    this.magic_word_cur_idx = this.options.magic_word_cur_idx;
    this.magic = this.options.magic || Sourcemap.MagicWords.popup_content;
    this.viz_attr_map = this.options.viz_attr_map;
    this.initMap();
    this.initDialog();
    this.initEvents();
    // todo: put this somewhere else.
    if(this.options.watermark) {
        this.watermark = $('<div id="watermark"></div>');
        $(this.map.map.div).append(this.watermark);
    }
    var ratio = Math.min(document.body.clientHeight,document.body.clientWidth) / 500 * 100;
    //$("body").css("font-size", Math.max(60, Math.min(100,Math.floor(ratio)))+"%");
}

Sourcemap.Map.Base.prototype.initMap = function() {
    this.map = new Sourcemap.Map(this.options.map_element_id, {
        "prep_stop": $.proxy(function(stop, ftr) {
            // todo: magic words for size (other than "size")?
            var hasmagic = false;
            for(var ski=0; ski<this.magic_word_sequence.length; ski++) {
                var sk = this.magic_word_sequence[ski];
                if(stop.getAttr(sk, false))
                    hasmagic = true;
            }
            if(hasmagic) {
                ftr.attributes.strokeWidth = 2;
                ftr.attributes.strokeColor = "#fff";
            } else {
                ftr.attributes.label = "";
            }
        }, this),
        // callback for decorating hop feature and its arrow
        'prep_hop': function(hop, ftr, arrow) {
            // set arc and related arrow color
            //ftr.attributes.color = hop.getAttr('color', '#006633');
            //if(arrow) arrow.attributes.color = hop.getAttr('color', '#006633');
        }        
    });
    //this.map.setBaseLayer(this.options.tileset);

    $(this.map.map.div).css("position", "relative");

    // add filter controls to dock
    this.map.dockAdd('weight', {
        "title": 'Weight',
        "content": "<span class=\"value\">-.-</span> <span class=\"unit\">kg</span>",
        "toggle": true,
        "panel": 'filter',
        "callbacks": {
            "click": $.proxy(function() {
                this.toggleVisualization("weight");
            }, this)
        }
    });

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

    Sourcemap.listen('map-base-calc-update', $.proxy(function(evt, metric, value) {
        if(value === undefined || value === null) {
            var range = this.calcMetricRange(metric);
            value = range.total;
        }
        var unit = "kg";
        if(metric === "water") unit = "L";
        var scaled = Sourcemap.Units.scale_unit_value(value, unit, 2);
        this.map.dockControlEl(metric).find('.value').text(scaled.value);
        this.map.dockControlEl(metric).find('.unit').text(scaled.unit);
    }, this));

}

Sourcemap.Map.Base.prototype.initEvents = function() {
    Sourcemap.listen('map:supplychain_mapped', $.proxy(function(evt, map, sc) {
        if(!this.map || this.map !== map) return;
        if(this.options.banner && !($("#banner").length)) this.initBanner();
        // todo: do calculations here
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
    }, this));

    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
        //this.hideDialog();
        if(ftr.cluster) {
            this.showClusterDetails(ftr);
        }
        else if(ftr.attributes.stop_instance_id && !(map.editor)) {
            this.showStopDetails(
                ftr.attributes.stop_instance_id, ftr.attributes.supplychain_instance_id, 0
            );
        }
        else if (ftr.attributes.hop_instance_id && !(map.editor)) {
            this.showHopDetails(
                ftr.attributes.hop_instance_id, ftr.attributes.supplychain_instance_id, 0
            );
        }
    }, this)); 
    Sourcemap.listen('map:feature_unselected', $.proxy(function(evt, map, ftr) {
        this.last_selected = null;
        this.hideDialog();
    }, this));
}

Sourcemap.Map.Base.prototype.initBanner = function(sc) {
    this.banner_div = $(this.map.map.div).find('#banner').length ? 
        $(this.map.map.div).find('#banner') : false;
    if(!this.banner_div) {
        this.banner_div = $('<div id="banner"></div>');
        $(this.map.map.div).append(this.banner_div);
        $(this.map.map.div).append('<div class="map-status"></div>');
    }
    if(!sc) {
        // todo: this is bad, but it's worst case
        sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }
    }
    var cb = function(p, tx, th) {
        $(this.banner_div).html(th);
        $(this.banner_div).find('.banner-description-link').click(function(){
            $.scrollTo('#map-secondary', 600);
        });
        $(this.banner_div).find('.banner-share-link').click(function(){
            $.scrollTo('#share-info', 600);
        });
        $(this.banner_div).find('.banner-favorite-link').click($.proxy(function() { 
            this.favorite();
        }, this));
         $.ajax({"url": 'services/favorites', "type": "GET",
                "success": $.proxy(function(resp) {
                   for(var k in resp) {
                       if(resp[k].id == sc.remote_id) {
                           $(".banner-favorite-link").parent().addClass("marked");
                       }
                   }                   
                }, this)});        
    }

    Sourcemap.tpl('map/banner', sc, $.proxy(cb, this));

    return this;
}
        
Sourcemap.Map.Base.prototype.initDialog = function() {   
    // set up dialog
    if(!this.dialog) {
        this.dialog = $('<div id="dialog"></div>');
        $(this.map.map.div).append(this.dialog);
    } else $(this.dialog).empty();

    this.dialog_content = $('<div id="dialog-content"></div>');
    this.dialog.append(this.dialog_content);
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

Sourcemap.Map.Base.prototype.showDialog = function(mkup) {
    if(this.dialog) {
        this.initDialog();
        $(this.dialog_content).html(mkup);
        $(this.dialog_content).find(".close").click($.proxy(function() { this.hideDialog(); }, this));
        
        var fade = $(this.dialog).css("display") == "block" ? 0 : 100;
        $(this.dialog).fadeIn(fade, function() {});
    }
}

Sourcemap.Map.Base.prototype.hideDialog = function() {
    if(this.dialog) {
        this.dialog_content.empty();
        $(this.dialog).hide();
        this.map.controls["select"].unselectAll();        
    }
}

Sourcemap.Map.Base.prototype.showStopDetails = function(stid, scid, seq_idx) {
   // make sure the target magic word index is valid
    var seq_idx = seq_idx ? parseInt(seq_idx) : 0;
   
    // load stop details template and show in detail pane
    var sc = this.map.supplychains[scid];
    var stop = sc.findStop(stid);
    var f = this.map.stopFeature(scid, stid);
    
    // get magic word...make sure it's valid
    var magic_word = this.magic_word_sequence[seq_idx];
    while(((stop.getAttr(magic_word, false) === false) || (!stop.getAttr(magic_word).length || stop.getAttr(magic_word).length == 1)) 
        && seq_idx < this.magic_word_sequence.length-1) {
        magic_word = this.magic_word_sequence[++seq_idx];
    }
    
    // sync cur seq idx
    this.magic_word_sequence_cur_idx = seq_idx;

    if(stop.getAttr(magic_word, false) === false) magic_word = false;

    //"default_feature_colors": ["#35a297", "#b01560", "#e2a919"],
    
    // load template and render
    // todo: make this intelligible
    Sourcemap.template('map/details/stop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);

            // Sets up content-nav behavior
            $(this.base.dialog_content).find('.navigation-item').click($.proxy(function(evt) {
                var clicked_idx = parseInt(evt.target.id.split('-').pop());
                var idx = -1;
                for(var i=0; i<this.base.magic_word_sequence.length; i++) {
                    if(clicked_idx === i) {
                        idx = i;
                        break;
                    }
                }
                if(idx >= 0) {
                    this.base.showStopDetails(
                        this.stop.instance_id, this.supplychain.instance_id, idx
                    );
                }
            }, this));
                
        }, 
        {"stop": stop, "supplychain": sc, "magic_word": magic_word, 'base': this},
        {"base": this, "magic_word": magic_word, "stop": stop, "supplychain": sc},
        this.options.tpl_base_path
    );
    
}

Sourcemap.Map.Base.prototype.showClusterDetails = function(cluster) {    
            $(this.dialog_content).empty();
            var cluster_id = cluster.attributes.cluster_instance_id;
            var chtml = $("<div id='"+cluster_id+"' class='cluster'></div>");

            for(var i in cluster.cluster) {
                var title = cluster.cluster[i].attributes.title ?
                    cluster.cluster[i].attributes.title.substring(0,36) : "";
                title += title.length == 36 ? "..." : "";
                var address = cluster.cluster[i].attributes.address ? 
                    cluster.cluster[i].attributes.address.substring(0,46) : "";
                address += address.length == 46 ? "..." : "";
                
                var stop_id = cluster.cluster[i].attributes.stop_instance_id;

                var new_citem = $("<div id='target-"+stop_id+"' class='cluster-item'><a><h2>"+title+"</h2><h3 class='placename'>"+address+"</h3></a></div>");
                chtml.append(new_citem);        
            }
            this.showDialog(chtml);
            $(this.dialog).attr("class","grey");
            $(this.dialog).find(".cluster-item").click($.proxy(function(evt) {
                var sid = $(evt.currentTarget).attr("id").substring(7);
                var scid = null;
                for(scid in this.map.supplychains) break;
                var sftr = this.map.stop_features[scid][sid].stop;
                this.map.broadcast('map:feature_selected', this.map, sftr); 

            },this));


    
}
Sourcemap.Map.Base.prototype.showHopDetails = function(hid, scid) {

    // make sure the target magic word index is valid
    var seq_idx = seq_idx ? parseInt(seq_idx) : 0;
   
    // load stop details template and show in detail pane
    var sc = this.map.supplychains[scid];
    var hop = sc.findHop(hid);

    var f = this.map.hopFeature(scid, hid);

    // get magic word...make sure it's valid
    var magic_word = this.magic_word_sequence[seq_idx];
    
    while(((hop.getAttr(magic_word, false) === false) || (!hop.getAttr(magic_word).length || hop.getAttr(magic_word).length == 1)) 
        && seq_idx < this.magic_word_sequence.length-1) {
        magic_word = this.magic_word_sequence[++seq_idx];
    }
    
    // sync cur seq idx
    this.magic_word_sequence_cur_idx = seq_idx;

    if(hop.getAttr(magic_word, false) === false) magic_word = false;

    //"default_feature_colors": ["#35a297", "#b01560", "#e2a919"],
    
    // load template and render
    // todo: make this intelligible
    Sourcemap.template('map/details/hop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);

            // Sets up content-nav behavior
            $(this.base.dialog_content).find('.navigation-item a').click($.proxy(function(evt) {
                var clicked_idx = parseInt(evt.target.parentNode.id.split('-').pop());
                var idx = -1;
                for(var i=0; i<this.base.magic_word_sequence.length; i++) {
                    if(clicked_idx === i) {
                        idx = i;
                        break;
                    }
                }
                if(idx >= 0) {
                    this.base.showHopDetails(
                        this.hop.instance_id, this.supplychain.instance_id, idx
                    );
                }
            }, this));
                
        }, 
        {"hop": hop, "supplychain": sc, "magic_word": magic_word, 'base': this},
        {"base": this, "magic_word": magic_word, "hop": hop, "supplychain": sc},
        this.options.tpl_base_path
    );

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

Sourcemap.Map.Base.prototype.sizeFeaturesOnAttr = function(attr_nm, vmin, vmax, smin, smax, active_color) {
    var active_color = active_color || this.options.attr_missing_color;
    var smin = smin == undefined ? this.options.min_stop_size : parseInt(smin);
    if(!smin) smin = this.options.min_stop_size;
    var smax = smax == undefined ? this.options.max_stop_size : parseInt(smax);
    if(!smax) smax = this.options.max_stop_size;
    var dec_fn = $.proxy(function(f, mb) {
        var attr_nm = this.basemap.viz_attr_map[this.attr_nm];
        if(f.cluster) {
            var val = 0;
            for(var c in f.cluster) {
                if(attr_nm instanceof Function) val += attr_nm(f.cluster[c].attributes.ref);
                else val += parseFloat(f.cluster[c].attributes[attr_nm]);
            }
            if(!isNaN(val)) {
                // scale
                val = Math.max(val, this.vmin);
                val = Math.min(val, this.vmax);
                var voff = val - this.vmin;
                var vrange = this.vmax - this.vmin;
                var sval = this.smin;
                if(vrange)
                    sval = parseInt(smin + ((voff/vrange) * (this.smax - this.smin)));
                f.attributes.size = sval;
                var fsize = 18;
                f.attributes.fsize = fsize+"px";   
                f.attributes.fcolor = this.color;             
                f.attributes.yoffset = -1*(sval+fsize);

                var unit = "kg";
                if(attr_nm === "water") { unit = "L"; }                
                var scaled = Sourcemap.Units.scale_unit_value(val, unit, 2);   
                if(attr_nm === "co2e") { scaled.unit += " co2e"}                            
                f.attributes.label = scaled.value + " " + scaled.unit;
            }
        } else if(attr_nm && ((attr_nm instanceof Function) || (f.attributes[attr_nm] !== undefined))) {
            if(attr_nm instanceof Function) val = attr_nm(f.attributes.ref);
            else val = f.attributes[attr_nm];
            val = parseFloat(val);
            if(!isNaN(val)) {
                // scale
                val = Math.max(val, this.vmin);
                val = Math.min(val, this.vmax);
                var voff = val - this.vmin;
                var vrange = this.vmax - this.vmin;
                var sval = this.smin;
                if(vrange)
                    sval = parseInt(smin + ((voff/vrange) * (this.smax - this.smin)));
                f.attributes.size = sval;
                var fsize = 18;
                f.attributes.fsize = fsize+"px";     
                f.attributes.fcolor = this.color
                f.attributes.yoffset = -1*(sval+fsize);                
                
                var unit = "kg";
                if(attr_nm === "water") { unit = "L"; }                
                var scaled = Sourcemap.Units.scale_unit_value(val, unit, 2);   
                if(attr_nm === "co2e") { scaled.unit += " co2e"}                            
                f.attributes.label = scaled.value + " " + scaled.unit;
            }
        } 
        f.attributes.size = f.attributes.size || smin;
        f.attributes.yoffset = f.attributes.yoffset || 0;
        f.attributes.label = f.attributes.label || "";
        f.attributes.color = this.color
        f.attributes.scolor = this.color
    }, {"vmin": vmin, "vmax": vmax, "smin": smin, "smax": smax, "attr_nm": attr_nm, "basemap": this, "color": active_color});
    return this.decorateStopFeatures(dec_fn) && this.decorateHopFeatures(dec_fn);
}

Sourcemap.Map.Base.prototype.toggleVisualization = function(viz_nm) {
    this.map.last_selected = null;
    this.map.controls.select.unselectAll();
    
    this.map.controls["select"].unselectAll();        
    switch(viz_nm) {
        //case "energy":
        //    break;
        case "water":
        case "co2e":
        case "weight":
            if(this.visualization_mode === viz_nm) {
                this.toggleVisualization();
                break;
            } else {
                this.toggleVisualization();
            }
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
            this.sizeFeaturesOnAttr(viz_nm, range.min, range.max, null, null, this.options.visualization_colors[viz_nm]);
            
            this.map.dockToggleActive(viz_nm);
            this.map.redraw();
            break;
        default:
            this.visualization_mode = null;
            for(var i=0; i<this.options.visualizations.length; i++) {
                var viz = this.options.visualizations[i];
                this.map.dockToggleInactive(viz);
            }
            for(var k in this.map.supplychains)
                this.map.mapSupplychain(k);
            break;
    }
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

Sourcemap.Map.Base.prototype.favorite = function() {
    for(var k in this.map.supplychains) {
        var sc = this.map.supplychains[k]; break;
    }
// check for delete
     if($(".banner-favorite-link").parent().hasClass("marked")) {
         $.ajax({"url": 'services/favorites/'+sc.remote_id, "type": "DELETE",
                "success": $.proxy(function(resp) {
                    if(resp) {
                        $(".banner-favorite-link").parent().removeClass("marked");
                    } 
                }, this)
            });
     } else {
         $.ajax({"url": 'services/favorites', "type": "POST",
                "success": $.proxy(function(resp) {
                    if(resp) {
                        $(".banner-favorite-link").parent().addClass("marked");
                    } else { }
                }, this),
                "error": function(resp) {
                }, "data": {"supplychain_id":parseInt(sc.remote_id)}
            });
    }
}
// jQuery fxn to center an detailed element
jQuery.fn.detail_center = function () {
    this.css("position","absolute");
    this.css("top", ($(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ($(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */

;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);
