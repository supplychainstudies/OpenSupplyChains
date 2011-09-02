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

Sourcemap.Form = function(form_el) {
    this._form_el = $(form_el);
    this._rules = [];
    // null means we don't know.
    // false means clean
    this._errors = null;

    this._fields = null;
    this._checkq = [];
    this._lastcheck = 0;
    this._defer = false;
    this.init();
}

Sourcemap.Form.prototype.init = function() {
    var fs = this.fields();
    if(!fs._form_id) return false;
    for(var fn in fs) {
        var fel = $(fs[fn]);
        $(fel).keydown($.proxy(function() {
            this._errors = null;
        }, this));
        if(fel.hasClass('required'))
            this.add_hilite_el(fn);
        if(fel.hasClass('required'))
            this.add_status_el(fn);
    }
    var fso = this.fields(); var fs = []; for(var fn in fso) fs.push(fso[fn]);
    //this.update();
    
    $(this.el()).find('div.error').hide();
    $(this._form_el).bind("submit", $.proxy(function() {
        if(this._errors === false) {
            (this._form_el).find('input[type=submit]').attr("disabled", "true");
            return true;
        }
        this.check(null, $.proxy(function(data) {
            if(data === true) {
                this._form_el.find("form").submit(); 
            } else {
                // pass
            }
        }, this));
        return false;
    }, this));
}

Sourcemap.Form.prototype.toggleThrobber = function() {
    //add "working" class to every field
    var fs = this.fields()
    for(var fn in fs) {
        var fel = $(fs[fn]);
        if (!($(fel).hasClass('working')))
            $(fel).addClass('working');
        else
            $(fel).removeClass('working');
    }
}

Sourcemap.Form.prototype.check = function(evt, cb) {

    // should we enqueue this? (not if it's later than anything in the queue)
    var _t = (new Date()).getTime();
    if(this._lastcheck >= _t) return;
    if(Math.max.apply(window, this._checkq) > _t) return;

    // defer checking again on change events after a blur event
    if(evt && evt.type == "blur") this._defer = true;
    else if(evt && evt.type == "change" && this._defer) {
        this._defer = false;
        return;
    }
    
    this.toggleThrobber();

    var form_id = $(this._form_el).find('input[name=_form_id]').val();
    var p = 'services/validate/'+form_id;
    var data = $(this._form_el).find('input,select,textarea').serializeArray();
    var serial = {};
    for(var i=0; i<data.length; i++) {
        var j = data[i];
        serial[j.name] = j.value;
    }
    var _cb = cb;
    this._checkq.push(_t);
    $.ajax({ "type": "post",
        "url": p, "data": serial,
        "dataType": "json", "success": $.proxy(function(data) {
            if(_t > this._lastcheck) {
                this._lastcheck = _t;
                this.update(data);
                if(_cb && _cb instanceof Function) _cb(data);
                this.toggleThrobber();
            }
            // dequeue outdated checks
            var tmpq = [];
            for(var i=0; i<this._checkq.length; i++) {
                if(this._checkq[i] > _t) tmpq.push(this._checkq[i]);
            }
            this._checkq = tmpq;
        }, this)
    });
    return this;
}

Sourcemap.Form.prototype.update = function(validation) {    
    if(validation !== true) this._errors = validation;
    else this._errors = false;
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
        //this.el().find('input[type="submit"]').attr('disabled', 'disabled');
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
        'input[type="hidden"],input[type="text"],input[type="password"],'+
        'input[type="radio"],input[type="checkbox"],select,textarea,'
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
        if(f.prev().is('div.sourcemap-form-error')) {
            return f.prev();
        } else if(f.parent().prev().is('div.sourcemap-form-error')) {
            return f.parent().prev();
        }
    }
    return null;
}

Sourcemap.Form.prototype.add_error_el = function(field) {
    var f = this.field_el(field);
    if(f && !this.field_error(field)) {
        var html = '<div class="sourcemap-form-error"></div>';
        $(f).parent().before(html);
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

   $('.sourcemap-form textarea').keyup(function() {
        var maxlength = $(this).attr('maxlength');
        if(maxlength != -1) {
            var val = $(this).val();

            if (val.length > maxlength) {
              $(this).val(val.slice(0, maxlength));
            }
        }
    });

});
