Sourcemap.MapTour = function(map, o) {
    if(!(map instanceof Sourcemap.Map)) {
        throw new Error('MapTour requires a map instance.');
    }
    this.map = map;
    this.features = o.features || [];
    this.ftr_index = -1;
    this.timeout = null;
    this.instance_id = Sourcemap.instance_id('sourcemap-tour');
    this.stopped = false;
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.MapTour.prototype.defaults = {
    "auto_init": true, "interval": 2.5, // seconds
    "wait_interval": 5, // seconds
    "tour_hops": false, "tour_stops": true,
    "pan_easing": true
};

Sourcemap.MapTour.prototype.init = function() {
    this.interval = this.options.interval > 0 ? this.options.interval * 1000 : 1000;
    this.wait_interval = this.options.wait_interval > 0 ? this.options.wait_interval : this.defaults.wait_interval;
    this.initEvents();
    this.initControls();
    this.features = this.options.features ? this.options.features : this.getFeatures();
    this.wait();
    Sourcemap.broadcast('map_tour:init', this);
    return this;
}

Sourcemap.MapTour.prototype.initControls = function() {
    this.controls_div_id = this.instance_id+'-controls';
    this.controls = $('<div id="'+this.controls_div_id+'" class="sourcemap-tour-control-panel">'
                    + '<div class="tour-progress-bar"></div></div>');
    this.control_prev = $('<div class="sourcemap-tour-prev"></div>');
    this.control_next = $('<div class="sourcemap-tour-next"></div>');
    this.control_play = $('<div class="sourcemap-tour-play stopped"></div>');
    $(this.map.map.div).append(this.controls);
    $(this.controls).append(this.control_prev).append(this.control_play)
        .append(this.control_next);
    this.control_prev.click($.proxy(function() {
        this.prev();
    }, this));
    this.control_next.click($.proxy(function() {
        this.next();
    }, this));
    this.control_play.click($.proxy(function() {
        if(this.timeout) {
            this.stop();
        } else {
            this.start();
        }
    }, this));
}

Sourcemap.MapTour.prototype.initEvents = function() {
    this.map.map.events.on({
        "click": function(e) {
            this.stop().wait();
        },
        "scope": this 
    });
    var map_changes = [
        'map:supplychain_added', 'map:supplychain_removed', 
        'map:supplychain_updated'
    ];
    Sourcemap.listen(map_changes, function(evt, map) {
        if(map === this.map) {
            this.stop().wait();
        }
    }, this);
    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
        if(map === this.map) {
            this.map.controls.select.unselectAll({"except": ftr});
            if(this.timeout) this.stop();
        }
    }, this));
    // callback for map tour advance or retreat event
    Sourcemap.listen('map_tour:positionchange',$.proxy(function(evt, maptour) {
        // update tour status indicator
        if(maptour === this) {
            var currentindex = maptour.ftr_index+1;
            var totalcount = maptour.features.length+1;
            var widthpercent = (currentindex/totalcount*100*.8)+"%";
            $(".tour-progress-bar").css({"width":widthpercent});

        }
    }, this));
    return this;
}

Sourcemap.MapTour.prototype.getFeatures = function(order) {
    var map = this.map;
    var features = [];
    var order = order || false;
    for(var k in map.supplychains) {
        var sc = map.supplychains[k];
        var g = new Sourcemap.Supplychain.Graph(map.supplychains[k]);
        if(!order) {
            order = g.depthFirstOrder();
            order = order.concat(g.islands());
        }
        for(var i=0; i<order.length; i++)
            features.push(map.mapped_features[order[i]]);
    }
    return features;
}

Sourcemap.MapTour.prototype.wait = function() {
    this.clearTimeout();
    $(this.control_play).removeClass("stopped");
    this.stopped = false;
    if(this.wait_interval) {
        this.timeout = setTimeout($.proxy(this.start, this), this.wait_interval*1000);
    } else {
        this.start();
    }
    return this;
}

Sourcemap.MapTour.prototype.start = function() {
    if(this.timeout) this.clearTimeout(this.timeout);
    this.stopped = false;
    Sourcemap.broadcast('map_tour:start', this);
    this.control_play.removeClass("stopped");
    for(var i=0; i<this.features.length; i++)
        this.map.controls.select.unselect(this.features[i]);
    this.next();
    return this;
}

Sourcemap.MapTour.prototype.stop = function() {
    if(this.timeout) this.clearTimeout(this.timeout);
    this.stopped = true;
    this.control_play.addClass("stopped");
    return this;
}

Sourcemap.MapTour.prototype.next = function() {
    this.clearTimeout();
    var next_index = this.ftr_index >= this.features.length ?
        0 : this.ftr_index+1;
    var current_ftr = this.features[this.ftr_index] || null;
    var next_ftr = this.features[next_index] || null;
    if(current_ftr && this.map.controls && this.map.controls.select) 
        this.map.controls.select.unselect(current_ftr);
    if(next_ftr) {
        if(this.options.pan_easing) {
            //this.map.map.zoomToExtent(this.map.getDataExtent());
            //this.map.map.panTo(this.getFeatureLonLat(next_ftr));
            //setTimeout($.proxy(function() { 
            //    this.map.map.zoomTo(this.map.map.getNumZoomLevels()-1); 
            //}, this), 3000);
            this.map.panTween = new OpenLayers.Tween();
            this.map.map.panTo(this.getFeatureLonLat(next_ftr));
        } else {
            this.map.map.moveTo(this.getFeatureLonLat(next_ftr));
            this.map.map.zoomTo(this.map.map.getNumZoomLevels()-1);
        }
        if(this.map.controls && this.map.controls.select)
            this.map.controls.select.select(next_ftr);
    }
    this.ftr_index = next_index;
    if(!this.stopped)
        this.timeout = setTimeout($.proxy(this.next, this), this.interval);
    Sourcemap.broadcast('map_tour:positionchange', this);    
}

Sourcemap.MapTour.prototype.prev = function() {
    this.clearTimeout();
    var prev_index = this.ftr_index <= 0 ?
        this.features.length-1 : this.ftr_index-1;
    var current_ftr = this.features[this.ftr_index] || null;
    var prev_ftr = this.features[prev_index] || null;
    if(current_ftr && this.map.controls && this.map.controls.select) 
        this.map.controls.select.unselect(current_ftr);
    if(prev_ftr) {
        this.map.map.panTo(this.getFeatureLonLat(prev_ftr));
        if(this.map.controls && this.map.controls.select)
            this.map.controls.select.select(prev_ftr);
    }
    this.ftr_index = prev_index;
    if(!this.stopped)
        this.timeout = setTimeout($.proxy(this.next, this), this.interval);
    Sourcemap.broadcast('map_tour:positionchange', this);    
    
}

Sourcemap.MapTour.prototype.clearTimeout = function() {
    if(this.timeout) clearTimeout(this.timeout);
    this.timeout = null;
}

Sourcemap.MapTour.prototype.getNextFeature = function() {}

Sourcemap.MapTour.prototype.getFeatureLonLat = function(ftr) {
    var ll = null;
    if(ftr.geometry && ftr.geometry instanceof OpenLayers.Geometry.Point) {
        ll = new OpenLayers.LonLat(ftr.geometry.x, ftr.geometry.y);
    } else if(ftr.geometry && ftr.geometry instanceof OpenLayers.Geometry.MultiLineString) {
        var ctr = ftr.geometry.getBounds().getCenterLonLat();
        ll = ctr;
    }
    return ll;
}

Sourcemap.MapTour.prototype.getCurrentFeature = function() {
    return this.features[this.ftr_index] || null;
}

Sourcemap.MapTour.prototype.getCurrentStop = function() {
    var st = false;
    var f = this.getCurrentFeature();
    if(f) {
        var scid = f.attributes.supplychain_instance_id;
        if(f.attributes.stop_instance_id) {
            var stid = f.attributes.stop_instance_id;
            st = this.map.findSupplychain(scid).findStop(stid);
        }
    }
    return st;
}

Sourcemap.MapTour.prototype.getCurrentHop = function() {
    var h = false;
    var f = this.getCurrentFeature();
    if(f) {
        var scid = f.attributes.supplychain_instance_id;
        if(f.attributes.hop_instance_id) {
            var hid = f.attributes.hop_instance_id;
            h = this.map.findSupplychain(scid).findHop(hid);
        }
    }
    return h;
}
