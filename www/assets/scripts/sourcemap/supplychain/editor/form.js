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
    this.container.innerHTML = 'hello: '+this.id;
    $(this.container).append('<div id="'+this.id+'-stops"></div>');
    this.stops_container = $('#'+this.id+'-stops').get(0);
    $(this.container).append('<div id="'+this.id+'-hops"><div>');
    this.hops_container = $('#'+this.id+'-hops').get(0);
    for(var i=0; i<this.supplychain.stops.length; i++) {
        this.addStop(this.supplychain.stops[i]);
    }
}

Sourcemap.FormEditor.prototype.addStop = function(stop) {
    var stop = stop instanceof Sourcemap.Stop ? stop : new Sourcemap.Stop(stop.geometry, stop.attributes);
    var f = (new OpenLayers.Format.WKT()).read(stop.geometry);
    var pf = new OpenLayers.Projection('EPSG:900913');
    var pt = new OpenLayers.Projection('EPSG:4326');
    var p = f.geometry.transform(pf, pt);
    var ll = new OpenLayers.LonLat(p.x, p.y);
    $(this.stops_container).append('<div class="formeditor-stop" id="'+this.id+'-'+stop.local_id+'">'+
        '<h3 class="formedit-stop-id">'+stop.local_id+'</h3><dl class="attributes">'+
        '<dt>geometry</dt><dd>'+stop.geometry+'</dd>'+
        '<dt>lonlat</dt><dd>'+ll.lon+', '+ll.lat+'</dd>'+
        '</dl><button class="stop-geocode">geocode</button></div>'
    );
    $('#'+this.id+'-'+stop.local_id+' button.stop-geocode').click({"latlon": ll, "stop_id": stop.local_id, "editor_id": this.id}, function(evt) {
        var ll = evt.data.latlon;
        var _stelid = evt.data.editor_id+'-'+stop.local_id;
        $(this).val('working...').attr("disabled", true);
        (new google.maps.Geocoder()).geocode({'latLng': new google.maps.LatLng(ll.lat, ll.lon)}, function(results, stat) {
            Sourcemap.log(results);
            $('#'+_stelid+' dl.attributes').append('<dt>placename</dt><dd>'+results[0].formatted_address+'</dd>');
            $('#'+_stelid+' button.stop-geocode').remove();
        });
    });
}
