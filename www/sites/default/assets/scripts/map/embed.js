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
    	// TODO: throw supplychain:loaded equivalant event on resize and retrigger center

    });
    Sourcemap.loadSupplychain(scid, function(sc) {
        Sourcemap.embed_instance.map.addSupplychain(sc);
    	$(window).resize();
    	$("#banner").click(function() {
    		window.location.href = "view/" + window.location.pathname.split("/")[2];
    	});
    });

});
