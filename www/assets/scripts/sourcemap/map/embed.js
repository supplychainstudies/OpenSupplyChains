// todo: put this somewhere else
// jQuery fxn to center an detailed element
jQuery.fn.detail_center = function () {
    this.css("position","absolute");
    this.css("top", ($(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ($(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

Sourcemap.Map.Embed = function(o) {
    this.broadcast('map_embed:instantiated', this);
    var o = o || {};
    Sourcemap.Configurable.call(this, o);
    this.instance_id = Sourcemap.instance_id("sourcemap-embed");
}

Sourcemap.Map.Embed.prototype = new Sourcemap.Configurable();

Sourcemap.Map.Embed.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.Embed.prototype.defaults = {
    "auto_init": true,
    "map_element_id": 'sourcemap-map-embed',
    "banner": true, "watermark": true, "magic_word_sequence": [
        "description", "youtube:link", "vimeo:link", "flickr:setid"
    ], "magic_word_cur_idx": -1, "tpl_base_path": Sourcemap.TPL_PATH,
    "tour_order_strategy": "upstream", "tileswitcher": false,
    "locate_user": true, "user_loc": false, "user_loc_color": "#ff0000",
    "tileset": "cloudmade" // terrain, cloudmade, etc. (check map.js)
}

// callbacks for magic attributes
Sourcemap.Map.Embed.prototype.defaults.magic = {
    "youtube": {
        "link": function(lnk) {
            if(!lnk || !lnk.match(/((\?v=)|(v\/))(.+)$/))
                return '<p class="error">Invalid YouTube link.</p>';
            var mkup = '<iframe class="youtube-player" type="text/html"'+
                'src="http://www.youtube.com/embed/'+(lnk.match(/((\?v=)|(v\/))(.+)$/))[4]+'?autoplay=1"'+ 
                'frameborder="0" allowfullscreen></iframe>';
            return mkup;
        }
    },
    "vimeo": {
        "link": function(lnk) {
            var mkup = '<iframe class="vimeo-player" src="http://player.vimeo.com/video/'+
                (lnk.match(/\/(\d+)$/))[1]+'?title=0&amp;byline=0&amp;portrait=0&autoplay=1" '+
                'frameborder="0"></iframe>';
            return mkup;
        }
    },
    "flickr": {
        "api_key": "06ea60fff75fc5721cfd11d823634ab8",
        "setid": function(setid, elid) {
            var url = "http://www.flickr.com/services/rest/?jsoncallback=?";
            $.getJSON(url, {
                "method": "flickr.photosets.getPhotos", "format": "json",
                "api_key": this.magic.flickr.api_key, "photoset_id": setid
            }, $.proxy(function(data) {
                /*
                var mkup = $('<div class="flickr-photoset not-found"></div>');
                if(data.photoset && data.photoset.photo && data.photoset.photo.length) {
                    mkup = $('<div class="flickr-photoset"></div>');
                    var ownerid = data.photoset.owner;
                    var ownername = data.photoset.ownername;
                    for(var i=0; i<data.photoset.photo.length; i++) {
                        var photo = data.photoset.photo[i];
                        var farm = photo.farm;
                        var server = photo.server;
                        var imgurl = 'http://farm'+farm+'.static.flickr.com/'+server+'/'+photo.id+'_'+photo.secret+'.jpg'
                        var seturl = 'http://www.flickr.com/photos/'+ownername+'/sets/'+setid;
                        mkup.append('<a href="'+seturl+'"><img src="'+imgurl+'" /></a>');
                    }
                    mkup.append('<div class="clear">&nbsp;</div>');
                }
                */
                if(data && data.photoset && data.photoset.photo && data.photoset.photo.length) {
                    var mkup = '<object> <param name="flashvars" value="offsite=true&lang=en-us&page_show_url=%2Fphotos%2F'+
                        data.photoset.owner+'%2Fsets%2F'+setid+'%2Fshow%2F&page_show_back_url=%2Fphotos%2F'+
                        data.photoset.owner+'%2Fsets%2F'+setid+'%2F&set_id='+setid+'&jump_to="></param> '+
                        '<param name="movie" value="http://www.flickr.com/apps/slideshow/show.swf?v=71649"></param> '+
                        '<param name="allowFullScreen" value="true"></param><embed type="application/x-shockwave-flash"'+
                        'src="http://www.flickr.com/apps/slideshow/show.swf?v=71649" allowFullScreen="true" '+
                        'flashvars="offsite=true&lang=en-us&page_show_url=%2Fphotos%2F'+data.photoset.owner+
                        '%2Fsets%2F'+setid+'%2Fshow%2F&page_show_back_url=%2Fphotos%2F'+data.photoset.owner+
                        '%2Fsets%2F'+setid+'%2F&set_id='+setid+'&jump_to="></embed></object>';
                } else {
                    var mkup = 'Photo set not found.';
                }
                $('#flickr-photoset-'+setid).replaceWith(mkup);
                $(window).resize();                
                
                return;
            }, this));
            return '<div class="flickr-slideshow-wrapper" id="flickr-photoset-'+setid+'"></div>';
        }
    }
};

Sourcemap.Map.Embed.prototype.init = function() {
    this.magic_word_sequence = this.options.magic_word_sequence;
    this.magic_word_cur_idx = this.options.magic_word_cur_idx;
    this.magic = this.options.magic;
    this.initMap();
    this.initDialog();
    this.initEvents();
    // todo: put this somewhere else.
    var ratio = Math.min(document.body.clientHeight,document.body.clientWidth) / 500 * 100;
    $("body").css("font-size", Math.max(60, Math.min(100,Math.floor(ratio)))+"%");
}

Sourcemap.Map.Embed.prototype.initMap = function() {
    this.map = new Sourcemap.Map(this.options.map_element_id, {
        "tileswitcher": this.options.tileswitcher,
        "prep_popup": $.proxy(function(ref, ftr, pop) {
            var t = ['popup'];
            var tscope = {"popup": pop, "feature": ftr, 'embed': this};
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
            Sourcemap.template('embed/'+t.join('-'), $.proxy(function(p, tx, th) {
                $(this.popup.contentDiv).html(th);
                $(this.popup.contentDiv).find('.popup-more-link').click($.proxy(function() {
                    if(this.stop) { 
                        this.embed.showStopDetails(
                            this.stop.instance_id, 
                            this.feature.attributes.supplychain_instance_id, 
                            this.embed.magic_word_sequence_idx
                        );
                    } else if(this.hop) {
                        this.embed.showHopDetails(
                            this.hop.instance_id, 
                            this.feature.attributes.supplychain_instance_id, 
                            this.embed.magic_word_sequence_idx
                        );
                    }
                }, this));
                //this.popup.contentDiv.appendChild(this.popup.getCloseBox());
                this.popup.updateSize();
                this.popup.updatePosition();
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
    // make and place custom zoom controls
    /*
    var ze = new OpenLayers.Control.ZoomToMaxExtent({"title": "zoom all the way out"});
    var zi = new OpenLayers.Control.ZoomIn({"title": "zoom in"});
    var zo = new OpenLayers.Control.ZoomOut({"title": "zoom out"});
    $(zo.div).text("-");
    $(zi.div).text("+");
    $(ze.div).text("0");
    var cpanel = new OpenLayers.Control.Panel({"defaultControl": ze});
    cpanel.addControls([zo, ze, zi]);
    this.map.map.addControl(cpanel);
    */

    // short-circuit panTo method to ease, even if we're at
    // a high zoom level
    /*Sourcemap.map_instance.map.panTo = function(lonlat) {
        if(true) {
            if (!this.panTween) {
                this.panTween = new OpenLayers.Tween(this.panMethod);
            }
            var center = this.getCenter();

            // center will not change, don't do nothing
            if (lonlat.lon == center.lon &&
                lonlat.lat == center.lat) {
                return;
            }

            var from = {
                lon: center.lon,
                lat: center.lat
            };
            var to = {
                lon: lonlat.lon,
                lat: lonlat.lat
            };
            this.panTween.start(from, to, this.panDuration, {
                callbacks: {
                    start: OpenLayers.Function.bind(function(lonlat) {
                        this.events.triggerEvent("movestart");
                    }, this),
                    eachStep: OpenLayers.Function.bind(function(lonlat) {
                        lonlat = new OpenLayers.LonLat(lonlat.lon, lonlat.lat);
                        this.moveTo(lonlat, this.zoom, {
                            'dragging': true,
                            'noEvent': true
                        });
                    }, this),
                    done: OpenLayers.Function.bind(function(lonlat) {
                        lonlat = new OpenLayers.LonLat(lonlat.lon, lonlat.lat);
                        this.moveTo(lonlat, this.zoom, {
                            'noEvent': true
                        });
                        this.events.triggerEvent("moveend");
                    }, this)
                }
            });
        } else {
            this.setCenter(lonlat);
        }
    }*/
}

Sourcemap.Map.Embed.prototype.initEvents = function() {
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
    // embed activity fades 
    $("html").mouseenter(function() {
        if($(".sourcemap-tour-control-panel").length >=1) {        
            $(".sourcemap-tour-control-panel")
                .fadeIn("fast");
        }
    });
    $("html").mousemove(function() {
        if($(".sourcemap-tour-control-panel").length>=1) {
            $(".sourcemap-tour-control-panel")
            .fadeIn("fast");
            $("html").unbind("mousemove");
        }
    });
    $("html").mouseleave(function() {
        $(".sourcemap-tour-control-panel")
            .fadeOut("fast");
    });
    
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
                $(this).children("embed").width($('#detail-content').width()).height($(this).width() * 0.8);                                       
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

        /*if($(this.embed_dialog).css("display") == "block") {
            var shrink = $(window).height() 
                         - $(this.embed_dialog).outerHeight();
            $(this.map.map.div).css({"height":shrink});
        }   */
        
    }, this));

}

Sourcemap.Map.Embed.prototype.initTour = function() {
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

Sourcemap.Map.Embed.prototype.initBanner = function(sc) {
    this.banner_div = $('<div id="embed-banner"></div>');
    $(this.map.map.div).append(this.banner_div);
    if(!sc) {
        // todo: this is bad, but it's worst case
        sc = false;
        for(var k in this.map.supplychains) {
            sc = this.map.supplychains[k];
            break;
        }
    }
    Sourcemap.template('embed/overlay/supplychain', function(p, tx, th) {
        $(this.banner_div).html(th);
    }, sc, this, this.options.tpl_base_path);
    return this;
}

Sourcemap.Map.Embed.prototype.initDialog = function() {
   
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
        .append(this.dialog_prev_el)
        .append(this.dialog_content)
        .append(this.dialog_next_el);
    $(this.dialog).data("state", 1); // todo: check this?

    // close on click-out
    this.map.map.events.on({
        "click": function(e) {
            if($(this.dialog).data("state")) {
                this.hideDialog();
            }
            this.tour.stop();//.wait();
        },
        "scope": this 
    });

    // Setup dimmer
    if(!this.curtain) {
        this.curtain = $('<div id="curtain" class="hidden"></div>');
        $(this.map.map.div).append(this.curtain);
        this.curtain.click($.proxy(function() {
            this.hideDialog();
        }, this));
    }
}

Sourcemap.Map.Embed.prototype.showDialog = function(mkup, no_controls) {
    if(this.dialog) {
        $(this.curtain).removeClass("hidden").fadeIn();
        // update dialog content and position
        if(mkup && no_controls) {
            $(this.dialog).html(mkup); // wipe controls
        } else if(mkup) {
            this.dialog.empty();
            this.initDialog();
            $(this.dialog_content).html(mkup);
        }
        
        this.map.controls.select.unselectAll();
        
        $(window).resize();
        
        var fade = $(this.dialog).css("display") == "block" ? 0 : 100;
        $(this.dialog).fadeIn(fade, function() {}).data("state", 1);
        
        this.tour.stop();
    }
}

Sourcemap.Map.Embed.prototype.hideDialog = function() {
    if(this.dialog) {
        $("#curtain").fadeOut(function() {$(this).addClass("hidden")});           
        this.dialog_content.empty();
        $(this.dialog).find('#detail-nav').css({"height": "auto"}).hide();
        $(this.dialog).hide().data("state", 0);
    }
}

Sourcemap.Map.Embed.prototype.showStopDetails = function(stid, scid, seq_idx) {
   // make sure the target magic word index is valid
    var seq_idx = seq_idx ? parseInt(seq_idx) : 0;
    
    // sync tour
    var tftrs = this.tour.features;
    for(var tfi=0; tfi< tftrs.length; tfi++) {
        var tfattrs = tftrs[tfi].attributes;
        if(tfattrs.stop_instance_id && tfattrs.stop_instance_id == stid) {
            this.tour.ftr_index = tfi;
            break;
        }
    }

    // load stop details template and show in embed detail pane
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
    Sourcemap.template('embed/details/stop', function(p, tx, th) {
            $(this.embed.dialog_content).empty();
            this.embed.showDialog(th);
            $(this.embed.dialog_content).find('.content-item a').click($.proxy(function(evt) {
                var clicked_idx = parseInt(evt.target.parentNode.id.split('-').pop());
                var idx = -1;
                for(var i=0; i<this.embed.magic_word_sequence.length; i++) {
                    if(clicked_idx === i) {
                        idx = i;
                        break;
                    }
                }
                if(idx >= 0) {
                    this.embed.showStopDetails(
                        this.stop.instance_id, this.supplychain.instance_id, idx
                    );
                }
            }, this));
        }, 
        {"stop": stop, "supplychain": sc, "magic_word": magic_word, 'embed': this},
        {"embed": this, "magic_word": magic_word, "stop": stop, "supplychain": sc},
        this.options.tpl_base_path
    );
}

Sourcemap.Map.Embed.prototype.showHopDetails = function(hid, scid) {
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

    // load hop details template and show in embed detail pane
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
    Sourcemap.template('embed/details/hop', function(p, tx, th) {
            $(this.embed.dialog_content).empty();
            this.embed.showDialog(th);
            $(this.embed.dialog_content).find('.content-item a').click($.proxy(function(evt) {
                var clicked_idx = parseInt(evt.target.parentNode.id.split('-').pop());
                var idx = -1;
                for(var i=0; i<this.embed.magic_word_sequence.length; i++) {
                    if(clicked_idx === i) {
                        idx = i;
                        break;
                    }
                }
                if(idx >= 0) {
                    this.embed.showHopDetails(
                        this.hop.instance_id, this.supplychain.instance_id, idx
                    );
                }
            }, this));
        }, 
        {"hop": hop, "supplychain": sc, "magic_word": magic_word, 'embed': this},
        {"embed": this, "magic_word": magic_word, "hop": hop, "supplychain": sc},
        this.options.tpl_base_path
    );
}

Sourcemap.Map.Embed.prototype.dialogNext = function() {
    if($(this.dialog).data("state") === -1) return;
    var nxt_seq_idx = this.magic_word_sequence_cur_idx >= 0 && this.magic_word_sequence_cur_idx < this.magic_word_sequence.length - 1 ?
        this.magic_word_sequence_cur_idx + 1 : -1;
    if(nxt_seq_idx < 0) {
        this.tour.next();
        nxt_seq_idx = 0;
    }
    this.magic_word_sequence_cur_idx = nxt_seq_idx;
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

Sourcemap.Map.Embed.prototype.dialogPrev = function() {
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

Sourcemap.Map.Embed.prototype.dialogClose = function() {
    if($(this.dialog).data("state")) {
        this.hideDialog();
    }
    this.tour.stop();//.wait();
}

Sourcemap.Map.Embed.prototype.showLocationDialog = function(msg) {
    var msg = msg ? msg : false;
    $(this.dialog).data("state", -1);
    Sourcemap.template("embed/location", function(p, txt, th) {
        this.showDialog(th, true);
        $(this.dialog).find('#update-user-loc').click($.proxy(function(evt) {
            var new_loc = $(this.embed.dialog).find('#new-user-loc').val();
            if(this.embed.user_loc && (new_loc === this.embed.user_loc.placename)) {
                // pass, no change
                this.embed.mapUserLoc();
                this.embed.hideDialog();
            } else {
                $.ajax({"url": 'services/geocode', "type": "GET",
                    "success": $.proxy(function(resp) {
                        if(resp && resp.results) {
                            this.embed.user_loc = resp.results[0];
                            this.embed.showLocationConfirm();
                        } else {
                            // no results!
                            this.embed.showLocationDialog('Sorry, that location could not be found.');
                        }
                    }, this),
                    "error": function(resp) {
                    }, "data": {"placename": new_loc},
                    "processData": true
                });
            }
        }, {"embed": this}));
    }, {"embed": this, "err_msg": msg, "user_loc": this.user_loc}, this)
}

Sourcemap.Map.Embed.prototype.showLocationConfirm = function() {
    $(this.dialog).data("state", -1);
    Sourcemap.template('embed/location/confirm', function(p, tx, th) {
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

Sourcemap.Map.Embed.prototype.mapUserLoc = function() {
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
