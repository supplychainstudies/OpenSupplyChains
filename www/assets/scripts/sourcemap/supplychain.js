Sourcemap.Supplychain = function(remote_id) {
    this.remote_id = remote_id;
    this.local_id = Sourcemap.local_id("supplychain");
    this.stops = [];
    this.hops = [];
    this.broadcast('supplychainInstantiated', this);
}

Sourcemap.Supplychain.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Supplychain.prototype.stopIds = function() {
    var ids = [];
    for(var i=0; i<this.stops.length; i++) ids.push(this.stops[i].local_id);
    return ids;
}

Sourcemap.Supplychain.prototype.findStop = function(target_id) {
    var found = false;
    for(var i=0; i<this.stops.length; i++) {
        if(this.stops[i].local_id === target_id) {
            found = this.stops[i];
            break;
        }
    }
    return found;
}

Sourcemap.Supplychain.prototype.addStop = function(stop) {
    if(stop instanceof Sourcemap.Stop) {
        if(this.stopIds().indexOf(stop.local_id) >= 0) {
            throw new Error("Stop already exists in this supplychain.");
        }
        this.stops.push(stop);
        this.broadcast('supplychainStopAdded', this, stop);
    } else throw new Error("Sourcemap.Stop expected.");
    return this;
}

Sourcemap.Supplychain.prototype.removeStop = function(target_id) {
    var removed = false;
    for(var i=0; i<this.stops.length; i++) {
        if(this.stops[i].local_id === target_id) {
            removed = this.stops[i];
            delete this.stops[i];
            break;
        }
    }
    this.broadcast('supplychainStopRemoved', this, removed);
    return removed;
}

Sourcemap.Supplychain.prototype.stopHops = function(stop_id) {
    var stop_hops = {
        'in': [], 'out': []
    };
    for(var i=0; i<this.hops.length; i++) {
        var hop = this.hops[i];
        if(hop.from_stop_id === stop_id) {
            stop_hops.out.push(hop.local_id);
        } else if(hop.to_stop_id === stop_id) {
            stop_hops["in"].push(hop.local_id);
        }
    }
    return stop_hops;
}

Sourcemap.Supplychain.prototype.cycleCheck = function() {
    var vector = [];
    var stack = [];
    for(var i=0; i<this.hops.length; i++) {
        if(this.hops[i].from_stop_id === this.hops[i].to_stop_id) {
            throw new Error("Hop '"+this.hops[i].local_id+"' is circular.");
        }
        var n = this.hops[i].from_stop_id;
        var v = [];
        var st = [{"n": n, "v": v}];
        while(st.length) {
            var sti = st.pop();
            var n = sti.n;
            var v = sti.v;
            var new_v = Sourcemap.deep_clone(v);
            new_v.push(n);
            var outgoing = this.stopHops(n).out;
            for(var oi=0; oi<outgoing.length; oi++) {
                var out_hop = this.findHop(outgoing[oi]);
                if(new_v.indexOf(out_hop.to_stop_id) >= 0) {
                    throw new Error("Found cycle at hop from '"+n+"' to '"+
                        out_hop.to_stop_id+"' in '"+this.local_id+"'.");
                }
                st.push({"n": out_hop.to_stop_id, "v": new_v});
            }
        }
    }
    return true;
}

Sourcemap.Supplychain.prototype.hopIds = function() {
    var ids = [];
    for(var i=0; i<this.hops.length; i++) ids.push(this.hops[i].local_id);
}

Sourcemap.Supplychain.prototype.findHop = function(target_id) {
    var found = false;
    for(var i=0; i<this.hops.length; i++) {
        if(this.hops[i].local_id === target_id) {
            found = this.hops[i];
            break;
        }
    }
    return found;
}

Sourcemap.Supplychain.prototype.hopExists = function(from_stop_id, to_stop_id) {
    var exists = false;
    for(var i=0; i<this.hops.length; i++) {
        var hop = this.hops[i];
        if(hop.from_stop_id === from_stop_id &&
            hop.to_stop_id === to_stop_id) {
            exists = hop.local_id;
            break;
        }
    }
    return exists;
}

Sourcemap.Supplychain.prototype.addHop = function(hop) {
    if(hop instanceof Sourcemap.Hop) {
        if(this.hopExists(hop.from_stop_id, hop.to_stop_id)) {
            throw new Error("Hop exists.");
        }
        this.hops.push(hop);
        this.broadcast('supplychainHopAdded', this, hop);
    } else throw new Error("Sourcemap.Hop expected.");
    return this;
}

Sourcemap.Supplychain.prototype.removeHop = function(hop_id) {
    var removed = false;
    for(var i=0; i<this.hops.length; i++) {
        if(hop_id === this.hops[i].local_id) {
            removed = this.hops[i];
            delete this.hops[i];
        }
    }
    this.broadcast('supplychainHopRemoved', this, removed);
    return removed;
}

Sourcemap.Stop = function(geometry, attributes) {
    this.local_id = Sourcemap.local_id("stop");
    this.geometry = geometry;
    this.attributes = attributes ? Sourcemap.deep_clone(attributes) : {};
}

Sourcemap.Hop = function(geometry, from_stop_id, to_stop_id, attributes) {
    this.local_id = Sourcemap.local_id("hop");
    this.from_stop_id = from_stop_id;
    this.to_stop_id = to_stop_id;
    this.geometry = geometry;
    this.attributes = attributes ? Sourcemap.deep_clone(attributes) : {};
}
