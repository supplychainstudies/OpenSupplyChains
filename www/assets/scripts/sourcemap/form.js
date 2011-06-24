Sourcemap.Form = function(form_el) {
    this._form_el = $(form_el);
    this._rules = [];
    this._errors = false;

    this._fields = null;

    this.init();
}

Sourcemap.Form.Validators = {
    "not_empty": function(v, f, r, a) { return (typeof v) === "string" && v.length; },
    "email": function(v, f, r, a) {
        var p = /^[A-Za-z0-9._%\+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/;
        return p.test(v);
    },
    "alphadash": function(v, f, r, a) {
        var p = /^[A-Za-z0-9_-]+$/
        return p.test(v);
    },
    "long": function(v, f, r, a) {
        var p = /[A-Za-z0-9_-]{7,}+$/
        return p.test(v);
    },
    "confirm": function(v, f, r, a) {
        var m = f.replace('_confirm', '');
        return this.field_el(m).val() == v;
    },
    "tags": function(v, f, r, a) {
        var p = /^(\s+)?(\w+(\s+)?)*$/; 
        return v.length ? p.test(v) : true;
    }
}

Sourcemap.Form.ValidatorClassMap = {
    "required": "not_empty",
    "email": "email",
    "alphadash": "alphadash",
    "long": "long",
    "confirm": "confirm",
    "tags": "tags"
}

Sourcemap.Form.ValidatorEMsgs = {
    "required": "Required.",
    "email": "Must be a valid email address.",
    "alphadash": "May contain only letters, numbers, underscores, and dashes.",
    "long": "Must be eight characters or longer.",
    "confirm": 'Doesn\'t match.',
    "tags": 'List tags separated by spaces.'
}

Sourcemap.Form.prototype.init = function() {
    var fs = this.fields();
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
        for(var vmk in Sourcemap.Form.ValidatorClassMap) {
            if(fel.hasClass(vmk)) {
                this.rule(fn, 
                    Sourcemap.Form.ValidatorClassMap[vmk], [], 
                    Sourcemap.Form.ValidatorEMsgs[vmk]
                );
            }
        }
        if(fel.hasClass('required') || this.field_rules(fn).length)
            this.add_status_el(fn);
    }
    var fso = this.fields(); var fs = []; for(var fn in fso) fs.push(fso[fn]);
    this.update();
    $(this.el()).find('div.error').hide();
}

Sourcemap.Form.prototype.update = function() {
    for(var fn in this.fields()) {
        this.rm_error_el(fn);
        //this.field_el(fn).removeClass('error');
        if(this.field_status(fn)) 
            this.field_status(fn).removeClass('invalid');
        if(this.field_error(fn) && this.field_error(fn).hasClass('preserve'))
            continue;
        this.rm_error_el(fn);
    }
    if(this.check()) {
        this.el().find('input[type="submit"]').removeAttr('disabled');
    } else {
        this.el().find('input[type="submit"]').attr('disabled', 'disabled');
        var es = this.errors();
        for(var roi=0; roi<es.length; roi++) {
            var ro = es[roi];
            var fn = ro.f;
            var emsg = ro.emsg ? ro.emsg : 'Invalid.';
            this.field_status(fn).addClass('invalid');
            if(this.field_error(fn) && this.field_error(fn).hasClass('preserve'))
                continue;
            this.add_error_el(fn);
            this.field_error(fn).text(emsg);
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

Sourcemap.Form.prototype.check = function() {
    this._errors = [];
    if(this._rules) {
        for(var i=0; i<this._rules.length; i++) {
            var ro = this._rules[i];
            var f = $(this._form_el).find('[name="'+ro.f+'"]');
            if(f.length) f = f[0];
            else {
                this._errors.push(ro);
                continue;
            }
            if(this.check_rule(ro, $(f).val())) {
                // pass
            } else {
                this._errors.push(ro);
            }
        }
    }
    return !this._errors.length;
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
    var f = this.field_el(field).parent();

    var s = null;
    if(f && f.prev().is('div.status'))
        s = f.prev();
    return s;
}

Sourcemap.Form.prototype.add_status_el = function(field) {
    var f = this.field_el(field).parent();
    if(f && !this.field_status(field)) {
        f.before('<div class="status invalid"></div>');
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
        if(f.next().is('div.error')) {
            return f.next();
        } else if(f.next().next().is('div.error')) {
            return f.next().next();
        }
    }
    return null;
}

Sourcemap.Form.prototype.add_error_el = function(field) {
    var f = this.field_el(field);
    if(f && !this.field_error(field)) {
        var html = '<div class="error"></div>';
        if(this.field_status(field))
            this.field_status(field).after(html);
        else
            $(f).after(html);
    }
    return this;
}

Sourcemap.Form.prototype.rm_error_el = function(field) {
    var fe = this.field_error(field);
    if(fe && !fe.hasClass('preserve')) fe.remove();
    return this;
}

$(document).ready(function() {
    $('div.sourcemap-form').each(function() {
        (new Sourcemap.Form(this));
    });
});
