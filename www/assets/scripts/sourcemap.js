Sourcemap = {};

Sourcemap.ERROR = 1;
Sourcemap.WARNING = 2;
Sourcemap.INFO = 4;

Sourcemap.options = {
    "log_level": Sourcemap.ERROR | Sourcemap.WARNING | Sourcemap.INFO
};

Sourcemap.log = function(message, level) {
    var level = typeof level === "undefined" ? Sourcemap.INFO : level;
    if(level & Sourcemap.options.log_level) {
        if(console && console.log) console.log(message);
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
        } else {
            var r = o.constructor ? new o.constructor() : {};
            for(var k in o) {
                r[k] = Sourcemap.deep_clone(o[k]);
            }
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
