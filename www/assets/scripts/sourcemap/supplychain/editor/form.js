if(window.jQuery) {
    // :match selector for jquery
    (function($){
        $.expr[":"].match=function(a,i,m){
            var r=/^\/((?:\\\/|[^\/])+)\/([mig]{0,3})$/,e=r.exec(m[3]);
            return RegExp(e[1],e[2]).test($.trim(a.innerHTML))}
    })(jQuery);
}

Sourcemap = Sourcemap || {};
// todo: make this extensible, templates, etc.
Sourcemap.FormEditor = function(container, supplychain) {
    this.id = Sourcemap.local_id('supplychaineditor-form');
    if(!(container instanceof HTMLElement)) 
        throw new Error('Form-based supplychain editor requires a container.');
    this.container = container;
    try{
        Sourcemap.validate('supplychain', supplychain);
    } catch(e) {
        throw new Error('Invalid supplychain passed to form-based editor.');
    }
    this.dialog = $('<div id="'+this.id+'-dialog"></div>').dialog({
        "modal": true, "autoOpen": false, "title": "Information"
    });
    this.supplychain = supplychain;
    this.broadcast('supplychainFormEditorInstantiated', this);
}

Sourcemap.FormEditor.prototype.defaults = {
    'supplychain_attributes': true, // allowed/required keys and pattern/filter for sc attrs, true = allow all
    'stop_attributes': true, // allowed/req'd keys and pattern/filter for stop attrs
    'hop_attributes': true // allowed/req'd keys and pattern/filter for hop attrs
}

Sourcemap.FormEditor.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.FormEditor.prototype.init = function() {
    this.supplychain.attributes.test = 'hello';
    $(this.container).attr('id', this.id);
    $(this.container).empty();
    $(this.container).append('<button class="supplychain-serialize">serialize</button>');
    $('button.supplychain-serialize', this.container).click({'editor': this}, function(evt) {
        var editor = evt.data.editor;
        Sourcemap.log(editor.serialize());
    });
    $(this.container).append('<br/>');
    $(this.container).append('<fieldset class="supplychain-setattr-form"><legend>Set Property</legend>'+
        '<label>Property:</label><input type="text" class="input-text supplychain-setattr-k" />&nbsp;'+
        '<label>Value:</label><input type="text" class="input-text supplychain-setattr-v" />&nbsp;'+
        '<button class="supplychain-setattr">set property</button></fieldset><br />'
    );
    $(this.container).append('<fieldset class="supplychain-rmattr-form"><legend>Unset Property</legend>'+
        '<label>Property:</label><input type="text" class="input-text supplychain-rmattr-k" />&nbsp;'+
        '<button class="supplychain-rmattr">unset property</button></fieldset><br />'
    );
    $(this.container).append('<fieldset class="supplychain-addstop-form"><legend>Add Stop</legend>'+
        '<label>Lat:</label><input type="text" class="input-text supplychain-addstop-lat" value="0" />&nbsp;'+
        '<label>Lon:</label><input type="text" class="input-text supplychain-addstop-lon" value="0" />&nbsp;'+
        '<button class="supplychain-addstop">add stop</button></fieldset><br />'
    );
    $('#'+this.container.id+' button.supplychain-setattr').click({'editor': this}, function(evt) {
        Sourcemap.log(evt);
        Sourcemap.log(this);
        var editor = evt.data.editor;
        $(this).attr('disabled', true).text('setting...');
        var k = $('#'+$(editor.container).attr('id')+' > .supplychain-setattr-form > .supplychain-setattr-k').val();
        var v = $('#'+$(editor.container).attr('id')+' > .supplychain-setattr-form > .supplychain-setattr-v').val();
        Sourcemap.log(k);
        if(k) editor.setSupplychainAttribute(k, v);
        $(this).attr('disabled', false).text('set property');
    });
    $('#'+this.container.id+' button.supplychain-rmattr').click({'editor': this}, function(evt) {
        var editor = evt.data.editor;
        editor.removeSupplychainAttribute(
            $('#'+$(editor.container).attr('id')+' > .supplychain-rmattr-form > .supplychain-rmattr-k').val()
        );
        $('#'+$(editor.container).attr('id')+' > .supplychain-rmattr-form > .supplychain-rmattr-k').val('')
    });
    $('#'+this.container.id+' button.supplychain-addstop').click({'editor': this}, function(evt) {
        var editor = evt.data.editor;
        var lat = $('#'+editor.container.id+' .supplychain-addstop-lat').val();
        var lon = $('#'+editor.container.id+' .supplychain-addstop-lon').val();
        var ll = new OpenLayers.LonLat(lon, lat);
        ll.transform(
            new OpenLayers.Projection('EPSG:4326'), 
            new OpenLayers.Projection('EPSG:900913')
        );
        var p = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(ll.lon, ll.lat));
        var st = new Sourcemap.Stop((new OpenLayers.Format.WKT()).write(p));
        editor.addStop(st);
    });
    $(this.container).append($('<h3>Properties</h3><dl class="attributes"></dl>'));
    for(var k in this.supplychain.attributes) {
        var v = this.supplychain.attributes[k];
        $('#'+this.container.id+' > dl.attributes').append(
            $('<dt></dt>').text(k).after(
                $('<dd></dd>').text(v)
            )
        );
    }
    $(this.container).append('<div class="stops" id="'+this.id+'-stops"></div>');
    this.stops_container = $('#'+this.id+'-stops').get(0);
    $(this.container).append('<div class="hops" id="'+this.id+'-hops"><div>');
    this.hops_container = $('#'+this.id+'-hops').get(0);
    for(var i=0; i<this.supplychain.stops.length; i++) {
        this.addStop(this.supplychain.stops[i]);
    }
    return this;
}

Sourcemap.FormEditor.prototype.serialize = function() {
    var serialized = {};
    serialized.remote_id = this.supplychain.remote_id;
    var scattr_ks = $('#'+this.container.id+"> dl.attributes dt");
    var scattr_vs = $('#'+this.container.id+"> dl.attributes dd");
    serialized.attributes = {};
    for(var i=0; i<scattr_ks.length; i++) 
        serialized.attributes[$(scattr_ks[i]).text()] = $(scattr_vs[i]).text();
    // todo: stops, attrs
    var stop_els = $('> .stops > .formeditor-stop', this.container);
    serialized.stops = [];
    for(var i=0; i<stop_els.length; i++)
        serialized.stops.push(this.serializeStop(stop_els[i]));
    // todo: hops, attrs
    return serialized;
}

Sourcemap.FormEditor.prototype.serializeStop = function(stop_el) {
    var stop = false;
    stop = {};
    var lat = parseFloat($('.lonlat > .lat', stop_el).text());
    var lon = parseFloat($('.lonlat > .lon', stop_el).text());
    var ll = new OpenLayers.LonLat(lon, lat);
    ll.transform(
        new OpenLayers.Projection('EPSG:4326'),
        new OpenLayers.Projection('EPSG:900913')
    );
    stop.geometry = (new OpenLayers.Format.WKT()).write(
        new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Point(ll.lon, ll.lat)
        )
    );
    var attrs_ks = $('> dl.attributes dt', stop_el);
    var attrs_vs = $('> dl.attributes dd', stop_el);
    for(var i=0, attrs={}; i<attrs_ks.length; i++)
        attrs[$(attrs_ks[i]).text()] = $(attrs_vs[i]).text();
    stop.attributes = attrs;
    return stop;
}

Sourcemap.FormEditor.prototype.getSupplychainAttributes = function() {
    var scattr_ks = $('#'+this.container.id+"> dl.attributes dt");
    var scattr_vs = $('#'+this.container.id+"> dl.attributes dd");
    sc_attrs = {};
    for(var i=0; i<scattr_ks.length; i++) 
        sc_attrs[$(scattr_ks[i]).text()] = $(scattr_vs[i]).text();
    return sc_attrs;
}

Sourcemap.FormEditor.prototype.getSupplychainAttribute = function(k, d) {
    var attr = this.getSupplychainAttributes()[k];
    if(typeof attr === 'undefined') attr = d;
    return attr;
}

Sourcemap.FormEditor.prototype.setSupplychainAttribute = function(k, v) {
    var curr = this.getSupplychainAttribute(k, false);
    var set = false;
    if(curr !== false) {
        set = this.updateSupplychainAttribute(k, v);
    } else {
        set = this.addSupplychainAttribute(k, v);
    }
    if(!set) throw new Error('Could not set "'+k+'" for "'+this.supplychain.local_id+'".');
    return this;
}

Sourcemap.FormEditor.prototype.updateSupplychainAttribute = function(k, v) {
    $('#'+this.container.id+' > dl.attributes dt:match("/'+k+'/") + dd').text(v);
    return this;
}

Sourcemap.FormEditor.prototype.addSupplychainAttribute = function(k, v) {
    $('#'+this.container.id+' > dl.attributes').append(
        $('<dt></dt>').text(k).after(
            $('<dd></dd>').text(v)
        )
    );
    return this;
}

Sourcemap.FormEditor.prototype.removeSupplychainAttribute = function(k) {
    var kel = $('#'+this.container.id+' > dl.attributes dt:match("/'+k+'/")');
    var vel = $('#'+this.container.id+' > dl.attributes dt:match("/'+k+'/") + dd');
    kel.remove();
    vel.remove();
    return this;
}

Sourcemap.FormEditor.prototype.addStop = function(stop) {
    var stop = stop instanceof Sourcemap.Stop ? stop : new Sourcemap.Stop(stop.geometry, stop.attributes);
    var f = (new OpenLayers.Format.WKT()).read(stop.geometry);
    var pf = new OpenLayers.Projection('EPSG:900913');
    var pt = new OpenLayers.Projection('EPSG:4326');
    var p = f.geometry.transform(pf, pt);
    var ll = new OpenLayers.LonLat(p.x, p.y);
    $(this.stops_container).append('<div class="formeditor-stop" id="'+this.id+'-'+stop.local_id+'">'+
        '<h3 class="formedit-stop-id">'+stop.local_id+'</h3>'+
        '<button class="rmstop">remove this stop</button>'+
        '<div class="lonlat"><span class="lat">'+ll.lat+'</span>, <span class="lon">'+ll.lon+'</span></div>'+
        '<br /><label>Move to address:</label><input type="text" class="input-text stop-moveto-v" />&nbsp;'+
        '<button class="stop-moveto">find address</button>'+
        '<fieldset class="addstopattr-form"><legend>Set Property</legend>'+
        '<label>Property:</label><input type="text" class="input-text addstopattr-k" />&nbsp;'+
        '<label>Value:</label><input type="text" class="input-text addstopattr-v" />&nbsp;'+
        '<button class="addstopattr">set property</button></fieldset>'+
        '<dl class="attributes"></dl><button class="stop-geocode">geocode</button>'+
        '<!--button class="stop-edit">edit</button--></div>'
    );
    $('#'+this.id+'-'+stop.local_id+' > .addstopattr-form > button.addstopattr').click(
        {'editor': this, 'stop_id': stop.local_id}, 
        function(evt) {
            var editor = evt.data.editor;
            var stop_id = evt.data.stop_id;
            var k = $(this).parent().find('.addstopattr-k').val();
            var v = $(this).parent().find('.addstopattr-v').val();
            editor.setStopAttribute(stop_id, k, v);
            $(this).parent().find('.addstopattr-k').val('');
            $(this).parent().find('.addstopattr-v').val('');
        }
    );
    $('#'+this.id+'-'+stop.local_id+' > .rmstop').click({'editor': this, 'stop_id': stop.local_id},
        function(evt) {
            var _editor = evt.data.editor;
            var _stop_id = evt.data.stop_id;
            _editor.dialog.text('Are you sure?');
            _editor.dialog.dialog("option", "buttons", {
                "OK": function() {
                    _editor.removeStop(_stop_id);
                    $(this).dialog("close");
                },
                "Cancel": function() {
                    $(this).dialog("close");
                }
            });
            _editor.dialog.dialog("open");
        }
    );
    $('#'+this.id+'-'+stop.local_id+' > .stop-moveto').click({'editor': this, 'stop_id': stop.local_id},
        function(evt) {
            var editor = evt.data.editor;
            var _editor = editor;
            var stop_id = evt.data.stop_id;
            var _stop_id = stop_id;
            var addr = $('#'+editor.id+'-'+stop_id+' > .stop-moveto-v').val();
            var geocoder = new google.maps.Geocoder();
            editor.dialog.dialog("open");
            editor.dialog.text('Searching for that location...');
            geocoder.geocode({'address': addr}, function(results, stat) {
                if(results.length) {
                    var new_addr = results[0].formatted_address;
                    var new_geom = results[0].geometry.location;
                    var new_lat = new_geom.wa;
                    var new_lon = new_geom.xa;
                    //editor.dialog.dialog("close");
                    editor.dialog.text('Did you mean "'+new_addr+'"?');
                    editor.dialog.dialog("option", "buttons", {
                        "OK": function() {
                            $('#'+_editor.id+'-'+_stop_id+' > .stop-moveto-v').val('');
                            $('#'+_editor.id+'-'+_stop_id+' > .lonlat > .lat').text(new_lat);
                            $('#'+_editor.id+'-'+_stop_id+' > .lonlat > .lon').text(new_lon);
                            _editor.dialog.dialog("close");
                        },
                        "Cancel": function() {
                            _editor.dialog.dialog("close");
                        }
                    });
                } else {
                    editor.dialog.text('We could not locate that address. Please, try again.');
                    editor.dialog.dialog("option", "buttons", {
                        "Dismiss": function() { $(this).dialog("close"); }
                    });
                }
            });
        }
    );
    for(var k in stop.attributes) {
        this.setStopAttribute(stop.id, k, stop.attributes[k]);
    }
    var data = {"stop_id": stop.local_id, "editor_id": this.id, "editor": this};
    $('#'+this.id+'-'+stop.local_id+' button.stop-geocode').click(data, function(evt) {
        var editor = evt.data.editor;
        var stop_id = evt.data.stop_id;
        var lat = parseFloat($('#'+editor.id+'-'+stop_id+' > .lonlat > .lat').text());
        var lon = parseFloat($('#'+editor.id+'-'+stop_id+' > .lonlat > .lon').text());
        var ll = new OpenLayers.LonLat(lon, lat);
        var _stelid = evt.data.editor_id+'-'+stop.local_id;
        $(this).val('working...').attr("disabled", true);
        var _editor = evt.data.editor;
        var _stop_id = evt.data.stop_id;
        (new google.maps.Geocoder()).geocode({'latLng': new google.maps.LatLng(ll.lat, ll.lon)}, function(results, stat) {
            Sourcemap.log(results);
            if(results.length) _editor.setStopAttribute(_stop_id, "placename", results[0].formatted_address);
            //$('#'+_stelid+' button.stop-geocode').remove();
            $('#'+_stelid+' button.stop-geocode').attr("disabled", false);
        });
        // todo: edit button click()
    });
}

Sourcemap.FormEditor.prototype.removeStop = function(stop_id) {
    $('#'+this.id+'-'+stop_id).remove();
    return this;
}

Sourcemap.FormEditor.prototype.getStopAttribute = function(stop_id, k, d) {
    var attr = d;
    var m = $('#'+this.id+'-'+stop_id+' > dl.attributes > dt:match("/'+k+'/")');
    if(m.length) {
        attr = m.text();
    }
    return attr;
}

Sourcemap.FormEditor.prototype.setStopAttribute = function(stop_id, k, v) {
    var curr = this.getStopAttribute(stop_id, k, false);
    var set = false;
    if(curr !== false) {
        set = this.updateStopAttribute(stop_id, k, v);
    } else {
        set = this.addStopAttribute(stop_id, k, v);
    }
    if(!set) throw new Error('Could not set "'+k+'" for "'+stop_id+'".');
    return this;
}

Sourcemap.FormEditor.prototype.updateStopAttribute = function(stop_id, k, v) {
    $('#'+this.id+'-'+stop_id+' > dl.attributes dt:match("/'+k+'/") + dd').text(v);
    return this;
}


Sourcemap.FormEditor.prototype.addStopAttribute = function(stop_id, k, v) {
    $('#'+this.id+'-'+stop_id+' > dl.attributes').append(
        $('<dt></dt>').text(k).after(
            $('<dd></dd>').text(v)
        )
    );
    return this;
}

Sourcemap.FormEditor.prototype.removeStopAttribute = function(stop_id, k) {
    var kel = $('#'+this.id+'-'+stop_id+' > dl.attributes dt:match("/'+k+'/")');
    var vel = $('+ dd', kel);
    kel.remove();
    vel.remove();
    return this;
}

Sourcemap.FormEditor.prototype.addHop = function(from_stop_id, to_stop_id) {

}

Sourcemap.FormEditor.prototype.addHopAttribute = function(hop_id) {

}
