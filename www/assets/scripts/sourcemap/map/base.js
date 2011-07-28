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
    "tour": false, "min_stop_size": 1, "max_stop_size": 48, "error_color": '#ff0000',
    "attr_missing_color": Sourcemap.Map.prototype.defaults.default_feature_color,
    "visualization_mode": null, "visualizations": ["co2e","weight","water"],
    "visualization_colors": {"co2e": "#ffa500", "weight": "#804000", "water": "#000080"}
}

Sourcemap.Map.Base.prototype.init = function() {
    this.magic_word_sequence = this.options.magic_word_sequence;
    this.magic_word_cur_idx = this.options.magic_word_cur_idx;
    this.magic = this.options.magic || Sourcemap.MagicWords.popup_content;
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
            Sourcemap.broadcast('map-base-calc-update', v);
        }
    }, this));

    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
        //this.hideDialog();
        if(ftr.cluster) {
            this.showClusterDetails(ftr);
        }
        else if(ftr.attributes.stop_instance_id) {
            this.showStopDetails(
                ftr.attributes.stop_instance_id, ftr.attributes.supplychain_instance_id, 0
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
        // share link event
        $(this.banner_div).find('.banner-share-link').click($.proxy(function() { 
            this.showShare();
        }, this));
        $(this.banner_div).find('.banner-favorite-link').click($.proxy(function() { 
            this.favorite();
        }, this));
         $.ajax({"url": 'services/favorites', "type": "GET",
                "success": $.proxy(function(resp) {
                   for(var k in resp) {
                       if(resp[k].id == sc.remote_id) {
                           $(".banner-favorite-link").addClass("marked");
                       }
                   }                   
                }, this)});        
    }

    Sourcemap.tpl('map/overlay/supplychain', sc, $.proxy(cb, this));

    return this;
}
        
Sourcemap.Map.Base.prototype.initDialog = function(no_controls) {
   
    // set up dialog
    if(!this.dialog) {
        this.dialog = $('<div id="dialog"></div>');
        $(this.map.map.div).append(this.dialog);
    } else $(this.dialog).empty();
    // todo: bind events, not inline javascript
    this.dialog_prev_el = $('<div id="detail-nav" class="prev"><a href="javascript: void(0);"></a></div>');
    this.dialog_next_el = $('<div id="detail-nav" class="next"><a href="javascript: void(0);"></a></div>');
    this.dialog_close = $('<div id="detail-close" class="close"><a href="javascript: void(0);"></a></div>'); 
    $(this.dialog_close).click($.proxy(function() { this.hideDialog(); }, this));
    this.dialog_content = $('<div id="detail-content" class="content"></div>');

    this.dialog.append(this.dialog_content);
    this.dialog.append(this.dialog_close)
    $(this.dialog).data("state", 1); // todo: check this?

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

Sourcemap.Map.Base.prototype.showDialog = function(mkup, no_controls) {
    if(this.dialog && !($(this.dialog).hasClass("editor-dialog"))) {
        // update dialog content and position
        if(mkup && no_controls) {
            this.dialog.empty();
            this.initDialog(no_controls);
            $(this.dialog_content).html(mkup); // wipe controls
        } else if(mkup) {
            this.initDialog();
            $(this.dialog_content).html(mkup);
        }
        
        var fade = $(this.dialog).css("display") == "block" ? 0 : 100;
        $(this.dialog).fadeIn(fade, function() {}).data("state", 1);
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

    $(this.dialog).data("state", -1); // loading
    //"default_feature_colors": ["#35a297", "#b01560", "#e2a919"],
    
    // load template and render
    // todo: make this intelligible
    Sourcemap.template('map/details/stop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);

            // Sets up content-nav behavior
            $(this.base.dialog_content).find('.content-item').click($.proxy(function(evt) {
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
            $(this.dialog).removeClass("editor-dialog");        
    
            $(this.dialog_content).empty();
            var cluster_id = cluster.attributes.cluster_instance_id;
            var chtml = $("<div id='"+cluster_id+"' class='cluster'></div>");

            for(var i in cluster.cluster) {
                var linkcontent = cluster.cluster[i].attributes.title ?
                    cluster.cluster[i].attributes.title+" " : "";
                linkcontent += cluster.cluster[i].attributes.address ? 
                    "("+cluster.cluster[i].attributes.address+")" : "";
                var stop_id = cluster.cluster[i].attributes.stop_instance_id;

                var new_citem = $("<div id='target-"+stop_id+"' class='cluster-item'><a>"+linkcontent+"</a></div>");
                chtml.append(new_citem);        
            }
            chtml.prepend($("<h2>Cluster</h2>"));            
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

    $(this.dialog).data("state", -1); // loading
    //"default_feature_colors": ["#35a297", "#b01560", "#e2a919"],
    
    // load template and render
    // todo: make this intelligible
    Sourcemap.template('map/details/hop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);

            // Sets up content-nav behavior
            $(this.base.dialog_content).find('.content-item a').click($.proxy(function(evt) {
                var clicked_idx = parseInt(evt.target.parentNode.id.split('-').pop());
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

Sourcemap.Map.Base.prototype.showLocationDialog = function(msg) {
    var msg = msg ? msg : false;
    $(this.dialog).data("state", -1);
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
    $(this.dialog).data("state", -1);
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

Sourcemap.Map.Base.prototype.sizeStopsOnAttr = function(attr_nm, vmin, vmax, smin, smax, active_color) {
   // var active_color = active_color || this.options.attr_missing_color;
    var smin = smin == undefined ? this.options.min_stop_size : parseInt(smin);
    if(!smin) smin = this.options.min_stop_size;
    var smax = smax == undefined ? this.options.max_stop_size : parseInt(smax);
    if(!smax) smax = this.options.max_stop_size;
    var dec_fn = $.proxy(function(stf, mb) {    
        if(stf.cluster) {
            var val = 0;
            for(var c in stf.cluster) {
                val += parseFloat(stf.cluster[c].attributes[attr_nm]);
            }
            if(!isNaN(val)) {
                // scale
                val = Math.max(val, this.vmin);
                val = Math.min(val, this.vmax);
                var voff = val - this.vmin;
                var vrange = this.vmax - this.vmin;
                var sval = this.smax;
                if(vrange)
                    sval = parseInt(smin + ((voff/vrange) * (this.smax - this.smin)));
                stf.attributes.size = sval;
                var fsize = 18;
                stf.attributes.fsize = fsize+"px";                
                stf.attributes.yoffset = -1*(sval+fsize);
                
                var unit = "kg";
                if(attr_nm === "water") { unit = "L"; }                
                var scaled = Sourcemap.Units.scale_unit_value(val, unit, 2);   
                if(attr_nm === "co2e") { scaled.unit += " co2e"}                            
                stf.attributes.label = scaled.value + " " + scaled.unit;
                return;
            }
        }
        else if(stf.attributes[this.attr_name] !== undefined) {
            var val = stf.attributes[attr_nm];
            val = parseFloat(val);
            if(!isNaN(val)) {
                // scale
                val = Math.max(val, this.vmin);
                val = Math.min(val, this.vmax);
                var voff = val - this.vmin;
                var vrange = this.vmax - this.vmin;
                var sval = this.smax;
                if(vrange)
                    sval = parseInt(smin + ((voff/vrange) * (this.smax - this.smin)));
                stf.attributes.size = sval;
                var fsize = 18;
                stf.attributes.fsize = fsize+"px";                
                stf.attributes.yoffset = -1*(sval+fsize);                
                
                var unit = "kg";
                if(attr_nm === "water") { unit = "L"; }                
                var scaled = Sourcemap.Units.scale_unit_value(val, unit, 2);   
                if(attr_nm === "co2e") { scaled.unit += " co2e"}                            
                stf.attributes.label = scaled.value + " " + scaled.unit;
                return;
            }
        }
        stf.attributes.size = smin;
        stf.attributes.yoffset = 0;            
        stf.attributes.label = "";
        
        stf.attributes.color = mb.options.attr_missing_color;
    }, {"vmin": vmin, "vmax": vmax, "smin": smin, "smax": smax, "attr_name": attr_nm});
    return this.decorateStopFeatures(dec_fn);
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
            var range = null;
            for(var k in this.map.supplychains) {
                var sc = this.map.supplychains[k];
                if(range === null) range = sc.stopAttrRange(viz_nm);
                else {
                    var tmprange = sc.stopAttrRange(viz_nm);
                    if(tmprange.min !== null)
                        range.min = Math.min(range.min, tmprange.min);
                    if(tmprange.max !== null)
                        range.max = Math.max(range.max, tmprange.max);
                    if(tmprange.total !== null) {
                        range.total += tmprange.total;
                    }
                }
            }
            this.sizeStopsOnAttr(viz_nm, range.min, range.max, null, null, this.options.visualization_colors[viz_nm]);
            
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
        if(range === null) range = sc.stopAttrRange(metric);
        else {
            var tmprange = sc.stopAttrRange(metric);
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

Sourcemap.Map.Base.prototype.showShare = function() {
    for(var k in this.map.supplychains) {
        var sc = this.map.supplychains[k]; break;
    }
    var cb = function(p, tx, th) {
        $(this.dialog_content).empty();
        this.showDialog(th);
    }
    Sourcemap.tpl('map/share', sc, $.proxy(cb, this));
}

Sourcemap.Map.Base.prototype.favorite = function() {
    for(var k in this.map.supplychains) {
        var sc = this.map.supplychains[k]; break;
    }
// check for delete
     if($(".banner-favorite-link").hasClass("marked")) {
         $.ajax({"url": 'services/favorites/'+sc.remote_id, "type": "DELETE",
                "success": $.proxy(function(resp) {
                    if(resp) {
                        $(".banner-favorite-link").removeClass("marked");
                    } 
                }, this)
            });
     } else {
         $.ajax({"url": 'services/favorites', "type": "POST",
                "success": $.proxy(function(resp) {
                    if(resp) {
                        $(".banner-favorite-link").addClass("marked");
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
