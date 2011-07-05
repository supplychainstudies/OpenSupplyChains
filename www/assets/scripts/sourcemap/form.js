Sourcemap.Form = function(form_el) {
    this._form_el = $(form_el);
    this._rules = [];
    this._errors = false;

    this._fields = null;

    this.init();
}

Sourcemap.Form.prototype.init = function() {
    var fs = this.fields();
    if(!fs._form_id) return false;
    for(var fn in fs) {
        var fel = $(fs[fn]);
        $(fel).focus(function(e){
            if (this.defaultValue == this.value)
                this.value = "";
        });
        $(fel).blur(function(e){
            if (this.value === "")
                this.value = this.defaultValue;
        });
        $(fel).change($.proxy(function() {
            this.update();
        }, this));
        if(fel.hasClass('required'))
            this.add_hilite_el(fn);
        if(fel.hasClass('required'))
            this.add_status_el(fn);
    }
    var fso = this.fields(); var fs = []; for(var fn in fso) fs.push(fso[fn]);
    this.update();
    
    $(this.el()).find('div.error').hide();
    $(this._form_el).find('input, select').change($.proxy(this.check, this));
}

Sourcemap.Form.prototype.check = function() {
    var form_id = $(this._form_el).find('input[name=_form_id]').val();
    var p = 'services/validate/'+form_id;
    var data = $(this._form_el).find('input, select').serializeArray();
    var serial = {};
    for(var i=0; i<data.length; i++) {
        var j = data[i];
        serial[j.name] = j.value;
    }
    $.ajax({ "type": "post",
        "url": p, "data": serial,
        "dataType": "json", "success": $.proxy(function(data) {
            this.update(data);
        }, this)
    });
    return this;
}

Sourcemap.Form.prototype.update = function(validation) {
    if(validation !== true) this._errors = validation;
    for(var fn in this.fields()) {
        this.rm_error_el(fn);
        //this.field_el(fn).removeClass('error');
        if(this.field_status(fn)) 
            this.field_status(fn).removeClass('invalid');
        if(this.field_error(fn) && this.field_error(fn).hasClass('preserve'))
            continue;
        this.rm_error_el(fn);
    }
    if(validation === true) {
        this.el().find('input[type="submit"]').removeAttr('disabled');
    } else {
        this.el().find('input[type="submit"]').attr('disabled', 'disabled');
        var eks = this.errors();
        for(var f in eks) {
            var es = eks[f];
            for(var roi=0; roi<es.length; roi++) {
                var ro = es[roi];
                var fn = f;
                var emsg = ro;
                //this.field_status(fn).addClass('invalid');
                //if(this.field_error(fn) && this.field_error(fn).hasClass('preserve'))
                //    continue;
                if(!this.field_error(fn))
                    this.add_error_el(fn);
                this.field_error(fn).text(emsg);
            }
        }
    }
    $(this.el()).find('div.error').show();
}

Sourcemap.Form.prototype.fields = function() {
    var f = this.el();
    var fields = {};
    var field_els = f.find(
        'input[type="text"],input[type="password"],'+
        'input[type="radio"],input[type="checkbox"],select,textarea'
    );
    for(var i=0; i<field_els.length; i++) {
        if($(field_els[i]).attr('name'))
            fields[$(field_els[i]).attr('name')] = field_els[i];
    }
    return fields;
}

Sourcemap.Form.prototype.errors = function() {
    return this._errors;
}

Sourcemap.Form.prototype.el = function() {
    return this._form_el;
}

Sourcemap.Form.prototype.rule = function(field, rule, arglist, emsg) {
    this._rules.push({"f": field, "r": rule, "a": arglist, "emsg": emsg});
    return this;
}

Sourcemap.Form.prototype.field_rules = function(field) {
    var ros = [];
    for(var i=0; i<this._rules.length; i++) {
        var ro = this._rules[i];
        if(ro.f == field) ros.push(ro);
    }
    return ros;
}

Sourcemap.Form.prototype.check_rule = function(rdat, val) {
    if(!rdat || !(rdat.f && rdat.r))
        return false;

    var f = rdat.f;
    var r = rdat.r;
    var a = rdat.a && rdat.a.length ? rdat.a : [];

    if(typeof r === "function") {
        // pass
    } else if(Sourcemap.Form.Validators[r]) {
        r = $.proxy(Sourcemap.Form.Validators[r], this);
    } else {
        return false;
    }

    return r(val, f, r, a);
    
}

Sourcemap.Form.prototype.field_el = function(field) {
    var f = $(this.el()).find('input[name="'+field+'"],select[name="'+field+'"],textarea[name="'+field+'"]');
    if(!f.length) f = null;
    return f;
}

Sourcemap.Form.prototype.field_label = function(field) {
    var f = this.field_el(field);
    var l = $(f).prev('label[for="'+field+'"]');
    l = l.length ? l : null;
    return l;
}

Sourcemap.Form.prototype.field_hilite = function(field) {
    var l = this.field_label(field);
    if(l && l.next().is('span.highlighted')) {
        return l.next();
    }
    return null;
}

Sourcemap.Form.prototype.add_hilite_el = function(field) {
    if(!this.field_hilite(field)) {
        var l = this.field_label(field);
        if(l) l.after('<span class="highlighted">*</span>');
    }
    return this;
}

Sourcemap.Form.prototype.rm_hilite_el = function(field) {
    var h = this.field_hilite(field);
    if(h) h.remove();
    return this;
}

Sourcemap.Form.prototype.field_status = function(field) {
    var f = this.field_el(field);
    var s = null;
    if(f && f.next().is('div.status'))
        s = f.next();
    return s;
}

Sourcemap.Form.prototype.add_status_el = function(field) {
    var f = this.field_el(field);
    console.log(f);
    if(f && !this.field_status(field)) {
        f.after('<div class="status invalid"></div>');
    }
    return this;
}

Sourcemap.Form.prototype.rm_status_el = function(field) {
    var s = this.field_status(field);
    if(s) s.remove();
    return this;
}

Sourcemap.Form.prototype.field_error = function(field) {
    var f = this.field_el(field);
    if(f) {
        if(f.next().is('div.sourcemap-form-error')) {
            return f.next();
        } else if(f.parent().next().is('div.sourcemap-form-error')) {
            return f.parent(t).next();
        }
    }
    return null;
}

Sourcemap.Form.prototype.add_error_el = function(field) {
    var f = this.field_el(field);
    if(f && !this.field_error(field)) {
        var html = '<div class="sourcemap-form-error"></div>';
        $(f).after(html);
    }
    return this;
}

Sourcemap.Form.prototype.rm_error_el = function(field) {
    var fe = this.field_error(field);
    if(fe && !fe.hasClass('preserve')) fe.remove();
    return this;
}

/*$.fn.listenForChange = function(options) {
    settings = $.extend({
        interval: 200 // in microseconds
    }, options);

    var jquery_object = this;
    var current_focus = null;

    jquery_object.filter(":input").add(":input", jquery_object).focus( function() {
        current_focus = this;
    }).blur( function() {
        current_focus = null;
    });

    setInterval(function() {
        // allow
        jquery_object.filter(":input").add(":input", jquery_object).each(function() {
            // set data cache on element to input value if not yet set
            if ($(this).data('change_listener') == undefined) {
                $(this).data('change_listener', $(this).val());
                return;
            }
            // return if the value matches the cache
            if ($(this).data('change_listener') == $(this).val()) {
                return;
            }
            // ignore if element is in focus (since change event will fire on blur)
            if (this == current_focus) {
                return;
            }
            // if we make it here, manually fire the change event and set the new value
            $(this).trigger('change');
            $(this).data('change_listener', $(this).val());
        });
    }, settings.interval);
    return this;
};
*/

$(document).ready(function() {
    $('div.sourcemap-form').each(function() {
        (new Sourcemap.Form(this));
    });

    $('.sourcemap-form input').each( function(){
        $(this).focus(function(e){
            if (this.defaultValue == this.value)
                this.value = "";
        });
        $(this).blur(function(e){
            if (this.value === "")
                this.value = this.defaultValue;
        });
        $(this).keydown(function(e){
            $(this).addClass('edited');
        });
    });

});
