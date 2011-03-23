Sourcemap.TPL_PATH = "assets/scripts/sourcemap/tpl/";
Sourcemap.TPL_EXT = ".jstpl";

Sourcemap.loaded_templates = {};
Sourcemap.template_queue = [];


Sourcemap.TemplateLoader = function(o) {
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.TemplateLoader.prototype.defaults = {
    "auto_init": true, "defer_interval": 20
}

Sourcemap.TemplateLoader.prototype.init = function() {
    this.id = Sourcemap.instance_id("template_loader");
    this.defer_interval = this.options.defer_interval;
    Sourcemap.broadcast('template_loader:init', this.id);
}

Sourcemap.TemplateLoader.prototype.fetch = function(tpl, callback) {
    Sourcemap.template_queue.push(this.get_id(tpl));
    var tpl_path = Sourcemap.TPL_PATH+tpl+Sourcemap.TPL_EXT;
    var __callback = callback;
    var __that = this;
    var __tpl = tpl;
    $.ajax({
        "type": "get", "url": tpl_path, "dataType": "text",
        "success": function(resptxt) {
            delete Sourcemap.template_queue[Sourcemap.template_queue.indexOf(__tpl)];
            __that.__callbackWrapper.call(__that, __tpl, resptxt, __callback);
        },
        "error": function() {
            delete Sourcemap.template_queue[Sourcemap.template_queue.indexOf(__tpl)];
            Sourcemap.log("Could not load template "+__tpl+".", Sourcemap.WARNING);
        }
    });
}

Sourcemap.TemplateLoader.prototype.__callbackWrapper = function(tpl, resptxt, callback) {
    Sourcemap.loaded_templates[this.get_id(tpl)] = resptxt;
    return callback(tpl, resptxt, this);
}

Sourcemap.TemplateLoader.prototype.get_id = function(tpl) {
    return "sourcemap-jstpl-"+tpl.replace(/\W+/g, '-').toLowerCase();
}

Sourcemap.TemplateLoader.prototype.load = function(templates, callback) {
    if(!templates) {
        alert(templates);
        return;
    }
    if(!templates.length) {
        alert(templates);
        return;
    }
    for(var t=0; t<templates.length; t++) {
        if(Sourcemap.loaded_templates[this.get_id(templates[t])]) {
            this.__callbackWrapper(templates[t], Sourcemap.loaded_templates[this.get_id(templates[t])], callback);
        } else if(Sourcemap.template_queue.indexOf(this.get_id(templates[t])) >= 0) {
            setTimeout($.proxy(this.load, this), this.defer_interval, [templates[t]], callback);
        } else {
            this.fetch(templates[t], callback);
        }
    }
}

Sourcemap.load_templates = function(tpls, callback) {
    var loader = new Sourcemap.TemplateLoader();
    loader.load(tpls, callback);
}

Sourcemap.template = function(tpath, ucallback, context, scope) {
    // this function takes a path, callback, context and scope OR
    // a path, target element(s) and context
    // tpath is required
    // ucallback is a function or a target element(s)
    // context is the data used to evaluate the template
    // the callback is bound to scope, if provided
    var context = context || {};
    if(!(ucallback instanceof Function)) {
        var __el = ucallback;
        var __ctxt = context || window;
        var ucallback = function(tpl, txt, thtml) {
            $(__el).html(thtml);
        }
    }
    if(scope) {
        ucallback = $.proxy(ucallback, scope);
    }
    Sourcemap.load_templates([tpath], function(tpl, txt, loader) {
        Sourcemap.broadcast("template:loaded", tpath, txt);
        var thtml = $('<script type="text/html">'+txt+'</script>').jqote(context);
        ucallback(tpl, txt, thtml);
    });
}
