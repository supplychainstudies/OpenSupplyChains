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
	switch(Sourcemap.embed_params.served_as) {
		default:
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
            // TODO : make password input window
            var passcode = "";
            if(!Sourcemap.passcode_exist){
                // no passcode
                Sourcemap.loadSupplychain(scid, passcode, function(sc) {
                    Sourcemap.embed_instance.map.addSupplychain(sc);
                    $(window).resize();
                    $("#banner").click(function() {
                        //window.location.href = "view/" + window.location.pathname.split("/")[2];
                        window.open("view/" + window.location.pathname.split("/")[2]);
                    });
                });
            } else {
                var popID = "popup";
                Sourcemap.initPasscodeInput(popID);
                $('form.passcode-input').submit(function(evt){
                    evt.preventDefault();
                    passcode = $('#' + popID).find("input[name='passcode']").val();

                    $('.submit-status').fadeIn();
                    $('#fade , .popup_block').fadeOut(function() {
                        //$('#fade, a.close').remove();
                    });

                    var cb = function(sc){
                        Sourcemap.embed_instance.map.addSupplychain(sc);
                        $(window).resize();
                        $("#banner").click(function() {
                            //window.location.href = "view/" + window.location.pathname.split("/")[2];
                            window.open("view/" + window.location.pathname.split("/")[2]);
                        });
                    };
                    Sourcemap.loadSupplychain(scid, passcode,cb);
                }); // submit end
                if(Sourcemap.passcode==''||Sourcemap.passcode==undefined){
                
                } else {
                    $('#fade , .popup_block').fadeOut(function() {
                    });
                    passcode = Sourcemap.passcode;
                    var cb = function(sc){
                        Sourcemap.embed_instance.map.addSupplychain(sc);
                        $(window).resize();
                        $("#banner").click(function() {
                            window.open("view/" + window.location.pathname.split("/")[2]);
                        });
                    };
                    Sourcemap.loadSupplychain(scid, passcode,cb);
                }
            }
			break;
		case "static":
			var l = "view/" + window.location.pathname.split("/")[2];
			var s = 'static/'+ window.location.pathname.split("/")[2]+'.f.png';
			$('#sourcemap-map-embed').replaceWith(
				"<a class='static-embed' href="+l+"><img src='"+s+"'/></a>"
			);
			break;
		case "earth":
			var id = window.location.pathname.split("/")[2];
			$('#sourcemap-map-embed').addClass("google-earth");
			$('body').prepend(
				"<div class='earth-banner'><a href='view/"+id+"'>View on Sourcemap</a> | <a href='services/supplychains/"+id+"?f=kml'>Download for Google Earth</a></div>"
			);
			$(window).resize(function() {
		      	$('#sourcemap-map-embed').css("height", $(window).height()-40).css("width", $(window).width());
		    });
			$(window).resize();
			google.earth.createInstance('sourcemap-map-embed', earthComplete);
			
			function earthComplete(instance) {
				ge = instance;
				ge.getWindow().setVisibility(true);
				ge.getNavigationControl().setVisibility(ge.VISIBILITY_AUTO);
				
				ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
				ge.getLayerRoot().enableLayerById(ge.LAYER_BUILDINGS, true);
				ge.getLayerRoot().enableLayerById(ge.LAYER_BUILDINGS_LOW_RESOLUTION, true);
				ge.getLayerRoot().enableLayerById(ge.LAYER_ROADS, true);
				ge.getLayerRoot().enableLayerById(ge.LAYER_TERRAIN, true);
				ge.getLayerRoot().enableLayerById(ge.LAYER_TREES, true);
				ge.getSun().setVisibility(true);
				ge.getTime().getControl().setVisibility(ge.VISIBILITY_HIDE);
				
				var lookAt = ge.getView().copyAsLookAt(ge.ALTITUDE_RELATIVE_TO_GROUND);
				lookAt.setRange(lookAt.getRange() * 0.5);
				ge.getView().setAbstractView(lookAt);
				
				var link = ge.createLink('');
				var href = 'http://'+window.location.host+'/services/supplychains/'+window.location.pathname.split("/")[2]+'?f=kml';
				link.setHref(href);

				var networkLink = ge.createNetworkLink('');
				networkLink.set(link, true, true); 
				ge.getFeatures().appendChild(networkLink);
			}			
			break;
	}

});
