$(document).ready(function() {

    Sourcemap.embed_params.map_element_id = 'sourcemap-map-embed';
    Sourcemap.embed_instance = new Sourcemap.Map.Base(Sourcemap.embed_params);
    Sourcemap.listen("map:supplychain_mapped", function(evt, map, sc) {
        var embed = Sourcemap.embed_instance;
    });

    // get scid from inline script
    var scid = Sourcemap.embed_supplychain_id;

    // fetch supplychain
	$(window).resize(function() {
		if(parseInt($(window).height()) > 480 && parseInt($(window).width()) > 640) {
			$("body").removeClass("zoom");
		}
		else {
			$("body").addClass("zoom");			
		}
	  	$('#sourcemap-map-embed').css("height", $(window).height()).css("width", $(window).width());
		// @todo, throw supplychain:loaded equivalant event on resize and retrigger center
		console.log(Sourcemap);
	});
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.embed_instance.map.addSupplychain(sc);
		$(window).resize();
		$("#banner").click(function() {
			window.location.href = "view/" + window.location.pathname.split("/")[2];
		});
    });

});
