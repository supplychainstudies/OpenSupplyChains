jQuery.fn.overlay_center = function () {
    this.css("position","absolute");
    this.css("top", ($(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ($(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

Sourcemap.magic = {
    "youtube": {
        "link": function(lnk) {
            if(!lnk || !lnk.match(/((\?v=)|(v\/))(.+)$/))
                return '<p class="error">Invalid YouTube link.</p>';
            var mkup = '<iframe class="youtube-player" type="text/html" width="400" height="300" '+
                'src="http://www.youtube.com/embed/'+(lnk.match(/((\?v=)|(v\/))(.+)$/))[4]+'?autoplay=1"'+ 
                'frameborder="0" allowfullscreen></iframe>';
            return mkup;
        }
    },
    "vimeo": {
        "link": function(lnk) {
            var mkup = '<iframe class="vimeo-player" src="http://player.vimeo.com/video/'+
                (lnk.match(/\/(\d+)$/))[1]+'?title=0&amp;byline=0&amp;portrait=0&autoplay=1" '+
                'width="400" height="300" frameborder="0"></iframe>';
            return mkup;
        }
    },
    "flickr": {
        "api_key": "06ea60fff75fc5721cfd11d823634ab8",
        "setid": function(setid, elid) {
            var url = "http://www.flickr.com/services/rest/?jsoncallback=?";
            $.getJSON(url, {
                "method": "flickr.photosets.getPhotos", "format": "json",
                "api_key": Sourcemap.magic.flickr.api_key, "photoset_id": setid
            }, function(data) {
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
                    var mkup = '<object width="400" height="300"> <param name="flashvars" value="offsite=true&lang=en-us&page_show_url=%2Fphotos%2F'+
                        data.photoset.ownername.toLowerCase()+'%2Fsets%2F'+setid+'%2Fshow%2F&page_show_back_url=%2Fphotos%2F'+
                        data.photoset.ownername.toLowerCase()+'%2Fsets%2F'+setid+'%2F&set_id='+setid+'&jump_to="></param> '+
                        '<param name="movie" value="http://www.flickr.com/apps/slideshow/show.swf?v=71649"></param> '+
                        '<param name="allowFullScreen" value="true"></param><embed type="application/x-shockwave-flash" '+
                        'src="http://www.flickr.com/apps/slideshow/show.swf?v=71649" allowFullScreen="true" '+
                        'flashvars="offsite=true&lang=en-us&page_show_url=%2Fphotos%2F'+data.photoset.ownername.toLowerCase()+
                        '%2Fsets%2F'+setid+'%2Fshow%2F&page_show_back_url=%2Fphotos%2F'+data.photoset.ownername.toLowerCase()+
                        '%2Fsets%2F'+setid+'%2F&set_id='+setid+'&jump_to=" width="400" height="300"></embed></object>';
                } else {
                    var mkup = '';
                }
                $('#flickr-photoset-'+setid).html(mkup)
                return Sourcemap.embed_overlay_show($(Sourcemap.embed_overlay_content).html());
            });
            return '<div style="height: 400px; width: 300px; overflow: hidden;" class="flickr-slideshow-wrapper" id="flickr-photoset-'+setid+'">Loading...</div>';
        }
    }
};

Sourcemap.magic_seq = ['description', 'youtube:link', 'vimeo:link', 'flickr:setid'];
Sourcemap.magic_seq_cur = -1; // should be negative to advance to first feature on tour

$(document).ready(function() {
    // reset template path to site-specific location
    Sourcemap.TPL_PATH = "sites/default/assets/scripts/tpl/";

    Sourcemap.embed_stop_details = function(stid, scid, seq_idx) {
        var seq_idx = seq_idx ? parseInt(seq_idx) : 0;
        
        // sync tour
        var tftrs = Sourcemap.map_tour.features;
        for(var tfi=0; tfi< tftrs.length; tfi++) {
            var tfattrs = tftrs[tfi].attributes;
            if(tfattrs.stop_instance_id && tfattrs.stop_instance_id == stid) {
                Sourcemap.map_tour.ftr_index = tfi;
                break;
            }
        }

        // load stop details template and show in embed overlay
        var sc = Sourcemap.map_instance.supplychains[scid];
        var stop = sc.findStop(stid);

        // get magic word...make sure it's valid
        var magic_word = Sourcemap.magic_seq[seq_idx];
        while(((stop.getAttr(magic_word, false) === false) || (!stop.getAttr(magic_word).length || stop.getAttr(magic_word).length == 1)) 
            && seq_idx < Sourcemap.magic_seq.length-1) {
            magic_word = Sourcemap.magic_seq[++seq_idx];
        }
        
        // sync cur seq idx
        Sourcemap.magic_seq_cur = seq_idx;

        if(stop.getAttr(magic_word, false) === false) magic_word = false;

        $(Sourcemap.embed_overlay).data("state", -1); // loading

        Sourcemap.template('embed/details/stop', $.proxy(function(p, tx, th) {
            Sourcemap.embed_overlay_show(th);
        }), 
        {"stop": stop, "supplychain": sc, "magic_word": magic_word});
    }

    Sourcemap.embed_hop_details = function(hop, sc) {
        // load hop details template and show in embed overlay
    }

    // initialize new map
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-embed', {
        "prep_popup": function(ref, ftr, pop) {
            var t = ['popup'];
            var tscope = {"popup": pop, "feature": ftr};
            if(ref instanceof Sourcemap.Stop) {
                t.push('stop');
                tscope.stop = ref;
            } else if(ref instanceof Sourcemap.Hop) {
                t.push('hop');
                tscope.hop = ref;
            }
            var hasmagic = false;
            for(var ski=0; ski<Sourcemap.magic_seq.length; ski++) {
                var sk = Sourcemap.magic_seq[ski];
                if(ref.getAttr(sk, false))
                    hasmagic = true;
            }
            tscope.morelink = hasmagic;
            Sourcemap.template('embed/'+t.join('-'), $.proxy(function(p, tx, th) {
                $(this.popup.contentDiv).html(th);
                this.popup.updateSize();
                this.popup.updatePosition();
            }, tscope), tscope);
        },
        "prep_stop": function(stop, ftr) {
            var sz = 5;
            var vol = parseFloat(stop.getAttr("percentage"));
            if(!isNaN(vol)) {
                if(vol < 1) {
                    sz = 5;
                } else if(vol < 20) {
                    sz = 10;
                } else if(vol < 70) {
                    sz = 14;
                } else {
                    sz = 48;
                }
            }
            ftr.attributes.size = sz;
            var color = stop.getAttr("color", null);
            var cat = stop.getAttr('category');
            switch(cat) {
                case "FS":
                    color = "#66cc33";
                    break;
                case "D":
                    color = "#339933";
                    break;
                default:
                    color = "#006633";
                    break;
            }
            if(color)
                ftr.attributes.color = color;
        }
    });

    // short-circuit panTo method to ease, even if we're at
    // a high zoom level
    Sourcemap.map_instance.map.panTo = function(lonlat) {
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
    }

    // get scid from inline script
    var scid = Sourcemap.embed_supplychain_id;

    // fetch supplychain
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.map_instance.addSupplychain(sc);
    });

    // wait for map to load, then add dressing
    Sourcemap.listen('map:supplychain_mapped', function(evt, map) {
        // die quietly if map fails
        if(map !== Sourcemap.map_instance)
            return;
       
        // tour?
        if(Sourcemap.embed_params && Sourcemap.embed_params.tour) {
            // build list of features for tour
            var features = [];
            for(var k in map.supplychains) {
                var sc = map.supplychains[k];
                var g = new Sourcemap.Supplychain.Graph(map.supplychains[k]);
                var order = g.depthFirstOrder(true); // upstream
                order = order.concat(g.islands());
                for(var i=0; i<order.length; i++)
                    features.push(map.mapped_features[order[i]]);
            }
            
            // initialize tour
            Sourcemap.map_tour = new Sourcemap.MapTour(Sourcemap.map_instance, {
                "features": features, "wait_interval": Sourcemap.embed_params.tour_start_delay,
                "interval": Sourcemap.embed_params.tour_interval
            });
            
            Sourcemap.listen('map:feature_selected', function(evt, map, ftr) {
                Sourcemap.map_instance.controls.select.unselectAll({"except": ftr});
                if(Sourcemap.map_tour.timeout) Sourcemap.map_tour.stop();
            });
            
            Sourcemap.listen('map_tour:positionchange', function(evt, maptour) {
                var currentindex = maptour.ftr_index+1;
                var totalcount = maptour.features.length+1;
                var widthpercent = (currentindex/totalcount*100*.8)+"%";
                $(".tour-progress-bar").css({"width":widthpercent});

                if($(Sourcemap.embed_overlay).data("state") == 1) {
                    Sourcemap.embed_stop_details(
                        Sourcemap.map_tour.getCurrentStop().instance_id, 
                        Sourcemap.map_tour.getCurrentFeature().attributes.supplychain_instance_id, 0
                    );
                }
              
            });
            // Setup embed activity fades 
            $("html").mouseenter(function() {
                $("#embed-banner, #tileswitcher, .sourcemap-tour-control-panel, .olControlPanel")
                    .fadeIn("fast");
            });
            $("html").mouseleave(function() {
                $("#embed-banner, #tileswitcher, .sourcemap-tour-control-panel, .olControlPanel")
                    .fadeOut("fast");
            });
 
        } else {
            // no tour
            Sourcemap.map_tour = false;
        }
        // the line below has to be here because, otherwise,
        // openlayers doesn't properly position the popup in webkit (chrome)
        Sourcemap.map_instance.map.zoomIn();

        // zoom to bounds of stops layer
        var zoomOffset = 0;
        var zoom = zoomOffset + Sourcemap.map_instance.map.getZoomForExtent(
            Sourcemap.map_instance.getStopLayer(sc.instance_id).getDataExtent()
        );
        
        Sourcemap.map_instance.map.zoomTo(zoom);

        // set up banner overlay. todo: make this optional.
        var overlay = $('<div id="embed-banner"></div>');
        if(Sourcemap.embed_params && Sourcemap.embed_params.banner) {
            Sourcemap.map_overlay = overlay;
            $(Sourcemap.map_instance.map.div).css("position", "relative");
            $(Sourcemap.map_instance.map.div).append(overlay);
            Sourcemap.template('embed/overlay/supplychain', function(p, tx, th) {
                $(Sourcemap.map_overlay).html(th);
            }, sc);
        }

        // Set up dialog
        Sourcemap.embed_dialog = $('<div id="embed-dialog"><h1>Message!</h1><p>Some text...</p></div>');       
        $(document.body).prepend(Sourcemap.embed_dialog);
        Sourcemap.template('embed/overlay/dialog', function(p, tx, th) {
        }, sc);
        Sourcemap.dialog_show = function() {
            Sourcemap.embed_dialog.slideDown("normal", function() {
                var shrink = $(Sourcemap.map_instance.map.div).outerHeight() 
                             - $(Sourcemap.embed_dialog).outerHeight();
                $(Sourcemap.map_instance.map.div).css({"height":shrink});
            });            
        }
        Sourcemap.dialog_hide = function() {
            Sourcemap.embed_dialog.slideUp("normal", function() {
                $(Sourcemap.map_instance.map.div).css({"height":"100%"});
            });
        }
                
        // Set up tileswithcer 
        if(Sourcemap.embed_params && Sourcemap.embed_params.tileswitcher) {       
            var tileswitcher = $('<div id="tileswitcher" class="terrain"><div id="current-tile">Terrain</div><ul id="available-tiles"><li id="styled"></li><li id="terrain"></li><li id="satellite"></li></ul></div>');
            $(Sourcemap.map_instance.map.div).append(tileswitcher);
            $("#tileswitcher #available-tiles li").click(function() {
                var newtile = $(this).attr("id");
                $("#tileswitcher").attr("class",  newtile);
                $("#tileswitcher #current-tile").text(newtile);

                // This is a little wonky, sorry
                if(newtile == "terrain") {
                   Sourcemap.map_instance.map.setBaseLayer(
                       Sourcemap.map_instance.map.getLayersByName("Google Streets").pop()
                   );
                }       
                else if(newtile == "styled") {
                   Sourcemap.map_instance.map.setBaseLayer(
                       Sourcemap.map_instance.map.getLayersByName("Cloudmade").pop()
                   );                   
                }  
                else if(newtile == "satellite") {
                   Sourcemap.map_instance.map.setBaseLayer(
                       Sourcemap.map_instance.map.getLayersByName("Google Satellite").pop()
                   );   
                }
            });
        }
        
        // Setup watermark    
        var watermark = $('<div id="watermark"></div>');
        $(Sourcemap.map_instance.map.div).append(watermark);
        
        // Setup dimmer    
        var dimmed = $('<div id="dimmed-overlay"></div>');
        $(Sourcemap.map_instance.map.div).append(dimmed);
        dimmed.click(function() {
            Sourcemap.embed_overlay_hide(); 
        });
        
        // make and place custom zoom controls
        var ze = new OpenLayers.Control.ZoomToMaxExtent({"title": "zoom all the way out"});
        var zi = new OpenLayers.Control.ZoomIn({"title": "zoom in"});
        var zo = new OpenLayers.Control.ZoomOut({"title": "zoom out"});
        $(zo.div).text("-");
        $(zi.div).text("+");
        $(ze.div).text("0");
        var cpanel = new OpenLayers.Control.Panel({"defaultControl": ze});
        cpanel.addControls([zo, ze, zi]);
        Sourcemap.map_instance.map.addControl(cpanel);

        // pause tour on click
        Sourcemap.map_instance.map.events.register('click', Sourcemap.map_instance, function() {
            if(Sourcemap.map_tour) Sourcemap.map_tour.stop();
            Sourcemap.embed_overlay_hide();
        });

        // set up overlay
        Sourcemap.embed_overlay = $('<div id="embed-overlay"></div>');
        Sourcemap.embed_overlay_prev_el = $('<div id="overlay-nav" class="prev"><a href="javascript:Sourcemap.embed_overlay_prev();"></a></div>');
        Sourcemap.embed_overlay_next_el = $('<div id="overlay-nav" class="next"><a href="javascript:Sourcemap.embed_overlay_next();"></a></div>');
        Sourcemap.embed_overlay_content = $('<div id="overlay-content" class="content"></div>');
        Sourcemap.embed_overlay.append(Sourcemap.embed_overlay_prev_el)
            .append(Sourcemap.embed_overlay_content).append(Sourcemap.embed_overlay_next_el);
        $(Sourcemap.map_instance.map.div).append(Sourcemap.embed_overlay);
        $(Sourcemap.embed_overlay).data("state", 1);
        Sourcemap.embed_overlay_show = function(mkup) {
            $("#dimmed-overlay").fadeIn();
            // update overlay content and position
            if(mkup) $(Sourcemap.embed_overlay_content).html(mkup);
            
            Sourcemap.map_instance.controls.select.unselectAll();
            
            var max_width = 0;
            $('#overlay-content > *').each(function(){
             var this_width = $(this).width();
             if (this_width > max_width) { max_width = this_width;}
            }); 
            $(Sourcemap.embed_overlay).width((max_width/.8));
            
            var total_height = 0;           
            $(Sourcemap.embed_overlay).css({"display":"block"});
            $('#overlay-content > *').each(function(){
                var this_height = $(this).height();
                total_height += this_height;      
            });
            $(Sourcemap.embed_overlay).css({"display":"none"});
            // Todo - this should not be hardcoded to 100, but it works.... need to understand the calc
            $(Sourcemap.embed_overlay).height((total_height+100));

            var h = $(Sourcemap.embed_overlay).height();
            $(Sourcemap.embed_overlay).find('#overlay-nav')
                .css({"height": h}).show();
  
            var h2 = ($(Sourcemap.embed_overlay).outerHeight() / 2);
            var dt  = Math.floor(($(Sourcemap.map_instance.map.div).innerHeight()-(h2*2)) / 2);
            var w2 = ($(Sourcemap.embed_overlay).outerWidth() / 2);
            var dl = Math.floor(($(Sourcemap.map_instance.map.div).innerWidth() - (w2*2)) / 2);
            $(Sourcemap.embed_overlay).css({"left": dl+"px"});
            $(Sourcemap.embed_overlay).css({"top": dt+"px"});
            
            var fade = $(Sourcemap.embed_overlay).css("display") == "block" ? 0 : 100;
            $(Sourcemap.embed_overlay).fadeIn(fade, function() {
            }).data("state", 1);
            
            Sourcemap.map_tour.stop();
        }
        Sourcemap.embed_overlay_hide = function() {
            $("#dimmed-overlay").fadeOut();           
            Sourcemap.embed_overlay_content.empty();
            $(Sourcemap.embed_overlay).find('#overlay-nav').css({"height": "auto"}).hide();
            $(Sourcemap.embed_overlay).hide().data("state", 0);
            Sourcemap.map_tour.start();           
        }
        Sourcemap.embed_overlay_hide();
        $(Sourcemap.map_instance.map.div).css("background", "#eeeeee");        
    });
    
    // side-scrolling next/prev for browsing content
    Sourcemap.embed_overlay_next = function() {
        if($(Sourcemap.embed_overlay).data("state") === -1) return;
        var nxt_seq_idx = Sourcemap.magic_seq_cur >= 0 && Sourcemap.magic_seq_cur < Sourcemap.magic_seq.length - 1 ?
            Sourcemap.magic_seq_cur + 1 : -1;
        if(nxt_seq_idx < 0) {
            Sourcemap.map_tour.next();
            nxt_seq_idx = 0;
        }
        Sourcemap.magic_seq_cur = nxt_seq_idx;
        var cftr = Sourcemap.map_tour.getCurrentFeature();
        if(!cftr) {
            if(Sourcemap.map_tour.features.length) {
                cftr = Sourcemap.map_tour.features[0];
                Sourcemap.map_tour.ftr_index = 0;
            } else {
                return;
            }
        }
        var scid = cftr.attributes.supplychain_instance_id;
        var stop, hop;
        if(stop = Sourcemap.map_tour.getCurrentStop()) {
            var magic_word = false;
             while(!magic_word && nxt_seq_idx < Sourcemap.magic_seq.length) {
                magic_word = Sourcemap.magic_seq[nxt_seq_idx];
                if(magic_word && stop.getAttr(magic_word, false) == false) {
                    magic_word = false;
                }
                if(!magic_word) nxt_seq_idx++;
            };
            if(!magic_word) {
                Sourcemap.magic_seq_cur = -1;
                return Sourcemap.embed_overlay_next();
            }
            Sourcemap.magic_seq_cur = nxt_seq_idx;
            Sourcemap.embed_stop_details(stop.instance_id, scid, Sourcemap.magic_seq_cur); 
        } else if(hop = Sourcemap.map_tour.getCurrentHop()) {
            Sourcemap.embed_hop_details(hop.instance_id, scid, Sourcemap.magic_seq_cur);
        } else {
            throw new Error('Unexpected feature...not a stop or a hop.');
        }
    };
    Sourcemap.embed_overlay_prev = function() {
        if($(Sourcemap.embed_overlay).data("state") === -1) return;
        var prv_seq_idx = Sourcemap.magic_seq_cur >= 0 ? Sourcemap.magic_seq_cur - 1 : 0;
        if(prv_seq_idx < 0) {
            Sourcemap.map_tour.prev();
            prv_seq_idx = Sourcemap.magic_seq.length;
        }
        Sourcemap.magic_seq_cur = prv_seq_idx;
        var cftr = Sourcemap.map_tour.getCurrentFeature();
        if(!cftr) {
            if(Sourcemap.map_tour.features.length) {
                cftr = Sourcemap.map_tour.features[Sourcemap.map_tour.features.length-1];
                Sourcemap.map_tour.ftr_index = Sourcemap.map_tour.features.length-1;
            } else {
                return;
            }
        }
        var scid = cftr.attributes.supplychain_instance_id;
        var stop, hop;
        if(stop = Sourcemap.map_tour.getCurrentStop()) {
            var magic_word = false;
             while(!magic_word && prv_seq_idx >= 0) {
                magic_word = Sourcemap.magic_seq[prv_seq_idx];
                if(magic_word && stop.getAttr(magic_word, false) == false)
                    magic_word = false;
                if(!magic_word) prv_seq_idx--;
            };
            if(!magic_word) {
                Sourcemap.magic_seq_cur = 0;
                return Sourcemap.embed_overlay_prev();
            }
            Sourcemap.magic_seq_cur = prv_seq_idx;
            Sourcemap.embed_stop_details(stop.instance_id, scid, Sourcemap.magic_seq_cur); 
        } else if(hop = Sourcemap.map_tour.getCurrentHop()) {
            Sourcemap.embed_hop_details(hop.instance_id, scid, Sourcemap.magic_seq_cur);
        } else {
            throw new Error('Unexpected feature...not a stop or a hop.');
        }
    };

    // Misc UI things
    $("body").css("font-size", Math.min(100,Math.floor(document.body.clientWidth / 1020 * 100))+"%");
    
    $(window).resize(function () { 
        $("body").css("font-size", Math.min(100,Math.floor(document.body.clientWidth / 1020 * 100))+"%");
        
        var max_width = 0;
        $('#overlay-content > *').each(function(){
         var this_width = $(this).width();
         if (this_width > max_width) { max_width = this_width;}
        }); 
        $(Sourcemap.embed_overlay).width((max_width/.8));
        
        var total_height = 0;           
        $('#overlay-content > *').each(function(){
            var this_height = $(this).height();
            total_height += this_height;      
        });
        // Todo - this should not be hardcoded to 100, but it works.... need to understand the calc
        $(Sourcemap.embed_overlay).height((total_height+100));
        
        var h = $(Sourcemap.embed_overlay).height();
        $(Sourcemap.embed_overlay).find('#overlay-nav').css({"height": h}).show();
        var h2 = ($(Sourcemap.embed_overlay).outerHeight() / 2);
        var dt  = Math.floor(($(Sourcemap.map_instance.map.div).innerHeight()-(h2*2)) / 2);
        var w2 = ($(Sourcemap.embed_overlay).outerWidth() / 2);
        var dl = Math.floor(($(Sourcemap.map_instance.map.div).innerWidth() - (w2*2)) / 2);
        $(Sourcemap.embed_overlay).css({"left": dl+"px"});
        $(Sourcemap.embed_overlay).css({"top": dt+"px"});    
        
        if(Sourcemap.embed_dialog.css("display") == "block") {
            var shrink = $(window).height() 
                         - $(Sourcemap.embed_dialog).outerHeight();
            $(Sourcemap.map_instance.map.div).css({"height":shrink});
        }   
        
    });
    
}); 
