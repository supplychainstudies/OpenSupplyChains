Sourcemap.MapTour = function(map, o) {
    if(!(map instanceof Sourcemap.Map)) {
        throw new Error('MapTour requires a map instance.');
    }
    this.map = map;
    this.features = o.features || [];
    this.ftr_index = -1;
    this.timeout = null;
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.MapTour.prototype.defaults = {
    "auto_init": true, "interval": 2500,
    "start_inactive": 5, // seconds
    "tour_hops": false, "tour_stops": true
};

Sourcemap.MapTour.prototype.init = function() {
    this.interval = this.options.interval > 0 ? this.options.interval : 1000;
    this.start_inactive = this.options.start_inactive > 0 ? this.options.start_inactive : 0;
    if(this.start_inactive) {
        this.stop();
        this.timeout = setTimeout($.proxy(this.start, this), this.start_inactive*1000);
    }
    Sourcemap.broadcast('map_tour:init', this);
}

Sourcemap.MapTour.prototype.start = function() {
    Sourcemap.broadcast('map_tour:start', this);
    this.next();
}

Sourcemap.MapTour.prototype.stop = function() {
    if(this.timeout) clearTimeout(this.timeout);
}

Sourcemap.MapTour.prototype.next = function() {
    var next_index = this.ftr_index >= this.features.length ?
        0 : this.ftr_index+1;
    var current_ftr = this.features[this.ftr_index] || null;
    var next_ftr = this.features[next_index] || null;
    if(current_ftr && this.map.controls && this.map.controls.select) 
        this.map.controls.select.unselect(current_ftr);
    if(next_ftr) {
        this.map.map.panTo(this.getFeatureLonLat(next_ftr));
        if(this.map.controls && this.map.controls.select)
            this.map.controls.select.select(next_ftr);
    }
    this.ftr_index = next_index;
    if(this.timeout) clearTimeout(this.timeout);
    this.timeout = setTimeout($.proxy(this.next, this), this.interval);
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
