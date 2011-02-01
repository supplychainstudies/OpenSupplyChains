Sourcemap = {};

Sourcemap.ERROR = 1;
Sourcemap.WARNING = 2;
Sourcemap.INFO = 4;

Sourcemap.options = {
    "log_level": Sourcemap.ERROR | Sourcemap.WARNING //| Sourcemap.INFO
};

Sourcemap.log = function(message, level) {
    var level = typeof level === "undefined" ? Sourcemap.INFO : level;
    if(level & Sourcemap.options.log_level) {
        if(typeof console !== 'undefined' && console && console.log) console.log(message);
    }
    return true;
}

Sourcemap.log('Welcome to Sourcemap.', Sourcemap.INFO);

Sourcemap.deep_clone = function(o) {
    if(typeof o === "object") {
        if(o instanceof Array) {
            var r = [];
            for(var i=0; i<o.length; i++) r[i] = Sourcemap.deep_clone(o[i]);
        } else if(o instanceof Date) {
            var r = new o.constructor(o.getTime());
        } else if(o instanceof RegExp) {
            var r = new o.constructor(o.toString());
        } else if(o instanceof HTMLElement) {
            var r = o.cloneNode();
        } else if(o) {
            var r = o.constructor ? new o.constructor() : {};
            for(var k in o) {
                r[k] = Sourcemap.deep_clone(o[k]);
            }
        } else {
            r = o;
        }
    } else {
        var r = o;
    }
    return r;
}

Sourcemap.hash = function(str) {
    var h = 5381;
    for(var i=0; i<str.length; i++) {
        h = ((h << 5) + h) + str.charCodeAt(i);
    }
    return (h & 0x7FFFFFFF);
}

Sourcemap._local_seq = {};
Sourcemap.local_id = function(seq) {
    var seq = typeof seq === "string" ? seq : new String(seq);
    if(typeof Sourcemap._local_seq[seq] === "undefined") {
        Sourcemap._local_seq[seq] = 0;
    }
    var seq_val = ++Sourcemap._local_seq[seq];
    var id = [seq, seq_val].join("-");
    return id;
}

Sourcemap.Configurable = function(o) {
    var o = typeof o === "undefined" ? {} : o;
    var defaults = this.defaults ? Sourcemap.deep_clone(this.defaults) : {};
    this.options = {};
    for(var k in defaults) {
        if(typeof o[k] !== "undefined") {
            this.options[k] = o[k];
        } else {
            this.options[k] = defaults[k];
        }
    }
    for(var k in o) {
        if(typeof this.options[k] === "undefined") {
            this.options[k] = o[k];
        }
    }
    if(typeof this.init === "function" && this.options.auto_init) {
        this.init();
    }
}

Sourcemap.Configurable.prototype.defaults = {"auto_init": false};

Sourcemap.broadcast = function(evt) {
    var a = []; for(var i=0; i<arguments.length; i++) a.push(arguments[i]);
    var args = a.slice(1);
    $(document).trigger(evt, args);
    Sourcemap.log('Broadcast: '+evt);
}

Sourcemap.factory = function(type, data) {
    var instance;
    switch(type) {
        case 'supplychain':
            try {
                Sourcemap.validate(type, data);
            } catch(e) {
                Sourcemap.log(e, Sourcemap.ERROR);
            }
            instance = new Sourcemap.Supplychain();
            var sc = data;
            var stop_ids = {};
            sc.attributes = Sourcemap.deep_clone(sc.attributes);
            for(var i=0; i<sc.stops.length; i++) {
                var new_stop = new Sourcemap.Stop(
                    sc.stops[i].geometry, sc.stops[i].attributes
                );
                stop_ids[sc.stops[i].id] = new_stop.local_id;
                instance.addStop(new_stop);
            }
            for(var i=0; i<sc.hops.length; i++) {
                var local_from = stop_ids[sc.hops[i].from_stop_id];
                var local_to = stop_ids[sc.hops[i].to_stop_id];
                var new_hop = new Sourcemap.Hop(
                    sc.hops[i].geometry, local_from, local_to,
                    sc.hops[i].attributes
                );
                instance.addHop(new_hop);
            }
            break;
        default:
            instance = false;
            break;
    }
    return instance;
}

Sourcemap.validate = function(type, data) {
    switch(type) {
        case 'supplychain':
            var sc = data;
            if(!(sc.stops instanceof Array))
                throw new Error('Stops array missing or invalid.');
            if(!(sc.hops instanceof Array))
                throw new Error('Hops array missing or invalid.');
            var stop_ids = [];
            for(var i=0; i<sc.stops.length; i++) {
                Sourcemap.validate('stop', sc.stops[i]);
                stop_ids.push(sc.stops[i].id);
            }
            for(var i=0; i<sc.hops.length; i++) {
                Sourcemap.validate('hop', sc.hops[i]);
                if(stop_ids.indexOf(sc.hops[i].from_stop_id) < 0)
                    throw new Error('From stop in hop is invalid.');
                if(stop_ids.indexOf(sc.hops[i].to_stop_id) < 0)
                    throw new Error('To stop in hop is invalid.');
            }
            if(!(sc.attributes instanceof Object)) {
                throw new Error('Missing or invalid attributes property.');
            }
            break;
        case 'stop':
            var stop = data;
            if(!(stop.attributes instanceof Object))
                throw new Error('Stop missing attributes object.');
            if(!stop.geometry)
                throw new Error('Stop missing geometry.');
            var parser = new OpenLayers.Format.WKT();
            var parsed = parser.read(stop.geometry)
            if(!parsed || !(parsed instanceof OpenLayers.Feature.Vector)) {
                throw new Error('Invalid geometry.');
            }
            break;
        case 'hop':
            var hop = data;
            if(!(hop.attributes instanceof Object))
                throw new Error('Hop missing attributes object.');
            if(!hop.geometry)
                throw new Error('Hop missing geometry.');
            var parser = new OpenLayers.Format.WKT();
            var parsed = parser.read(hop.geometry)
            if(!parsed || !(parsed instanceof OpenLayers.Feature.Vector)) {
                throw new Error('Invalid geometry.');
            }
            break;
        default:
            throw new Error('validation not implemented: '+type);
            break;
    }
    return false;
}


Sourcemap.loadSupplychain = function(remote_id, callback) {
    // fetch and initialize supplychain
    var _that = this;
    var _remote_id = remote_id;
    $.get('services/supplychains/'+remote_id, {},  function(data) {
            callback.apply(this, arguments);
            // notice this event fires _after_ the callback runs.
            _that.broadcast('supplychain:loaded', this, data);
        }
    );
}

Sourcemap.saveSupplychain = function(supplychain_id) {
    // save supplychain
    // this.broadcast('supplychainSaved', this, supplychain); asynch!
}
