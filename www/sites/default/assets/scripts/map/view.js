//  Normal, non-embedded map view

Sourcemap.Map.View = function(o) {
    this.broadcast('map_view:instantiated', this);
    var o = o || {};
    Sourcemap.Configurable.call(this, o);
    this.instance_id = Sourcemap.instance_id("sourcemap-view");
}

Sourcemap.Map.View.prototype = new Sourcemap.Configurable();

Sourcemap.Map.View.prototype.init = function() {
    this.initMap();
    this.initDialog();
}

Sourcemap.Map.View.prototype.initMap = function() {
    this.map = new Sourcemap.Map(this.options.map_element_id, {
        "tileswitcher": this.options.tileswitcher,
        "prep_stop": $.proxy(function(stop, ftr) {
            var hasmagic = false;
            for(var ski=0; ski<this.magic_word_sequence.length; ski++) {
                var sk = this.magic_word_sequence[ski];
                if(stop.getAttr(sk, false))
                    hasmagic = true;
            }
            if(hasmagic) {
                ftr.attributes.strokeWidth = 2;
                ftr.attributes.strokeColor = "#fff";
            } else {
                ftr.attributes.label = "";
            }
        }, this),
        // callback for decorating hop feature and its arrow
        'prep_hop': function(hop, ftr, arrow) {
            // set arc and related arrow color
            ftr.attributes.color = hop.getAttr('color', '#006633');
            if(arrow) arrow.attributes.color = hop.getAttr('color', '#006633');
        }
    });
    
    var drawControls;

    // Define new layers for edit mode
    var pointLayer = new OpenLayers.Layer.Vector("Point Layer");

    // Define controls for edit mode
    drawControls = {
        point: new OpenLayers.Control.DrawFeature(pointLayer,
            OpenLayers.Handler.Point),
    };

    // Add controls
    for(var key in drawControls) {
        this.map.map.addControl(console.log(drawControls[key]));
    }
}

$(document).ready(function() {
    var scid = supplychain_id;
    Sourcemap.loadSupplychain(scid, function(sc) {
        if(sc.editable){
            Sourcemap.log("Supplychain "+sc.remote_id+" is editable.");
        }
    });

    //Sourcemap.template('map/view/place', function(tpl, txt, loader) { console.log('loaded template: '+tpl); });
});

