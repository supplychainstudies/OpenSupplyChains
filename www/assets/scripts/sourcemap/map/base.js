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
        "description", "youtube:link", "vimeo:link", "flickr:setid"
    ], "magic_word_cur_idx": -1, "tpl_base_path": Sourcemap.TPL_PATH,
    "tour_order_strategy": "upstream", "tileswitcher": false,
    "locate_user": false, "user_loc": false, "user_loc_color": "#ff0000",
    "tileset": "cloudmade", // terrain, cloudmade, etc. (check map.js)
    "tour": false
}
Sourcemap.Map.Base.prototype.init = function() {
    this.magic_word_sequence = this.options.magic_word_sequence;
    this.magic_word_cur_idx = this.options.magic_word_cur_idx;
    this.magic = this.options.magic || Sourcemap.MagicWords.popup_content;
    this.initMap();
    this.initDialog();
    this.initEvents();
    // todo: put this somewhere else.
    var ratio = Math.min(document.body.clientHeight,document.body.clientWidth) / 500 * 100;
    $("body").css("font-size", Math.max(60, Math.min(100,Math.floor(ratio)))+"%");
}

Sourcemap.Map.Base.prototype.initMap = function() {
    this.map = new Sourcemap.Map(this.options.map_element_id, {
        //"tileswitcher": this.options.tileswitcher,
        "prep_popup": $.proxy(function(ref, ftr, pop) {
            var t = ['popup'];
            var tscope = {"popup": pop, "feature": ftr, 'base': this};
            if(ref instanceof Sourcemap.Stop) {
                t.push('stop');
                tscope.stop = ref;
            } else if(ref instanceof Sourcemap.Hop) {
                t.push('hop');
                tscope.hop = ref;
            }

            // determine if this stop has relevant content
            var hasmagic = false;
            for(var ski=0; ski<this.magic_word_sequence.length; ski++) {
                var sk = this.magic_word_sequence[ski];
                if(ref.getAttr(sk, false))
                    hasmagic = true;
            }
            tscope.morelink = hasmagic;
            Sourcemap.template('map/'+t.join('-'), $.proxy(function(p, tx, th) {
                this.popup.setContentHTML($('<div></div>').html(th).html());
                $(this.popup.contentDiv).find('.popup-more-link').click($.proxy(function() {
                    if(this.stop) { 
                        this.base.showStopDetails(
                            this.stop.instance_id, 
                            this.feature.attributes.supplychain_instance_id, 
                            this.base.magic_word_sequence_idx
                        );
                    } else if(this.hop) {
                        this.base.showHopDetails(
                            this.hop.instance_id, 
                            this.feature.attributes.supplychain_instance_id, 
                            this.base.magic_word_sequence_idx
                        );
                    }
                }, this));
                //this.popup.updateSize();
                this.popup.updatePosition();
                this.base.map.broadcast('popup-initialized', this.popup, this.stop);
            }, tscope), tscope, null, this.options.tpl_base_path);
        }, this),
        // callback for Sourcemap.Map to decorate a stop feature
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



}

Sourcemap.Map.Base.prototype.initEvents = function() {
    Sourcemap.listen('map:supplychain_mapped', $.proxy(function(evt, map, sc) {
        if(!this.map || this.map !== map) return;
        if(this.options.banner) this.initBanner();
        if(this.options.tour) {
            this.initTour();
        } else {
            this.tour = false;
        }
        if(this.options.watermark) {
            this.watermark = $('<div id="watermark"></div>');
            $(this.map.map.div).append(this.watermark);
        }
    }, this));
    
    $(window).resize($.proxy(function () { 
        var ratio = Math.min(document.body.clientHeight,document.body.clientWidth) / 500 * 100;
        $("body").css("font-size", Math.max(60, Math.min(100,Math.floor(ratio)))+"%");
        
        // Display, but hide this while we calculate
        $(this.detailpane).css({"left": "-100000px"});
        $(this.detailpane).css({"top": "-100000px"});
        
        var hidden_height = ($(this.dialog).css("display") == "none");
        if(hidden_height) { $(this.dialog).css({"display":"block"}); }
        
        // Calculate the minimum needed width
        $(this.dialog).width(1).height(1);
        var max_width = 0;
        $('#detail-content > *').each(function(){ 
            var this_width = $(this).width();
            if (this_width > max_width) { max_width = this_width;}
        }); 
        $(this.dialog).width((max_width/.8));

        // Calculate the ideal height
        var total_height = 0;        
        $('#detail-content > *').each(function(){
            if(this.tagName == "IFRAME") {
                $(this).width($('#detail-content').width()).height($(this).width() * 0.8);
            } else if(this.tagName == "OBJECT") { 
                $(this).width($('#detail-content').width()).height($(this).width() * 0.8);         
                $(this).children("base").width($('#detail-content').width()).height($(this).width() * 0.8);                                       
            }
           total_height +=  $(this).outerHeight(true);      
        });
        $(this.dialog).height((total_height));
        $(this.dialog).find('#detail-nav').css({"height": total_height}).show();

        // Get positioning
        var h2 = ($(this.dialog).outerHeight() / 2);
        var dt  = Math.floor(($(this.map.map.div).innerHeight()-(h2*2)) / 2);
        var w2 = ($(this.dialog).outerWidth() / 2);
        var dl = Math.floor(($(this.map.map.div).innerWidth() - (w2*2)) / 2);
        
        // Undisplay and set correct position
        if(hidden_height) { $(this.dialog).css({"display":"none"}); }                
        $(this.dialog).css({"left": dl+"px"});
        $(this.dialog).css({"top": dt+"px"});

    }, this));

}

Sourcemap.Map.Base.prototype.initTour = function() {
    var strategy = this.options.tour_order_strategy;
    var features = false;
    switch(strategy) {
        default:
            features = [];
            for(var k in this.map.supplychains) {
                var sc = this.map.supplychains[k];
                var g = new Sourcemap.Supplychain.Graph(this.map.supplychains[k]);
                var order = g.depthFirstOrder(true); // upstream
                order = order.concat(g.islands());
                for(var i=0; i<order.length; i++)
                    features.push(this.map.mapped_features[order[i]]);
            }
            break;
    }
    this.tour = new Sourcemap.MapTour(this.map, {
        "features": features, "wait_interval": this.options.tour_start_delay,
        "interval": this.options.tour_interval
    });
    return this;
}

Sourcemap.Map.Base.prototype.initBanner = function(sc) {
    this.banner_div = $(this.map.map.div).find('.map-banner').length ? 
        $(this.map.map.div).find('.map-banner') : false;
    if(!this.banner_div) {
        this.banner_div = $('<div class="map-banner"></div>');
        $(this.map.map.div).append(this.banner_div);
    }
    if(!sc) {
        // todo: this is bad, but it's worst case
        sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }
    }
    Sourcemap.template('map/overlay/supplychain', function(p, tx, th) {
        $(this.banner_div).html(th);
        this.updateStatus("Loaded...", "good-news");
    }, sc, this, this.options.tpl_base_path);
    return this;
}

Sourcemap.Map.Base.prototype.initDialog = function(no_controls) {
   
    // set up detail pane
    if(!this.dialog) {
        this.dialog = $('<div id="detail-pane"></div>');
        $(this.map.map.div).append(this.dialog);
    } else $(this.dialog).empty();
    // todo: bind events, not inline javascript
    this.dialog_prev_el = $('<div id="detail-nav" class="prev"><a href="javascript: void(0);"></a></div>');
    this.dialog_next_el = $('<div id="detail-nav" class="next"><a href="javascript: void(0);"></a></div>');
    this.dialog_close = $('<div id="detail-close" class="close"><a href="javascript: void(0);"></a></div>'); 
    $(this.dialog_prev_el).click($.proxy(function() { this.dialogPrev(); }, this));
    $(this.dialog_next_el).click($.proxy(function() { this.dialogNext(); }, this));
    $(this.dialog_close).click($.proxy(function() { this.dialogClose(); }, this));
    this.dialog_content = $('<div id="detail-content" class="content"></div>');
    this.dialog.append(this.dialog_close)
    if(!no_controls) 
        this.dialog.append(this.dialog_prev_el)
    this.dialog.append(this.dialog_content);
    if(!no_controls)
        this.dialog.append(this.dialog_next_el);
    $(this.dialog).data("state", 1); // todo: check this?

    // close on click-out
    this.map.map.events.on({
        "click": function(e) {
            if($(this.dialog).data("state")) {
                this.hideDialog();
            }
            if(this.tour) this.tour.stop();//.wait();
        },
        "scope": this 
    });

    // Setup dimmer
    if(false && !this.curtain) {
        this.curtain = $('<div id="curtain" class="hidden"></div>');
        $(this.map.map.div).append(this.curtain);
        this.curtain.click($.proxy(function() {
            this.hideDialog();
        }, this));
    }
}

Sourcemap.Map.Base.prototype.updateStatus = function(msg, cls) {
    $(this.banner_div).children().clearQueue();
    var cls = cls || false;
    var newmsg = $('<div></div>').addClass("msg").text(msg);
    if(cls) newmsg.addClass(cls);
    $(this.banner_div).find('.map-status').empty().append(newmsg);
    $(this.banner_div).find('.map-status .msg').fadeTo(5000, 0);
    return this;
}

Sourcemap.Map.Base.prototype.showDialog = function(mkup, no_controls) {
    if(this.dialog) {
        $(this.curtain).removeClass("hidden").fadeIn();
        // update dialog content and position
        if(mkup && no_controls) {
            this.dialog.empty();
            this.initDialog(no_controls);
            $(this.dialog_content).html(mkup); // wipe controls
        } else if(mkup) {
            this.dialog.empty();
            this.initDialog();
            $(this.dialog_content).html(mkup);
        }
        
        this.map.controls.select.unselectAll();
        
        $(window).resize();
        
        var fade = $(this.dialog).css("display") == "block" ? 0 : 100;
        $(this.dialog).fadeIn(fade, function() {}).data("state", 1);
        
        if(this.tour) this.tour.stop();
    }
}

Sourcemap.Map.Base.prototype.hideDialog = function() {
    if(this.dialog) {
        $("#curtain").fadeOut(function() {$(this).addClass("hidden")});           
        this.dialog_content.empty();
        $(this.dialog).find('#detail-nav').css({"height": "auto"}).hide();
        $(this.dialog).hide().data("state", 0);
    }
}

Sourcemap.Map.Base.prototype.showStopDetails = function(stid, scid, seq_idx) {
   // make sure the target magic word index is valid
    var seq_idx = seq_idx ? parseInt(seq_idx) : 0;
    
    // sync tour
    if(this.tour) {
        var tftrs = this.tour.features;
        for(var tfi=0; tfi< tftrs.length; tfi++) {
            var tfattrs = tftrs[tfi].attributes;
            if(tfattrs.stop_instance_id && tfattrs.stop_instance_id == stid) {
                this.tour.ftr_index = tfi;
                break;
            }
        }
    }

    // load stop details template and show in detail pane
    var sc = this.map.supplychains[scid];
    var stop = sc.findStop(stid);

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

    // load template and render
    // todo: make this intelligible
    Sourcemap.template('map/details/stop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);
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

Sourcemap.Map.Base.prototype.showHopDetails = function(hid, scid) {
   // make sure the target magic word index is valid
    var seq_idx = seq_idx ? parseInt(seq_idx) : 0;
    
    // sync tour
    var tftrs = this.tour.features;
    for(var tfi=0; tfi< tftrs.length; tfi++) {
        var tfattrs = tftrs[tfi].attributes;
        if(tfattrs.hop_instance_id && tfattrs.hop_instance_id == hid) {
            this.tour.ftr_index = tfi;
            break;
        }
    }

    // load hop details template and show in base detail pane
    var sc = this.map.supplychains[scid];
    var hop = sc.findHop(hid);

    // get magic word...make sure it's valid
    var magic_word = this.magic_word_sequence[seq_idx];
    while(((hop.getAttr(magic_word, false) === false) || (!hop.getAttr(magic_word).length || hop.getAttr(magic_word).length == 1)) 
        && seq_idx < this.magic_word_sequence.length-1) {
        magic_word = this.magic_word_sequence[++seq_idx];
    }
    
    // sync cur seq idx
    this.magic_word_sequence_cur_idx = seq_idx;

    if(hop.getAttr(magic_word, false) === false) magic_word = false;

    $(this.dialog).data("state", -1); // loading

    // load template and render
    // todo: make this intelligible
    Sourcemap.template('map/details/hop', function(p, tx, th) {
            $(this.base.dialog_content).empty();
            this.base.showDialog(th);
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

Sourcemap.Map.Base.prototype.dialogNext = function() {
    if($(this.dialog).data("state") === -1) return;
    var nxt_seq_idx = this.magic_word_sequence_cur_idx >= 0 && this.magic_word_sequence_cur_idx < this.magic_word_sequence.length - 1 ?
        this.magic_word_sequence_cur_idx + 1 : -1;
    if(nxt_seq_idx < 0) {
        if(this.tour) this.tour.next();
        nxt_seq_idx = 0;
    }
    this.magic_word_sequence_cur_idx = nxt_seq_idx;
    if(!this.tour) return;
    var cftr = this.tour.getCurrentFeature();
    if(!cftr) {
        if(this.tour.features.length) {
            cftr = this.tour.features[0];
            this.tour.ftr_index = 0;
        } else {
            return;
        }
    }
    var scid = cftr.attributes.supplychain_instance_id;
    var stop, hop;
    if(stop = this.tour.getCurrentStop()) {
        var magic_word = false;
         while(!magic_word && nxt_seq_idx < this.magic_word_sequence.length) {
            magic_word = this.magic_word_sequence[nxt_seq_idx];
            if(magic_word && stop.getAttr(magic_word, false) == false) {
                magic_word = false;
            }
            if(!magic_word) nxt_seq_idx++;
        };
        if(!magic_word) {
            this.magic_word_sequence_cur_idx = -1;
            return this.dialogNext();
        }
        this.magic_word_sequence_cur_idx = nxt_seq_idx;
        this.showStopDetails(stop.instance_id, scid, this.magic_word_sequence_cur_idx); 
    } else if(hop = this.tour.getCurrentHop()) {
        var magic_word = false;
         while(!magic_word && nxt_seq_idx < this.magic_word_sequence.length) {
            magic_word = this.magic_word_sequence[nxt_seq_idx];
            if(magic_word && stop.getAttr(magic_word, false) == false) {
                magic_word = false;
            }
            if(!magic_word) nxt_seq_idx++;
        };
        if(!magic_word) {
            this.magic_word_sequence_cur_idx = -1;
            return this.dialogNext();
        }
        this.magic_word_sequence_cur_idx = nxt_seq_idx;
        this.showHopDetails(hop.instance_id, scid, this.magic_word_sequence_cur_idx);
    } else {
        throw new Error('Unexpected feature...not a stop or a hop.');
    }
}

Sourcemap.Map.Base.prototype.dialogPrev = function() {
    if($(this.dialog).data("state") === -1) return;
    var prv_seq_idx = this.magic_word_sequence_cur_idx >= 0 ? this.magic_word_sequence_cur_idx - 1 : 0;
    if(prv_seq_idx < 0) {
        this.tour.prev();
        prv_seq_idx = this.magic_word_sequence.length;
    }
    this.magic_word_sequence_cur_idx = prv_seq_idx;
    var cftr = this.tour.getCurrentFeature();
    if(!cftr) {
        if(this.tour.features.length) {
            cftr = this.tour.features[this.tour.features.length-1];
            this.tour.ftr_index = this.tour.features.length-1;
        } else {
            return;
        }
    }
    var scid = cftr.attributes.supplychain_instance_id;
    var stop, hop;
    if(stop = this.tour.getCurrentStop()) {
        var magic_word = false;
         while(!magic_word && prv_seq_idx >= 0) {
            magic_word = this.magic_word_sequence[prv_seq_idx];
            if(magic_word && stop.getAttr(magic_word, false) == false)
                magic_word = false;
            if(!magic_word) prv_seq_idx--;
        };
        if(!magic_word) {
            this.magic_word_sequence_cur_idx = 0;
            return this.dialogPrev();
        }
        this.magic_word_sequence_cur_idx = prv_seq_idx;
        this.showStopDetails(stop.instance_id, scid, this.magic_word_sequence_cur_idx); 
    } else if(hop = this.tour.getCurrentHop()) {
        this.showHopDetails(hop.instance_id, scid, this.magic_word_sequence_cur_idx);
    } else {
        throw new Error('Unexpected feature...not a stop or a hop.');
    }
}

Sourcemap.Map.Base.prototype.dialogClose = function() {
    if($(this.dialog).data("state")) {
        this.hideDialog();
    }
    this.tour.stop();//.wait();
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
    this.map.mapStop(user_stop, scid);
    if(this.tour) {
        this.tour.stop();
        var ftr = this.map.findFeaturesForStop(scid, user_stop.instance_id).stop;
        var g = new Sourcemap.Supplychain.Graph(this.map.supplychains[scid]);
        var order = g.fromClosestLeafOrder(user_stop);
        if(!order.length) order = g.islands();
        this.tour.features = this.tour.getFeatures(order);
        this.tour.features.splice(0, 0, ftr);
        this.map.map.zoomIn();
        this.map.supplychains[scid].stops.push(user_stop);
        this.tour.start();
    }
    return this;
}

// jQuery fxn to center an detailed element
jQuery.fn.detail_center = function () {
    this.css("position","absolute");
    this.css("top", ($(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ($(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

