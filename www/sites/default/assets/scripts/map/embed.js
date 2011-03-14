jQuery.fn.overlay_center = function () {
    this.css("position","absolute");
    this.css("top", ($(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ($(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

Sourcemap.magic = {
    "youtube": {
        "link": function(lnk) {
            var mkup = '<iframe title="YouTube video player" width="400" height="300" '+
                'src="http://www.youtube.com/embed/'+(lnk.match(/((\?v=)|(v\/))(.+)$/))[4]+'" frameborder="0" '+
                'allowfullscreen></iframe>';
            return mkup;
        }
    },
    "vimeo": {
        "link": function(lnk) {
            var mkup = '<iframe src="http://player.vimeo.com/video/'+
                (lnk.match(/\/(\d+)$/))[1]+'?title=0&amp;byline=0&amp;portrait=0" '+
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
                return Sourcemap.embed_dialog_show($(Sourcemap.embed_dialog_content).html());
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
        // load stop details template and show in embed dialog
        var sc = Sourcemap.map_instance.supplychains[scid];
        var stop = sc.findStop(stid);
        var magic_word = Sourcemap.magic_seq[seq_idx];
        while(stop.getAttr(magic_word, false) === false && seq_idx < Sourcemap.magic_seq.length-1) {
            magic_word = Sourcemap.magic_seq[++seq_idx];
        }
        if(stop.getAttr(magic_word, false) === false) magic_word = false;
        $(Sourcemap.embed_dialog).data("state", -1); // loading
        Sourcemap.embed_dialog_hide();
        Sourcemap.template('embed/details/stop', function(p, tx, th) {
            Sourcemap.embed_dialog_show(th);
        }, {"stop": stop, "supplychain": sc, "magic_word": magic_word});
    }

    Sourcemap.embed_hop_details = function(hop, sc) {
        // load hop details template and show in embed dialog
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

    // get scid from inline script
    var scid = Sourcemap.embed_supplychain_id;

    // fetch supplychain
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.map_instance.addSupplychain(sc);
    });

    // wait for map to load, then add dressing
    $(document).bind('map:supplychain_mapped', function(evt, map) {
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
                var order = g.depthFirstOrder();
                order = order.concat(g.islands());
                for(var i=0; i<order.length; i++)
                    features.push(map.mapped_features[order[i]]);
            }
            
            // initialize tour
            Sourcemap.map_tour = new Sourcemap.MapTour(Sourcemap.map_instance, {
                "features": features, "wait_interval": Sourcemap.embed_params.tour_start_delay,
                "interval": Sourcemap.embed_params.tour_interval
            });
        } else {
            // no tour
            Sourcemap.map_tour = false;
        }
        // the line below has to be here because, otherwise,
        // openlayers doesn't properly position the popup in webkit (chrome)
        Sourcemap.map_instance.map.zoomIn();

        // zoom to bounds of stops layer
        Sourcemap.map_instance.map.zoomToExtent(
            Sourcemap.map_instance.getStopLayer(sc.instance_id).getDataExtent()
        );

        // set up banner overlay. todo: make this optional.
        var overlay = $('<div class="sourcemap-embed-overlay" id="map-overlay"></div>');
        
        if(Sourcemap.embed_params && Sourcemap.embed_params.banner) {
            Sourcemap.map_overlay = overlay;
            $(Sourcemap.map_instance.map.div).css("position", "relative");
            $(Sourcemap.map_instance.map.div).append(overlay);
            Sourcemap.template('embed/overlay/supplychain', function(p, tx, th) {
                $(Sourcemap.map_overlay).html(th);
            }, sc);
        }

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
            Sourcemap.embed_dialog_hide();
        });

        // set body font-size to a constant(ish) factor based on doc width
        // TODO Need to threshold this
        //$(document.body).css("font-size", Math.floor(document.body.clientWidth / 65)+"px");

        // set up dialog
        Sourcemap.embed_dialog = $('<div id="embed-dialog" class="map-dialog"></div>');
        Sourcemap.embed_dialog_prev_el = $('<div id="embed-dialog-prev" class="map-dialog-nav prev"><a href="javascript:Sourcemap.embed_dialog_prev();">&laquo;</a></div>');
        Sourcemap.embed_dialog_next_el = $('<div id="embed-dialog-next" class="map-dialog-nav next"><a href="javascript:Sourcemap.embed_dialog_next();">&raquo;</a></div>');
        Sourcemap.embed_dialog_content = $('<div id="embed-dialog-content" class="content"></div>');
        Sourcemap.embed_dialog.append(Sourcemap.embed_dialog_prev_el)
            .append(Sourcemap.embed_dialog_content).append(Sourcemap.embed_dialog_next_el);
        $(document.body).append(Sourcemap.embed_dialog);
        $(Sourcemap.embed_dialog).data("state", 1);
        Sourcemap.embed_dialog_show = function(mkup) {
            // update dialog content and position
            if(mkup) $(Sourcemap.embed_dialog_content).html(mkup);

            Sourcemap.map_instance.controls.select.unselectAll();
            
            var h = $(Sourcemap.embed_dialog).innerHeight();
            $(Sourcemap.embed_dialog).find('.map-dialog-nav')
                .css({"height": h}).show();
            
            var h2 = ($(Sourcemap.embed_dialog).outerHeight() / 2);
            var dt  = Math.floor(($(Sourcemap.map_instance.map.div).innerHeight()-(h2*2)) / 2);
            var w2 = ($(Sourcemap.embed_dialog).outerWidth() / 2);
            var dl = Math.floor(($(Sourcemap.map_instance.map.div).innerWidth() - (w2*2)) / 2);
            $(Sourcemap.embed_dialog).css({"left": dl+"px"});
            $(Sourcemap.embed_dialog).css({"top": dt+"px"});
            
            $(Sourcemap.embed_dialog).show().data("state", 1);
            Sourcemap.map_tour.stop();
        }
        Sourcemap.embed_dialog_hide = function() {
            $(Sourcemap.embed_dialog_content).empty();
            $(Sourcemap.embed_dialog).find('.map-dialog-nav').css({"height": "auto"}).hide();
            $(Sourcemap.embed_dialog).hide().data("state", 0);
        }
        Sourcemap.embed_dialog_hide();
    });


    // side-scrolling next/prev for browsing content
    Sourcemap.embed_dialog_next = function() {
        if($(Sourcemap.embed_dialog).data("state") === -1) return;
        Sourcemap.embed_dialog_hide();
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
                if(magic_word && stop.getAttr(magic_word, false) === false) {
                    magic_word = false;
                }
                if(!magic_word) nxt_seq_idx++;
            };
            if(!magic_word) {
                Sourcemap.magic_seq_cur = -1;
                return Sourcemap.embed_dialog_next();
            }
            Sourcemap.magic_seq_cur = nxt_seq_idx;
            Sourcemap.embed_stop_details(stop.instance_id, scid, Sourcemap.magic_seq_cur); 
        } else if(hop = Sourcemap.map_tour.getCurrentHop()) {
            Sourcemap.embed_hop_details(hop.instance_id, scid, Sourcemap.magic_seq_cur);
        } else {
            throw new Error('Unexpected feature...not a stop or a hop.');
        }
    };
    Sourcemap.embed_dialog_prev = function() {
        if($(Sourcemap.embed_dialog).data("state") === -1) return;
        Sourcemap.embed_dialog_hide();
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
                if(magic_word && stop.getAttr(magic_word, false) === false)
                    magic_word = false;
                if(!magic_word) prv_seq_idx--;
            };
            if(!magic_word) {
                Sourcemap.magic_seq_cur = 0;
                return Sourcemap.embed_dialog_prev();
            }
            Sourcemap.magic_seq_cur = prv_seq_idx;
            Sourcemap.embed_stop_details(stop.instance_id, scid, Sourcemap.magic_seq_cur); 
        } else if(hop = Sourcemap.map_tour.getCurrentHop()) {
            Sourcemap.embed_hop_details(hop.instance_id, scid, Sourcemap.magic_seq_cur);
        } else {
            throw new Error('Unexpected feature...not a stop or a hop.');
        }
    };


});
