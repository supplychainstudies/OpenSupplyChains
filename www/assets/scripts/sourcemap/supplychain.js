/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

Sourcemap.Supplychain = function() {
    this.remote_id = null;
    this.instance_id = Sourcemap.instance_id("supplychain");
    this.stops = [];
    this.hops = [];
    this.attributes = {};
    this.usergroup_perms = 0;
    this.other_perms = 0;
    this.editable = false;
    this.broadcast('supplychain:instantiated', this);
}

Sourcemap.Supplychain.TEASER_LEN = 80;

Sourcemap.Supplychain.prototype.getAttr = function(k, d) {
    if(arguments.length == 1) {
        return this.attributes[k];
    } else if(arguments.length > 2) {
        for(var i=0, args=[]; i<arguments.length; args.push(arguments[i++]));
        var d = args.pop();
        for(var i=0; i<args.length; i++) {
            var k = args[i];
            if(this.attributes[k] !== undefined) return this.attributes[k];
        }
        return d;
    }
    if(this.attributes[k] !== undefined) return this.attributes[k];
    else return d;
}

Sourcemap.Supplychain.prototype.getLabel = function() {
    return this.getAttr("name", "label", "title", "A Sourcemap");
}

Sourcemap.Supplychain.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Supplychain.prototype.stopIds = function() {
    var ids = [];
    for(var i=0; i<this.stops.length; i++) ids.push(this.stops[i].instance_id);
    return ids;
}

Sourcemap.Supplychain.prototype.localStopIds = function() {
    var lids = [];
    for(var i=0; i<this.stops.length; i++) lids.push(this.stops[i].local_stop_id);
    return lids;
}

Sourcemap.Supplychain.prototype.findStop = function(target_id) {
    var found = false;
    for(var i=0; i<this.stops.length; i++) {
        if(this.stops[i].instance_id === target_id) {
            found = this.stops[i];
            break;
        }
    }
    return found;
}

Sourcemap.Supplychain.prototype.addStop = function(stop) {
    if(stop instanceof Sourcemap.Stop) {
        if(this.stopIds().indexOf(stop.instance_id) >= 0) {
            throw new Error("Stop already exists in this supplychain.");
        }
        stop.supplychain_id = this.instance_id;
        lsids = this.localStopIds();
        var last_id = lsids.length ? Math.max.apply(window, this.localStopIds()) : 0;
        stop.local_stop_id = last_id + 1;
        this.stops.push(stop);
        this.broadcast('supplychain:stop_added', this, stop);
    } else throw new Error("Sourcemap.Stop expected.");
    return this;
}

Sourcemap.Supplychain.prototype.removeStop = function(target_id) {
    if(target_id instanceof Sourcemap.Stop)
        target_id = target_id.instance_id;
    var removed = false;
    var hrmd = [];
    for(var i=0; i<this.hops.length; i++) {
        var h = this.hops[i];
        if(h.from_stop_id === target_id || h.to_stop_id === target_id) {
            //this.removeHop(h);
            hrmd.push(i);
            h.supplychain_id = null;
            h.from_stop_id = null;
            h.to_stop_id = null;
        }
    }
    for(var i=hrmd.length; i>0; i--) this.hops.splice(hrmd[i-1],1);
    for(var i=0; i<this.stops.length; i++) {
        if(this.stops[i].instance_id === target_id) {
            removed = this.stops.splice(i,1)[0];
            removed.supplychain_id = null;
            break;
        }
    }
    this.broadcast('supplychain:stop_removed', this, removed);
    return removed;
}

Sourcemap.Supplychain.prototype.stopHops = function(stop_id) {
    if(stop_id instanceof Sourcemap.Stop)
        stop_id = stop_id.instance_id;
    var stop_hops = {
        'in': [], 'out': []
    };
    for(var i=0; i<this.hops.length; i++) {
        var hop = this.hops[i];
        if(hop.from_stop_id === stop_id) {
            stop_hops.out.push(hop.instance_id);
        } else if(hop.to_stop_id === stop_id) {
            stop_hops["in"].push(hop.instance_id);
        }
    }
    return stop_hops;
}

Sourcemap.Supplychain.prototype.cycleCheck = function() {
    var vector = [];
    var stack = [];
    for(var i=0; i<this.hops.length; i++) {
        if(this.hops[i].from_stop_id === this.hops[i].to_stop_id) {
            throw new Error("Hop '"+this.hops[i].instance_id+"' is circular.");
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
                        out_hop.to_stop_id+"' in '"+this.instance_id+"'.");
                }
                st.push({"n": out_hop.to_stop_id, "v": new_v});
            }
        }
    }
    return true;
}

Sourcemap.Supplychain.prototype.hopIds = function() {
    var ids = [];
    for(var i=0; i<this.hops.length; i++) ids.push(this.hops[i].instance_id);
}

Sourcemap.Supplychain.prototype.findHop = function(target_id) {
    var found = false;
    for(var i=0; i<this.hops.length; i++) {
        if(this.hops[i].instance_id === target_id) {
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
            exists = hop.instance_id;
            break;
        }
    }
    return exists;
}

Sourcemap.Supplychain.prototype.addHop = function(hop) {
    if(hop instanceof Sourcemap.Hop) {
        if(this.hopExists(hop.from_stop_id, hop.to_stop_id) || this.hopExists(hop.to_stop_id, hop.from_stop_id)) {
            throw new Error("Hop exists.");
        }
        this.hops.push(hop);
        hop.supplychain_id = this.instance_id;
        hop.from_local_stop_id = this.findStop(hop.from_stop_id).local_stop_id;
        hop.to_local_stop_id = this.findStop(hop.to_stop_id).local_stop_id;
        this.broadcast('supplychain:hop_added', this, hop);
    } else throw new Error("Sourcemap.Hop expected.");
    return this;
}

Sourcemap.Supplychain.prototype.removeHop = function(hop_id) {
    if(hop_id instanceof Sourcemap.Hop)
        hop_id = hop_id.instance_id;
    var removed = false;
    for(var i=0; i<this.hops.length; i++) {
        if(hop_id === this.hops[i].instance_id) {
            removed = this.hops.splice(i,1)[0];
            removed.supplychain_id = null;
        }
    }
    this.broadcast('supplychain:hop_removed', this, removed);
    return removed;
}

Sourcemap.Supplychain.prototype.stopAttrRange = function(attr_nm) {
    var min = null;
    var max = null;
    var total = 0;
    for(var i=0; i<this.stops.length; i++) {
        var stop = this.stops[i];
        var val = null;
        if(attr_nm instanceof Function) {
            val = attr_nm(stop);
        } else if(stop.attributes[attr_nm] === undefined) {
            continue;
        } else {
            val = parseFloat(stop.attributes[attr_nm]);
        }
        if(isNaN(val))
            continue;
        if(min === null) min = val;
        if(max === null) max = val;
        min = Math.min(val, min);
        max = Math.max(val, max);
        total += val;
    }
    return {
        "min": min, "max": max, "total": total
    };
}

Sourcemap.Supplychain.prototype.hopAttrRange = function(attr_nm) {
    var min = null;
    var max = null;
    var total = 0;
    for(var i=0; i<this.hops.length; i++) {
        var hop = this.hops[i];
        var val = null;
        if(attr_nm instanceof Function) {
            val = attr_nm(hop);
        } else if(hop.attributes[attr_nm] === undefined) {
            continue;
        } else {
            val = parseFloat(hop.attributes[attr_nm]);
        }
        if(isNaN(val))
            continue;
        if(min === null) min = val;
        if(max === null) max = val;
        min = Math.min(val, min);
        max = Math.max(val, max);
        total += val;
    }
    return {
        "min": min, "max": max, "total": total
    };
}

Sourcemap.Supplychain.prototype.attrRange = function(attr_nm) {
    var sr = this.stopAttrRange(attr_nm);
    var hr = this.hopAttrRange(attr_nm);
    var min = Math.min(sr.min, hr.min);
    var max = Math.max(sr.max, hr.max);
    var tot = sr.total + hr.total;
    return {"stops": sr, "hops": hr, "min": min, "max": max, "total": tot};
}

Sourcemap.Stop = function(geometry, attributes) {
    this.instance_id = Sourcemap.instance_id("stop");
    this.supplychain_id = null;
    this.local_id = null; // local id unique to supplychain
    this.geometry = geometry;
    this.attributes = attributes ? Sourcemap.deep_clone(attributes) : {};
}

Sourcemap.Stop.prototype.getAttr = function(k, d) {
    if(arguments.length == 1) {
        return this.attributes[k];
    } else if(arguments.length > 2) {
        for(var i=0, args=[]; i<arguments.length; args.push(arguments[i++]));
        var d = args.pop();
        for(var i=0; i<args.length; i++) {
            var k = args[i];
            if(this.attributes[k] !== undefined) return this.attributes[k];
        }
        return d;
    }
    if(this.attributes[k] !== undefined) return this.attributes[k];
    else return d;
}

Sourcemap.Stop.prototype.setAttr = function(k, v) {
    if(((typeof k) === "object") && !v) {
        for(var ok in k) {
            this.setAttr(ok, k[ok]);
        }
    } else if(((typeof v) === "undefined") && ((typeof this.attributes[k]) !== "undefined")) {
        delete this.attributes[v];
    } else this.attributes[k] = v;
    return this;
}

Sourcemap.Stop.prototype.getLabel = function() {
    var label = false;
    var search_keys = ["title", "name", "label"
    ];
    for(var ki=0; ki<search_keys.length; ki++) {
        var k = search_keys[ki];
        if(this.getAttr(k, false)) {
            label = this.getAttr(k, false);
        }   
    }
    return label;
}

Sourcemap.Stop.prototype.makeHopTo = function(to_stop) {
    var fromll = Sourcemap.Stop.toLonLat(this);
    var toll = Sourcemap.Stop.toLonLat(to_stop);
    var rt = Sourcemap.great_circle_route(fromll, toll);
    var pts = [];
    for(var i=0; i<rt.length; i++) {
        var pt = rt[i];
        pt = new OpenLayers.Geometry.Point(pt.lon, pt.lat);
        pts.push(pt);
    }
    var new_geom = new OpenLayers.Geometry.MultiLineString(
        new OpenLayers.Geometry.LineString(pts)
    );
    new_geom = new_geom.transform(
        new OpenLayers.Projection('EPSG:4326'),
        new OpenLayers.Projection('EPSG:900913')
    );
    new_geom = new OpenLayers.Feature.Vector(new_geom);
    new_geom = (new OpenLayers.Format.WKT()).write(new_geom);

    var new_hop = new Sourcemap.Hop(new_geom, this.instance_id, to_stop.instance_id);
    return new_hop;
}

Sourcemap.Stop.fromLonLat = function(ll, proj) {
    var proj = proj || 'EPSG:4326';
    var geom = new OpenLayers.Geometry.Point(ll.lon, ll.lat);
    geom = geom.transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:900913')
    );
    var wkt = new OpenLayers.Format.WKT();
    var stop = new Sourcemap.Stop(wkt.write(new OpenLayers.Feature.Vector(geom)));
    return stop;
}

Sourcemap.Stop.toLonLat = function(st, proj) {
    var proj = proj || 'EPSG:900913';
    var geom = (new OpenLayers.Format.WKT()).read(st.geometry).geometry;
    geom = geom.transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:4326')
    );
    return {"lon": geom.x, "lat": geom.y};
}

Sourcemap.Stop.geocode = function(st, cb) {
    var cb = cb || $.proxy(function(data) {
        if(data && data.results) {
            this.setAttr("address", data.results[0].placename);
           // this.map.broadcast('map:feature_selected', this.map, this.map.stopFeature(sc.instance_id, new_stop.instance_id));
        }
    }, st);
    var url = 'services/geocode';
    var ll = false;
    var pl = false;
    if(st instanceof Sourcemap.Stop) {
        ll = Sourcemap.Stop.toLonLat(st);
    } else if(st.lon != undefined && st.lat != undefined) {
        ll = st;
    } else if(typeof st == "string") {
        ll = false;
        pl = st;
    }
    $.ajax({"url": url, "type": "GET", "data": ll ? {"ll": ll.lat+','+ll.lon} : {"placename": pl}, 
        "success": cb, "failure": cb, "dataType": "json"
    });
}

Sourcemap.Hop = function(geometry, from_stop_id, to_stop_id, attributes) {
    this.instance_id = Sourcemap.instance_id("hop");
    this.supplychain_id = null;
    this.from_stop_id = from_stop_id;
    this.to_stop_id = to_stop_id;
    this.geometry = geometry;
    this.attributes = attributes ? Sourcemap.deep_clone(attributes) : {};
   
    if (!this.attributes.distance) {
        this.attributes.distance = this.gc_distance();
    }
}

Sourcemap.Hop.prototype.toJSON = function() {
    var j = Sourcemap.deep_clone(this);
    j.from_stop_id = this.from_local_stop_id;
    j.to_stop_id = this.to_local_stop_id;
    return j;
}

Sourcemap.Hop.prototype.getAttr = function(k, d) {
    if(this.attributes[k] !== undefined) return this.attributes[k];
    else return d;
}

Sourcemap.Hop.prototype.setAttr = function(k, v) {
    if(((typeof k) === "object") && !v) {
        for(var ok in k) {
            this.setAttr(ok, k[ok]);
        }
    } else if(((typeof v) === "undefined") && ((typeof this.attributes[k]) !== "undefined")) {
        delete this.attributes[v];
    } else this.attributes[k] = v;
    return this;
}

Sourcemap.Hop.prototype.getLabel = function() {
    var label = false;
    var search_keys = ["title", "name", "label"
    ];
    for(var ki=0; ki<search_keys.length; ki++) {
        var k = search_keys[ki];
        if(this.getAttr(k, false)) {
            label = this.getAttr(k, false);
        }   
    }
    return label;
}

Sourcemap.Hop.prototype.gc_distance = function() {
    
    var proj = proj || 'EPSG:900913';
    if(!this.geometry) return 0.0;
    var geom = (new OpenLayers.Format.WKT()).read(this.geometry).geometry;
    var from_geom = geom.components[0].components[0].clone().transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:4326')
    );
    var to_geom = geom.components[0].components[1].clone().transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:4326')
    );
    var gc_distance = Sourcemap.haversine(from_geom, to_geom);
    return gc_distance;

}
