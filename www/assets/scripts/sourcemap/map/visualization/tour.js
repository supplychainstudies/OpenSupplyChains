Sourcemap.MapTour = function(map, o) {
    if(!(map instanceof Sourcemap.Map)) {
        throw new Error('MapTour requires a map instance.');
    }
    this.map = map;
    this.timeout = null;
    this.stop_index = 0;
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.MapTour.prototype.defaults = {
    "auto_init": true, "interval": 1000,
    "start_inactive": 5 // seconds
};

Sourcemap.MapTour.prototype.init = function() {
    this.interval = this.options.interval > 0 ? this.options.interval : 1000;
    this.start_inactive = this.options.start_inactive > 0 ? this.options.start_inactive : 0;
    if(this.start_inactive) {
        this.stop();
        console.log('waiting...'+this.start_inactive);
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
    var next_index = this.stop_index >= this.map.layers.stops.features.length ?
        0 : this.stop_index + 1;
    var current_stop = this.map.layers.stops.features[this.stop_index] || null;
    var next_stop = this.map.layers.stops.features[next_index] || null;
    if(current_stop) this.map.controls.select.unselect(current_stop);
    if(next_stop) {
        this.map.map.panTo((new OpenLayers.LonLat(next_stop.geometry.x, next_stop.geometry.y)));
        this.map.controls.select.select(next_stop);
    }
    if(next_stop) this.stop_index++;
    else this.stop_index = 0;
    if(this.timeout) clearTimeout(this.timeout);
    this.timeout = setTimeout($.proxy(this.next, this), this.interval);
}
