Sourcemap.TPL_PATH = "/assets/scripts/sourcemap/tpl/";
Sourcemap.TPL_EXT = ".js.tpl";


Sourcemap.TemplateLoader = function(tpls, o) {
    this.templates = tpls;
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.TemplateLoader.prototype.defaults = {
    "auto_init": true
}

Sourcemap.TemplateLoader.prototype.init = function() {
    this.id = Sourcemap.local_id("template_loader");
    Sourcemap.broadcast('template_loader:init', this.id);
    this.queue = [];
    this.loaded = [];
}

Sourcemap.TemplateLoader.prototype.fetch = function(tpl) {
    if(this.queue.indexOf(tpl) >= 0) return;
    this.queue.push(tpl);
    $.get(Sourcemap.TPL_PATH+tpl+Sourcemap.TPL_EXT, {
        "success": function() {
            Sourcemap.broadcast('template_loader:template_loaded');
        }
    });
}

Sourcemap.TemplateLoader.prototype.get_id = function(tpl) {
    return tpl.replace(/\w+/, '-').toLowerCase();
}

Sourcemap.TemplateLoader.prototype.load = function() {
    for(var t=0; t<this.templates.length; t++) {
        if(!$('#'+this.get_id(this.templates[t]))) {
            this.fetch(this.templates[t]);
        } else {
            this.loaded.push(this.templates[t]);
        }
    }
}

Sourcemap.load_templates = function(tpls, callback) {
    var loader = new Sourcemap.TemplateLoader(tpls);
    loader.load(callback);
}

Sourcemap.template = function(tpath, ucallback) {
    var context = context || {};
    var ucallback = ucallback || function(str) {};
    Sourcemap.load_templates([tpath], function(tpath, tid) {
        Sourcemap.broadcast("template:loaded", tpath, tid);
    });
}
