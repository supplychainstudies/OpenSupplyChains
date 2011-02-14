Sourcemap.TabbedEditor = function(el, sc, o) {
    this.id = Sourcemap.instance_id('tabbed-editor');
    this.container = $(el).get(0);
    this.supplychain = sc;
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.TabbedEditor.prototype.defaults = {
    "auto_init": true
};

Sourcemap.TabbedEditor.prototype.init = function() {
    //todo: preview link/button
    var tabsul = $(
        '<ul><li><a href="#'+this.id+'-sc">Supplychain</a></li>'+
        '<li><a href="#'+this.id+'-stops">Stops</a></li>'+
        '<li><a href="#'+this.id+'-hops">Hops</a></li></ul>'
    );
    var tabels = $(
        '<div id="'+this.id+'-sc"><h3>Supplychain Details</h3></div>'+
        '<div id="'+this.id+'-stops"><h3>Stops</h3></div>'+
        '<div id="'+this.id+'-hops"><h3>Hops</h3></div>'
    );
    var dialogel = $(
        '<div id="'+this.id+'-dialog"></div>'
    );
    $(this.container).append(tabsul);
    $(this.container).append(tabels);
    $(this.container).after(dialogel);
    $(dialogel).dialog({"modal": true, "buttons": {
        "Save": function() {
            $(this).dialog("close");
        }
    }});
    $(this.container).tabs();
    this.update();
    this.editStop();
}

Sourcemap.TabbedEditor.prototype.update = function() {
    this.updateSupplychainTab();
    this.updateStopsTab();
    this.updateHopsTab();
}

Sourcemap.TabbedEditor.prototype.updateSupplychainTab = function() {
    Sourcemap.template('supplychain/editor/tabbed/sc-tab', 
        this.supplychainTab(), 
        this.supplychain
    );
}

Sourcemap.TabbedEditor.prototype.updateStopsTab = function() {
    var _that = this;
    Sourcemap.template('supplychain/editor/tabbed/stops-tab',
        $.proxy(function(tpath, txt, thtml) {
            $(this.stopsTab()).html(thtml).find('td.buttons button.edit')
                .click(function() {
                    _that.editStop($(this).parents('tr').get(0).id);
                });
        }, this),
        this.supplychain
    );
}

Sourcemap.TabbedEditor.prototype.updateHopsTab = function() {
    Sourcemap.template('supplychain/editor/tabbed/hops-tab', 
        this.hopsTab(), 
        this.supplychain
    );
}
Sourcemap.TabbedEditor.prototype.supplychainTab = function() {
    return $('#'+this.id+'-sc').get(0);
}

Sourcemap.TabbedEditor.prototype.stopsTab = function() {
    return $('#'+this.id+'-stops').get(0);
}

Sourcemap.TabbedEditor.prototype.hopsTab = function() {
    return $('#'+this.id+'-hops').get(0);
}

Sourcemap.TabbedEditor.prototype.dialogEl = function() {
    return $('#'+this.id+'-dialog').get(0);
}

Sourcemap.TabbedEditor.prototype.editStop = function(stop_id) {
    var stop = stop_id ? this.supplychain.findStop(stop_id) : new Sourcemap.Stop()
    Sourcemap.template('supplychain/editor/tabbed/edit-stop',
        $.proxy(function(tpath, txt, thtml) {
            $(this.dialogEl()).html(thtml);
            $(this.dialogEl()).find("input.placename").geocomplete();
            $(this.dialogEl()).dialog("open");
        }, this),
        stop
    );
    var _that = this;
    var _stop_id = stop_id;
    $(this.dialogEl()).dialog("option", "buttons", {
        "Save": function() {
            var ser_arr = $(this).find('form').serializeArray();
            var ser_obj = {};
            for(var si=0; si<ser_arr.length; si++) 
                ser_obj[ser_arr[si].name] = ser_arr[si].value;
            if(_that.saveStop(stop_id, ser_obj)) {
                _that.update();
                $(this).dialog("close");
            } else {
                console.log('do something about errors on save.');
            }
        }
    });
}

Sourcemap.TabbedEditor.prototype.saveStop = function(stop_id, o) {
    console.log('savestop'); console.log(arguments);
    var stop = null;
    if(stop_id) {
        stop = this.supplychain.findStop(stop_id);
        if(!stop) throw new Error("Could not find stop with id: "+stop_id+".");
        if(geo_r = Sourcemap.geocode_cache[o.placename]) {
            stop.geometry = Sourcemap.Stop.fromLonLat(geo_r).geometry;
        }
    } else {
        var geo_r = Sourcemap.geocode_cache[o.placename];
        if(!geo_r) geo_r = {"lat": 0.0, "lon": 0.0};
        stop = Sourcemap.Stop.fromLonLat(geo_r);
        if(!stop) throw new Error("Could not create stop.");
        this.supplychain.addStop(stop);
    }
    stop.attributes["org.sourcemap.name"] = o.name;
    stop.attributes["org.sourcemap.placename"] = o.placename;
    return stop;
}
