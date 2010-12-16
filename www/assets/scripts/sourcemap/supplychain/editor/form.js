Sorucemap = Sourcemap || {};

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
    this.supplychain = supplychain;
    this.broadcast('supplychainFormEditorInstantiated', this);
}

Sourcemap.FormEditor.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.FormEditor.prototype.init = function() {
    this.supplychain.attributes.test = 'hello';
    Sourcemap.log(this.id);
    $(this.container).attr('id', this.id);
    $(this.container).empty();
    $(this.container).append('<button class="supplychain-serialize">serialize</button>');
    $('button.supplychain-serialize', this.container).click({'editor': this}, function(evt) {
        var editor = evt.data.editor;
        Sourcemap.log(editor.serialize());
    });
    $(this.container).append($('<dl class="attributes"></dl>'));
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
    // todo: hops, attrs
    return serialized;
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
        //'<dt>geometry</dt><dd>'+stop.geometry+'</dd>'+
        '<div class="lonlat"><span class="lon">'+ll.lon+'</span>, <span class="lat">'+ll.lat+'</span></div>'+
        '<dl class="attributes"></dl><button class="stop-geocode">geocode</button>'+
        '<button class="stop-edit">edit</button></div>'
    );
    for(var k in stop.attributes) {
        $('div#'+this.id+'-'+stop.local_id).append(
            $('<dt></dt>').text(k).after($('<dd></dd>').text(stop.attributes[k]))
        );
    }
    $('#'+this.id+'-'+stop.local_id+' button.stop-geocode').click({"latlon": ll, "stop_id": stop.local_id, "editor_id": this.id}, function(evt) {
        var ll = evt.data.latlon;
        var _stelid = evt.data.editor_id+'-'+stop.local_id;
        $(this).val('working...').attr("disabled", true);
        (new google.maps.Geocoder()).geocode({'latLng': new google.maps.LatLng(ll.lat, ll.lon)}, function(results, stat) {
            Sourcemap.log(results);
            var existing = false;
            if((existing = $('#'+_stelid+' dl.attributes:contains("placename")')) && existing.length) {
                $('#'+_stelid+' dl.attributes + dt').text(results[0].formatted_address);
            } else {
                $('#'+_stelid+' dl.attributes').append(
                    $('<dt></dt>').text('placename').after(
                        $('<dt></dt>').text(results[0].formatted_address)
                    )
                );
            }
            //$('#'+_stelid+' button.stop-geocode').remove();
            //$('#'+_stelid+' button.stop-geocode').attr("disabled", false);
        });
        // todo: edit button click()
    });
}
