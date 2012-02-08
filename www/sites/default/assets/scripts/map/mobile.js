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
    var cb = function(sc){
        Sourcemap.embed_instance.map.addSupplychain(sc);
        $(window).resize();
        $("#banner").click(function(event) {
            event.preventDefault();
            if(event.target.nodeName!="INPUT") {
                if($("body").hasClass("mobile")){return;}
                window.open("view/" + window.location.pathname.split("/")[2]);
            } 
        });
    };
	switch(Sourcemap.embed_params.served_as) {
		default:
		    Sourcemap.embed_params.map_element_id = 'sourcemap-map-mobile';
		    Sourcemap.embed_instance = new Sourcemap.Map.Base(Sourcemap.embed_params);
		    Sourcemap.listen("map:supplychain_mapped", function(evt, map, sc) {
		        var embed = Sourcemap.embed_instance;
                Sourcemap.init_mobile_dialog(sc);
		    });

		    // get scid from inline script
		    var scid = Sourcemap.embed_supplychain_id;

		    // fetch supplychain
		    $(window).resize(function() {
                var window_height = parseInt($(window).height());
                var window_width = parseInt($(window).width());
                //console.log("width:"+window_width+" /height:"+window_height);

                // clearing tags : for test
                var classlist = "zoom mobilezoom vertical horizontal small medium embed";
                $("body").removeClass(classlist);

		    	if(window_height >= 480 && window_width >= 640) {
		    		//$("body").removeClass("mobilezoom");
                    $("body").addClass("embed");
                    $('#sourcemap-map-mobile').css("height", window_height).css("width", window_width);
		    	}
		    	else {
		    		//$("body").addClass("mobilezoom");			
                    // Mobile view
                    var isiOS = (/(iphone|ipod)/.test(navigator.userAgent.toLowerCase()));
                    
                    //if(!isiOS){
                    if(false){
		    		    $("body").addClass("zoom");			
                        $('#sourcemap-map-mobile').css("height", $(window).height()).css("width", $(window).width());

                        if(parseInt($(window).width()) <= 320){
                           $("body").addClass("small");
                        } else {
                           $("body").addClass("medium");
                        }
                    } else {
		    		    $("body").addClass("mobilezoom");			

                        if(parseInt($(window).height()) > parseInt($(window).width())){
                            // vertical
                            $("body").addClass("vertical");			
                            //$('#sourcemap-map-mobile').css("height", 416).css("width", 320);
                            $('#sourcemap-map-mobile').css("height", 208).css("width", 320);
                            $('#sourcemap-dialog-mobile').css("height", 208).css("width", 320);
                        } else {
                            // horizontal
                            $("body").addClass("horizontal");	
                            //$('#sourcemap-map-mobile').css("height", 268).css("width", 480);
                            $('#sourcemap-map-mobile').css("height", 268).css("width", 240);
                            $('#sourcemap-map-mobile').css("float","left");
                            $('#sourcemap-dialog-mobile').css("height", 268).css("width", 240);
                            $('#sourcemap-dialog-mobile').css("float","left");
                        }
                    }
		    	}
                // end styling
		    	// TODO: throw supplychain:loaded equivalant event on resize and retrigger center
                
                // iOS hide address bar 
                setTimeout(function(){
                    if(window.pageYOffset !== 0) return;
                    window.scrollTo(0,window.pageYOffset + 1);
                },100);

                // fix banner size
                // Sourcemap.truncate_one_string("#banner-summary");
                // fix dialog size
                // top 45px
                // dock height + 12px
                //var dialogheight = window_height - 45 - $("#sourcemap-dock").height() - 12; 
                //onsole.log($("#sourcemap-dock"));
                //$("#dialog").css("max-height",dialogheight);
		    });

            // TODO : make password input window
            var passcode = "";
            if(!Sourcemap.passcode_exist){
                // no passcode
                Sourcemap.loadSupplychain(scid, passcode, cb);
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

                    Sourcemap.loadSupplychain(scid, passcode,cb);
                }); // submit end
                if(Sourcemap.passcode==''||Sourcemap.passcode==undefined){
                
                } else {
                    $('#fade , .popup_block').fadeOut(function() {
                    });
                    passcode = Sourcemap.passcode;
                    Sourcemap.loadSupplychain(scid, passcode,cb);
                }
            }
            // end passcode part
			break;
		case "static":
			var l = "view/" + window.location.pathname.split("/")[2];
			var s = 'static/'+ window.location.pathname.split("/")[2]+'.f.png';
			$('#sourcemap-map-mobile').replaceWith(
				"<a class='static-embed' href="+l+"><img src='"+s+"'/></a>"
			);
			break;
		case "earth":
			var id = window.location.pathname.split("/")[2];
			$('#sourcemap-map-mobile').addClass("google-earth");
			$('body').prepend(
				"<div class='earth-banner'><a href='view/"+id+"'>View on Sourcemap</a> | <a href='services/supplychains/"+id+"?f=kml'>Download for Google Earth</a></div>"
			);
			$(window).resize(function() {
		      	$('#sourcemap-map-mobile').css("height", $(window).height()-40).css("width", $(window).width());
		    });
			$(window).resize();
			google.earth.createInstance('sourcemap-map-mobile', earthComplete);
			
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


Sourcemap.init_mobile_dialog = function(sc){
    console.log(sc);
    $("#sourcemap-dialog-mobile").html('<div class="mobile-accordion"></div>');
    var d = new Date(sc.modified*1000);
    var owner_name = sc.owner.display_name!=undefined ? sc.owner.display_name : sc.owner.name;

    var sc_desc = sc.attributes.description;
    var regex = new RegExp(/\[youtube:(.+)\]/);
    var regex_result = sc_desc.match(regex);
    var supplychain_youtube_id = null;
    if(regex_result)
        supplychain_youtube_id = regex_result[1];
    sc_desc = sc_desc.replace(regex,"");
    sc_desc += "<br>"+Sourcemap.MagicWords.content.youtube.makelink(supplychain_youtube_id);
    var map_item = '<h3 class="accordion-title map-title first"><div class="noarrow"></div><div id="title">'+sc.attributes.title+'</div>'+
    '<div id="owner" style="background: url('+sc.owner.avatar+') 10px center no-repeat">'+owner_name+'</a></h3>'+
    '<div class="accordion-body"><div id="dialog-description">'+
    '<div class="dialog-item"><b>Title:</b><br/>'+sc.attributes.title+'</div>'+
    '<div class="dialog-item"><b>Owner:</b><br/><a href="user/'+sc.owner.name+'">'+owner_name+'</a></div>'+
    '<div class="dialog-item"><b>Modified:</b><br/>'+_S.fmt_date(d)+'</div>'+
    '</div></div>'+
    '<h3 class="accordion-title"><div class="arrow"></div>Map Description</h3>'+
    '<div class="accordion-body"><div id="dialog-description">'+sc_desc+'</div></div>';
    $(".mobile-accordion").append(map_item);
    //$(".mobile-accordion").append('<div class=""></div>')
    var stops_total = sc.stops.length;
    for( var i =0 ; i<stops_total ; i++){
        var stop = sc.stops[i];
        var item; 
        if(i==0)
            item = '<h3 class="accordion-title first" id="dialog-'+stop.instance_id+'">';
        else    
            item = '<h3 class="accordion-title" id="dialog-'+stop.instance_id+'">';
        item += '<div class="arrow"></div>'+stop.attributes.title+'</h3>';
        //if(stop.attributes.description!="")
        item += '<div class="accordion-body">'+
        '<div id="dialog-description">'+
        '<h3 class="placename">'+stop.attributes.address+'</h3>'+
        stop.attributes.description+'</div>';
        item+= Sourcemap.MagicWords.content.youtube.mobilelink(stop.attributes["youtube:link"]);
        item += '</div>'; // end accordion-body
        $(".mobile-accordion").append(item);
    }
    $(".mobile-accordion").find('.accordion-body').each(function() {
        $(this).hide();
    });
    $(".mobile-accordion").find('.accordion-title').click(function() {
        Sourcemap.Map.Base.prototype.hideDialog();

        var open = $(this).next().is(":visible");
        $('.accordion-body:visible').each(function() {
            $(this).slideToggle('fast');
        });
        $('.accordion-title').find('.arrow').removeClass('arrowopen');
        if (open == false) {
            $(this).next().slideToggle('fast');
            $(this).find('.arrow').addClass('arrowopen');

            var this_id = $(this).attr("id");
            if(this_id!=undefined){
                // panTo stop id while open the dialog
                var stid = this_id.split("dialog-")[1];
                var map_id = Sourcemap.embed_instance.instance_id;
                var sm_b = Sourcemap.embed_instance;
                var stop = sc.findStop(stid);
                var f = sm_b.map.stopFeature(stop);
                sm_b.map.map.panTo(sm_b.getFeatureLonLat(f));
            }
        }

        // set iphone screen
        setTimeout(function(){
            if(window.pageYOffset !== 0) return;
            window.scrollTo(0,window.pageYOffset + 1);
        },100);

        return false;
    });
    Sourcemap.Map.Base.prototype.showStopDetails = function(stid, scid) {
        this.hideDialog(true);
        var previous_height = $('.accordion-title').find('.arrowopen').parent().next().height();
        var target = "#dialog-"+stid;
        $(target).click();
        var targetOffset = $(target).offset().top;
        var currentOffset = $("#sourcemap-dialog-mobile").scrollTop();
        //var accordian_height = $(target).height();
        console.log(currentOffset);
        console.log(targetOffset);
        console.log(previous_height);
        var sumOffset;
        if(targetOffset>0){
            sumOffset = currentOffset+targetOffset-previous_height; // -64 = normal height
        } else {
            sumOffset = currentOffset+targetOffset;
        }
        if($("body").hasClass("vertical")){
            sumOffset -= 208;
        } 
        //else {    sumOffset = currentOffset+targetOffset; }
        $("#sourcemap-dialog-mobile").animate({scrollTop:sumOffset},200);
        
    }
    /*
    Sourcemap.Map.Base.prototype.showHopDetails = function(stid, scid) {
        console.log("showhop");
        this.hideDialog();
    }
    $(document).unbind("map:feature_selected");
    Sourcemap.listen('map:feature_selected', $.proxy(function(evt, map, ftr) {
        var _Base = _S.Map.Base.prototype;
        console.log(_Base);
        if(ftr.cluster) {
            _Base.showClusterDetails(ftr);
        } else if(ftr.attributes.stop_instance_id && (!(map.editor) || this.options.locked)) {
            _Base.showStopDetails(
            ftr.attributes.stop_instance_id, ftr.attributes.supplychain_instance_id
            );
        } else if (ftr.attributes.hop_instance_id && (!(map.editor) || this.options.locked)) {
            _Base.showHopDetails(
                ftr.attributes.hop_instance_id, ftr.attributes.supplychain_instance_id
            );
        }
    }, this));
    */
} // end init dialog


