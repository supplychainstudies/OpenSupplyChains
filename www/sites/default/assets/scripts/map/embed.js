$(document).ready(function() {

    Sourcemap.embed_params.map_element_id = 'sourcemap-map-embed';
    Sourcemap.embed_instance = new Sourcemap.Map.Base(Sourcemap.embed_params);
	console.log(Sourcemap.embed_params);
    Sourcemap.listen("map:supplychain_mapped", function(evt, map, sc) {
        var embed = Sourcemap.embed_instance;
    });

    // get scid from inline script
    var scid = Sourcemap.embed_supplychain_id;

    // fetch supplychain
	$(window).resize(function() {
	  $('#sourcemap-map-embed').css("height", $(window).height()).css("width", $(window).width());
	});
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.embed_instance.map.addSupplychain(sc);
		$(window).resize();
		$("#banner").click(function() {
			window.location.href = "view/" + window.location.pathname.split("/")[2];
		});
    });

});
