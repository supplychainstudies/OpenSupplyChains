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

if (!('indexOf' in Array.prototype)) {
    Array.prototype.indexOf= function(find, i /*opt*/) {
        if (i===undefined) i= 0;
        if (i<0) i+= this.length;
        if (i<0) i= 0;
        for (var n= this.length; i<n; i++)
            if (i in this && this[i]===find)
                return i;
        return -1;
    };
}

Sourcemap = {};
_S = Sourcemap;

Sourcemap.ERROR = 1;
Sourcemap.WARNING = 2;
Sourcemap.INFO = 4;

Sourcemap.READ = 1;
Sourcemap.WRITE = 2;
Sourcemap.DELETE = 8;

Sourcemap.options = {
    "log_level": Sourcemap.ERROR | Sourcemap.WARNING// | Sourcemap.INFO
};

Sourcemap.log = function(message, level) {
    var level = typeof level === "undefined" ? Sourcemap.INFO : level;
    if(level & Sourcemap.options.log_level) {
        if(typeof console !== 'undefined' && console && console.log) console.log(message);
    }
    return true;
}

Sourcemap.log('Welcome to Open Supply Chains.', Sourcemap.INFO);

Sourcemap.deep_clone = function(o) {
    if(typeof o === "object") {
        if(o instanceof Function) {
            var r = function() { return o.apply(this, arguments); };
        } else if(o instanceof Array) {
            var r = [];
            for(var i=0; i<o.length; i++) r[i] = Sourcemap.deep_clone(o[i]);
        } else if(o instanceof Date) {
            var r = new o.constructor(o.getTime());
        } else if(o instanceof RegExp) {
            var r = new o.constructor(o.toString());
        } /*else if(o instanceof HTMLElement) {
            var r = o.cloneNode();
        }*/ else if(o) {
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
Sourcemap.instance_id = function(seq) {
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

Sourcemap.listen = function(evts, callback, scope) {
    if(evts instanceof Array)
        evts = evts.join(" ");
    if(callback instanceof Function) {
        if(scope) {
            $(document).bind(evts, $.proxy(callback, scope));
        } else {
            $(document).bind(evts, callback);
        }
    }
    return true;
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
                stop_ids[sc.stops[i].local_stop_id] = new_stop.instance_id;
                new_stop.local_stop_id = sc.stops[i].local_stop_id;
                instance.addStop(new_stop);
            }
            for(var i=0; i<sc.hops.length; i++) {
                var from_instance = stop_ids[sc.hops[i].from_stop_id];
                var to_instance = stop_ids[sc.hops[i].to_stop_id];
                var new_hop = new Sourcemap.Hop(
                    sc.hops[i].geometry, from_instance, to_instance,
                    sc.hops[i].attributes
                );
                instance.addHop(new_hop);
            }
            instance.owner = data.owner;
            instance.remote_id = sc.id;
            instance.created = sc.created;
            instance.attributes = sc.attributes;
            instance.usergroup_perms = sc.usergroup_perms;
            instance.other_perms = sc.other_perms;
            instance.editable = data.editable;
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
            var sc = Sourcemap.factory('supplychain', data.supplychain);
            sc.editable = data.editable;
            callback.apply(this, [sc]);
            // notice this event fires _after_ the callback runs.
            _that.broadcast('supplychain:loaded', this, sc);
        }
    );
}

Sourcemap.saveSupplychain = function(supplychain, o) {
	window.onbeforeunload = function() {
	  window.onbeforeunload = null;	
	  return "Your map is being saved, are you sure you want to navigate away?";
	};
    var o = o || {};
    var scid = o.supplychain_id ? o.supplychain_id : null;
    var succ = o.success ? o.success : null;
    var fail = o.failure ? o.failure : null;
    var scid = scid || null;
    var payload = null;
    if(typeof supplychain === "string") payload = supplychain;
    else payload = JSON.stringify({"supplychain": supplychain});
    $.ajax({
        "url": 'services/supplychains/'+(scid ? scid : ''),
        "type": scid ? 'PUT' : 'POST', // put to update, post to create
        "contentType": 'application/json', "data": payload,
        "dataType": "json", "success": $.proxy(function(data) {
			window.onbeforeunload = null;
            var new_uri = null; // indicates 'created'
            if(data && data.created) {
                new_uri = data.created;
                var scid = data.created.split('/').pop();
            } else if(data && data.success) {
                var scid = this.supplychain_id;
            }
            if(this.success && ((typeof this.success) === "function")) {
                this.success(this.supplychain, scid, new_uri);
            }
            Sourcemap.broadcast("supplychainSaveSuccess", this.supplychain, scid, new_uri);
        }, {
            "supplychain_id": scid, "supplychain": supplychain, 
            "success": succ, "failure": fail
        }),
        "failure": $.proxy(function(data) {
            if(this.failure && ((typeof this.failure) === "function")) {
                this.failure(this.supplychain, this.supplychain_id);
            }
            Sourcemap.broadcast("supplychainSaveFailure", this.supplychain, this.supplychain_id);
        }, {
            "supplychain_id": scid, "supplychain": supplychain,
            "success": succ, "failure": fail
        })
    });
    return;
}

Sourcemap.humanDate = function(then, now) {
    var now = Math.floor((now ? now.getTime() : (new Date()).getTime())/1000);
    var then = Math.floor(then.getTime()/1000);
    var str = '';
    var dow = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    var moy = [
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"
    ];
    if(then > now) str = 'in the future';
    else if(now - then < 60) {
        str = 'just now';
    } else if(now - then < 60 * 60) {
        str = 'less than an hour ago';
    } else  if(now - then < 60 * 60 * 3) {
        str = 'a couple of hours ago';
    } else if(now - then < 60 * 60 * 6) {
        str = 'a few hours ago';
    } else if(now - then < 60 * 60 * 24) {
        str = 'today';
    } else if(now - then < 60 * 60 * 24 * 7) {
        str = 'on '+dow[(new Date(then*1000)).getDay()];
    } else if(now - then < 60 * 60 * 24 * 14) {
        str = 'last '+dow[(new Date(then*1000)).getDay()];
    } else if(now - then < 60 * 60 * 24 * 30) {
        str = 'in the last few weeks';
    } else if(now - then < 60 * 60 * 24 * 30 * 12) {
        str = 'in '+moy[(new Date(then*1000)).getMonth()];
    } else if(now - then < 60 * 60 * 24 * 30 * 16) {
        str = 'about a year ago';
    } else {
        str = 'a long time ago';
    }
    return str;
}

Sourcemap.htesc = function(str) {
    var esc = str;
    try {
        esc = $('<div/>').text(str).html();
    } catch(e) {
        // pass
        esc = str;
    }
    if(esc.length === 0) esc = str;
    return esc;
}

Sourcemap.ttrunc = function(str, lim, dots) {
    var suffix = '...';
    var tstr = str.substr(0, lim);
    if(str.length > tstr.length) {
        tstr = tstr.substr(0, tstr.length - 3) + '...';
    }
    return tstr;
}

Sourcemap.tlinkify = function(str) {
    var txt = _S.htesc(str);
    var regex = /((((http|https):\/\/([\w\d]+\.){1,2})|www\.[\w\d-]+\.)([\w\d]{2,3})((\/[\d\w%\.]+)+)?(\?([\d\w%]+=[\d\w%]+&?)+)?)/g;
    regex = new RegExp(regex);
    return txt.replace(regex, '<a href="$1">$1</a>');
}

Sourcemap.markdown = function(mrkdn) {
    if(!Showdown || !Showdown.converter) 
        throw new Exception('Showdown missing.');
    return (new Showdown.converter()).makeHtml(_S.htesc(mrkdn));
}

Sourcemap.okeys = function(o) {
    var keys = [];
    for(var k in o) keys.push(k);
    return keys;
}

Sourcemap.ovals = function(o) {
    var vals = [];
    for(var k in o) vals.push(o[k]);
    return vals;
}

Sourcemap.oksort = function(o, cmp) {
    var cmp = cmp ? cmp : function(a, b) { return a.v > b.v ? 1 : (a.v < b.v ? -1 : 0); };
    var keys = Sourcemap.okeys(o);
    var vals = Sourcemap.ovals(o);
    var olist = [];
    for(var ki=0; ki<keys.length; ki++) olist.push({"k": keys[ki], "v": vals[ki]});
    olist.sort(cmp);
    var sorted = [];
    for(var ki=0; ki<olist.length; ki++) sorted.push(olist[ki].k);
    return sorted;
}

Sourcemap.hexc2rgb = function(hexc) {
    if(hexc.charAt(0) == "#") hexc = hexc.substr(1);
    if(hexc.match(/^[0-9A-Fa-f]{3}$/)) {
        hexc = hexc.replace((new RegExp("([0-9a-fA-F])", "g")), "$1$1");
    }
    var r, g, b;
    if(hexc.match(/^[0-9a-fA-F]{6}$/)) {
        hexc = parseInt(hexc, 16);
        r = (hexc & 0xff0000) >> (8*2);
        g = (hexc & 0x00ff00) >> 8;
        b = (hexc & 0x0000ff)
    } else throw new Error('Invalid hex color.');
    return [r,g,b];
}

Sourcemap.rgb2hexc = function(rgb) {
    if(!rgb.length || rgb.length < 3)
        throw new Error('Invalid rgb.');
    var r = Math.min(256, Math.max(0, rgb[0]));
    var g = Math.min(256, Math.max(0, rgb[1]));
    var b = Math.min(256, Math.max(0, rgb[2]));
    var hexc = 0;
    hexc = (new Number(r)) << (8*2);
    hexc |= (new Number(g)) << 8;
    hexc |= (new Number(b));
    hexc = hexc.toString(16);
    while(hexc.length < 6) hexc = "0"+hexc;
    return "#"+hexc;
}

Sourcemap.Color = function(r, g, b) {
    this.r = r || 0;
    this.g = g || 0;
    this.b = b || 0;
}

Sourcemap.Color.prototype.fromHex = function(hexc) {
    var rgb = Sourcemap.hexc2rgb(hexc);
    this.r = rgb[0];
    this.g = rgb[1];
    this.b = rgb[2];
    return this;
}

Sourcemap.Color.prototype.toString = function() {
    return Sourcemap.rgb2hexc([this.r, this.g, this.b]);
}

Sourcemap.Color.prototype.clone = function() {
    var rgb = Sourcemap.hexc2rgb(this.toString());
    return new Sourcemap.Color(rgb[0],rgb[1],rgb[2]);
}

Sourcemap.Color.prototype.midpoint = function(to_color) {
    var dr = to_color.r - this.r;
    var mr = this.r + (Math.round(dr/2))
    var dg = to_color.g - this.g;
    var mg = this.g + (Math.round(dg/2))
    var db = to_color.b - this.b;
    var mb = this.b + (Math.round(db/2))
    return new Sourcemap.Color(mr, mg, mb);
}

Sourcemap.Color.graduate = function(colors, ticks) {
    ticks = isNaN(parseInt(ticks)) ? colors.length : parseInt(ticks);
    if(!ticks) return [];
    var g = [];
    var colors = colors.slice(0);
    while(colors.length > ticks) {
        var ri = Math.floor(colors.length / 2);
        colors.splice(ri,1);
    }
    while(colors.length < ticks) {
        var g = [];
        var d = Math.min(ticks - colors.length, colors.length-1);
        for(var i=0; i<colors.length; i++) {
            var c = colors[i];
            g.push(c);
            if(d) {
                g.push(c.midpoint(colors[i+1]));
                d--;
            }
        }
        colors = g.slice(0);
    }
    return colors;
}

Sourcemap.R = 6371 //km

Sourcemap.radians = function(deg) {
    return deg*Math.PI/180;
}

Sourcemap.degrees = function(rad) {
    return rad*180.0/Math.PI;
}

Sourcemap.dms2decdeg = function(d, m, s) {
    dd = Number(d);
    dd = dd + m/60.0;
    dd = dd + s/Math.pow(60.0,2.0);
    return dd;
}

Sourcemap.haversine = function(pt1, pt2) {
    // Calculate great circle distance between points on a spheriod.
    var R = Sourcemap.R;
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    var lat2 = pt2.y;
    var lon2 = pt2.x;

    lat1 = Sourcemap.radians(lat1);
    lon1 = Sourcemap.radians(lon1);
    lat2 = Sourcemap.radians(lat2);
    lon2 = Sourcemap.radians(lon2);
    var dLat = lat2-lat1;
    var dLon = lon2-lon1;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) + 
        Math.cos(lat1) * Math.cos(lat2) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c;
    return d;
}

Sourcemap.great_circle_bearing = function(pt1, pt2) {       //Note longitude is 0 at Greenich - in western hemisphere and positive in eastern
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    var lat2 = pt2.y;
    var lon2 = pt2.x;
    lat1 = Sourcemap.radians(lat1)
    lon1 = Sourcemap.radians(lon1)
    lat2 = Sourcemap.radians(lat2)
    lon2 = Sourcemap.radians(lon2)
    var dLon = lon2 - lon1     //Longitude is east-west distance...note this returns directional value (might be bigger than pi but cos symetric around 0) 
                                       //Note we switch sin and cos for latitude b/c 0 latitude is at equator    
    var y = Math.sin(dLon)*Math.cos(lat2)  //This calculates y position in cartesian coordinates with radius earth=1
    var x = Math.cos(lat1)*Math.sin(lat2) - 
        Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLon)
    var brng = Sourcemap.degrees(Math.atan2(y, x))     //Note bearing is the differential direction of the arc given in degrees relative to east being 0
    return brng  //Using the plane carie (sp?) projection EPSG:4326 (sending long to x and lat to y) brng is also differential direction of arc in projection
}

Sourcemap.great_circle_midpoint = function(pt1, pt2) {
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    var lat2 = pt2.y;
    var lon2 = pt2.x;
    lat1 = Sourcemap.radians(lat1);
    lon1 = Sourcemap.radians(lon1);   
    lat2 = Sourcemap.radians(lat2);
    lon2 = Sourcemap.radians(lon2);
    var dLon = lon2 - lon1;
    var Bx = Math.cos(lat2) * Math.cos(dLon);
    var By = Math.cos(lat2) * Math.sin(dLon);
    var lat3 = Math.atan2(Math.sin(lat1)+Math.sin(lat2),
        Math.sqrt((Math.cos(lat1)+Bx)*(Math.cos(lat1)+Bx) + By*By));
    var lon3 = lon1 + Math.atan2(By, Math.cos(lat1) + Bx);
    return {"y": Sourcemap.degrees(lat3), "x": Sourcemap.degrees(lon3)};
}

Sourcemap.great_circle_endpoint = function(pt1, brng, d) {
    var R = Sourcemap.R;
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    lat1 = Sourcemap.radians(lat1);
    lon1 = Sourcemap.radians(lon1);
    lat2 = Math.asin(Math.sin(lat1)*Math.cos(d/R) + 
            Math.cos(lat1)*Math.sin(d/R)*Math.cos(brng));
    lon2 = lon1 + Math.atan2(Math.sin(brng)*Math.sin(d/R)*Math.cos(lat1), 
            Math.cos(d/R)-Math.sin(lat1)*Math.sin(lat2));
    return {"y": Sourcemap.degrees(lat2), "x": Sourcemap.degrees(lon2)};
}

Sourcemap.great_circle_route = function(pt1, pt2, ttl) {
    var mp = Sourcemap.great_circle_midpoint(pt1, pt2);
    var rt = [pt1];
    if(ttl > 0) {
        var ttl = ttl - 1;
        rt = rt.concat(Sourcemap.great_circle_route(pt1, mp, ttl));
        rt = rt.concat(Sourcemap.great_circle_route(mp, pt2, ttl));
    }
    rt.push(pt2);
    //var rtuniq = []
    // TODO: find and discard duplicates...
    return rt;
}

Sourcemap.Units = {};

Sourcemap.Units.si_prefixes = {
    "y": {"label": "yocto", "mult":-24}, "z": {"label": "zepto", "mult": -21},
    "a": {"label": "atto", "mult": -18}, "f": {"label": "femto", "mult": -15},
    "p": {"label": "pico", "mult": -12}, "n": {"label": "nano", "mult": -9},
    "u": {"label": "micro", "mult": -6}, "m": {"label": "milli", "mult": -3},
    //"c": {"label": "centi", "mult": -2}, "d": {"label": "deci", "mult": -1},
    //"D": {"label": "deca", "mult": 1},  "h": {"label": "hecto", "mult": 2},
    "k": {"label": "kilo", "mult": 3}, "M": {"label": "mega", "mult": 6},
    "G": {"label": "giga", "mult": 9}, "T": {"label": "tera", "mult": 12},
    "P": {"label": "peta", "mult": 15}, "E": {"label": "exa", "mult": 18},
    "Z": {"label": "zetta", "mult": 21}, "Y": {"label": "yotta", "mult": 24}
}

Sourcemap.Units.si_equiv = {
    "g": {"abbrev": "g", "singular": "gram", "plural": "grams"},
    "Mg": {"abbrev": "t", "singular": "tonne", "plural": "tonnes"},
    "Gg": {"abbrev": "kt", "singular": "kilotonne", "plural": "kilotonnes"},
    "Tg": {"abbrev": "Mt", "singular": "megatonne", "plural": "megatonnes"},
    "Pg": {"abbrev": "Gt", "singular": "gigatonne", "plural": "gigatonne"}
}

Sourcemap.Units.to_base_unit = function(value, unit) {
    var value = parseFloat(value);
    var unit = new String(unit);
    var prefix = null;
    var max_prefix_len = 2;
    var prefix_len = max_prefix_len;
    while(prefix === null && prefix_len > 0) {
        if(unit.length > prefix_len) {
            if(Sourcemap.Units.si_prefixes[unit.substr(0, prefix_len)] !== undefined) {
                prefix = unit.substr(0, prefix_len);
                break;
            }
        }
        prefix_len--;
    }
    var from_power_of_ten = prefix ? Sourcemap.Units.si_prefixes[prefix].mult : 0;
    var base_unit = prefix !== null ? unit.substr(prefix_len) : unit+"";
    var base_value = value * Math.pow(10, from_power_of_ten);
    var base = {"unit": base_unit, "value": base_value};
    return base;
}

Sourcemap.Units.scale_unit_value = function(value, unit, precision) {
    if(isNaN(value)) return 0;
    var precision = isNaN(parseInt(precision)) ? 2 : parseInt(precision);
    var base = Sourcemap.Units.to_base_unit(value, unit);
    var pot = base.value === 0 ? 0 : Math.floor((Math.log(base.value)/Math.log(10))+.000000000001);
    var new_unit = null;
    //if(value == 0 || pot === 0) {
    if(pot < 2) {
        //new_unit = {"label": base.unit, "mult": 0};
        if(base.value < 10) base.value = parseFloat(base.value).toFixed(1);
        else base.value = Math.round(base.value);
        return base;
    } else {
        new_unit = false;
        while(new_unit === false) {
            for(var p in Sourcemap.Units.si_prefixes) {
                var u = Sourcemap.Units.si_prefixes[p];
                if(u.mult === pot) {
                    new_unit = p;
                    break;
                }
            }
            if(new_unit !== false) break;
            if(pot <= -24) {
                new_unit = p; 
                break;
            } else if(pot >= 24) {
                new_unit = p;
                break;
            }
            if((pot % 3) === 2) pot++;
            else if(pot % 3) pot--;
            else pot = pot - 3;
        }
        new_unit += base.unit;
        new_unit = {"label": new_unit, "mult": pot};
    }
    var scaled_value = parseFloat(base.value * Math.pow(10, -new_unit.mult)).toFixed(1);
    if(scaled_value < 10) scaled_value = parseFloat(scaled_value).toFixed(1);
    else scaled_value = parseFloat(Math.round(scaled_value));
    var scaled_unit = new_unit;
    var scaled = {"unit": scaled_unit.label, "value": scaled_value};
    if(Sourcemap.Units.si_equiv[scaled.unit] !== undefined) 
        scaled.unit = Sourcemap.Units.si_equiv[scaled.unit].abbrev;
    return scaled;
}

// Date fxns
Sourcemap.Date = {};

Sourcemap.Date.months = [
    "January","February","March","April","May","June",
    "July","August","September","October","November","December"
];

Sourcemap.Date.get_month_name = function(num) { // 1-12
    return this.months[num];
}

Sourcemap.Date.get_month_abbr = function(num) {
    return this.get_month_name(num).substr(0,3);
}

Sourcemap.Date.format = function(dt) {
    var s = this.get_month_abbr(dt.getMonth());
    s += " "+dt.getDate()+", "+dt.getFullYear();
    return s;
}

Sourcemap.fmt_date = function(dt) {
    return Sourcemap.Date.format(dt);
}
