Sourcemap.Map.Editor = function(map, o) {
    var o = o || {}
    this.map = map.map ? map.map : map;
    if(map instanceof Sourcemap.Map.Base)
        this.map_view = map;
    Sourcemap.Configurable.call(this);
    this.instance_id = Sourcemap.instance_id("sourcemap-editor");
}

Sourcemap.Map.Editor.prototype = new Sourcemap.Configurable();

Sourcemap.Map.Editor.prototype.defaults = {
    "trigger_events": true, "auto_init": true
}

Sourcemap.Map.Editor.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.Editor.prototype.init = function() {

    // listen for supplychain updates and save
    Sourcemap.listen('supplychain-updated', function(evt, sc) {
        var succ = $.proxy(function() {
            this.map_view.updateStatus("Saved...", "good-news");
        }, this);
        var fail = $.proxy(function() {
            this.map_view.updateStatus("Could not save! Contact support.", "bad-news");
        }, this);
        this.map_view.updateStatus("Saving...");
        Sourcemap.saveSupplychain(sc, {"supplychain_id": sc.remote_id, "success": succ, "failure": fail});
    }, this);

    // decorate prep_popup
    Sourcemap.listen('popup-initialized', $.proxy(function(evt, p, ref) {
        // todo: make popup buttons part of the popup class?
        $(p.contentDiv).find('.popup-wrapper .popup-buttons').append(
            '<a class="popup-edit-link" href="javascript: void(0);">Edit</a>'
        );
        $(p.contentDiv).find('.popup-edit-link').click($.proxy(function(e) {
            var reftype = ref instanceof Sourcemap.Hop ? 'hop' : 'stop';
            Sourcemap.template('map/edit/edit-'+reftype, function(p, tx, th) {
                this.editor.map_view.showDialog(th, true);
                $(this.editor.map_view.dialog).find('.edit-save').click($.proxy(function(e) {
                    // save updated attributes
                    var f = $(e.target).parent();
                    var vals = f.serializeArray();
                    for(var k in vals) {
                        var val = vals[k].value;
                        k = vals[k].name;
                        if(k === "address" && (val != this.ref.getAttr("address", false))) {
                            this.ref.setAttr(k, val);
                            // if address is set, move the stop.
                            $(f).find('input,textarea,select').attr("disabled", true);
                            Sourcemap.Stop.geocode(this.ref.getAttr("address"), $.proxy(function(res) {
                                var pl = res && res.results ? res.results[0] : false;
                                if(pl) {
                                    this.stop.setAttr("address", pl.placename);
                                    var new_geom = new OpenLayers.Geometry.Point(pl.lon, pl.lat);
                                    new_geom = new_geom.transform(
                                        new OpenLayers.Projection('EPSG:4326'),
                                        new OpenLayers.Projection('EPSG:900913')
                                    );
                                    this.stop.geometry = (new OpenLayers.Format.WKT()).write(new OpenLayers.Feature.Vector(new_geom))
                                    this.editor.map.mapStop(this.stop, this.stop.supplychain_id);
                                    this.editor.map.map.zoomToExtent(this.editor.map.getStopLayer(this.stop.supplychain_id).getDataExtent());
                                    this.editor.map.stopFeature(this.stop).popup.panIntoView();
                                    this.editor.map_view.updateStatus("Moved stop to '"+pl.placename+"'...", "good-news");
                                } else {
                                    $(this.edit_form).find('input,textarea,select').removeAttr("disabled");
                                    this.editor.map_view.updateStatus("Could not geocode...", "bad-news");
                                }
                                this.editor.map.controls.select.select(
                                    this.editor.map.stopFeature(this.stop)
                                );
                                this.editor.map.broadcast('supplychain-updated', this.editor.map.supplychains[this.stop.supplychain_id]);
                            }, {"stop": this.ref, "edit_form": f, "editor": this.editor}));
                        } else {
                            this.ref.setAttr(k, val);
                        }
                    }
                    if(this.ref instanceof Sourcemap.Stop) {
                        this.editor.map.mapStop(this.ref, this.ref.supplychain_id);
                        this.editor.map_view.hideDialog();
                        this.editor.map_view.updateStatus("Stop updated...", "good-news");
                    }
                }, {"ref": this.ref, "editor": this.editor}));
            }, {"ref": this.ref}, this);
        }, {"ref": ref, "editor": this}));
    }, this));

    this.map.dockAdd('addstop', {
        "icon_url": "sites/default/assets/images/dock/add.png",
        "callbacks": {
            "click": $.proxy(function() {
                // make a suitable geometry
                var geometry = (new OpenLayers.Format.WKT()).write(
                    new OpenLayers.Feature.Vector(
                        new OpenLayers.Geometry.Point(this.map.map.center.lon, this.map.map.center.lat)
                    )
                );
                attributes = {
                    "title": "New Stop"
                };
                // make a new stop
                var new_stop = new Sourcemap.Stop(
                    geometry, attributes
                );
                // grab the first supplychain
                var sc = false;
                for(var k in this.map.supplychains) {
                    sc = this.map.supplychains[k];
                    break;
                }
                // add a stop to the supplychain object
                sc.addStop(new_stop);
                Sourcemap.Stop.geocode(new_stop);
                // redraw the supplychain
                //this.map.mapSupplychain(sc.instance_id);
                this.map.mapStop(new_stop, sc.instance_id);
                // get the new feature
                var f = this.map.stopFeature(sc.instance_id, new_stop.instance_id)
                // select the new feature
                this.map.controls.select.unselectAll();
                this.map.controls.select.select(f);
            }, this)
        }
    });
    
    // set up drag control
    var stopl = false;
    for(var k in this.map.supplychains) {
        stopl = this.map.getStopLayer(k);
        break;
    }

    var scid = k;

    this.map.addControl('stopdrag', new OpenLayers.Control.DragFeature(stopl, {
        "onStart": $.proxy(function() {
            this.map.controls.select.unselectAll();
        }, this),
        "onComplete": $.proxy(function(ftr, px) {
            ftr.popup.updatePosition();
            this.editor.moveStop(ftr);
        }, {"editor": this})
    }));

    this.map.controls.stopdrag.handlers.drag.stopDown = false;
    this.map.controls.stopdrag.handlers.drag.stopUp = false;
    this.map.controls.stopdrag.handlers.drag.stopClick = false;

    this.map.controls.stopdrag.handlers.feature.stopDown = false;
    this.map.controls.stopdrag.handlers.feature.stopUp = false;
    this.map.controls.stopdrag.handlers.feature.stopClick = false;

    this.map.controls.stopdrag.activate();
}

Sourcemap.Map.Editor.prototype.moveStop = function(ftr) { //todo: rename this
    var scid = ftr.attributes.supplychain_instance_id;
    var stid = ftr.attributes.stop_instance_id;
    var st = this.map.findSupplychain(scid).findStop(stid);
    st.geometry = (new OpenLayers.Format.WKT()).write(ftr);
    var ll = new OpenLayers.LonLat(ftr.geometry.x, ftr.geometry.y)
    ftr.popup.lonlat = ll;
    ftr.popup.updatePosition();
    ll = ll.clone();
    ll.transform(new OpenLayers.Projection('EPSG:900913'), new OpenLayers.Projection('EPSG:4326'));
    this.map_view.updateStatus("Moved stop '"+st.getLabel()+'"..."');
    Sourcemap.Stop.geocode(ll, $.proxy(function(data) {
        if(data && data.results && data.results.length) {
            this.editor.map_view.updateStatus("Updated address...");
            this.stop.setAttr("address", data.results[0].placename);
            Sourcemap.broadcast('supplychain-updated', 
                this.editor.map.findSupplychain(ftr.supplychain_instance_id)
            );
        }
    }, {"stop": st, "editor": this}));
}
