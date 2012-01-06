/* Copyright (C) Sourcemap 2014
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

if (!('indexOf' in Array.prototype)) {
    Array.prototype.indexOf= function(find, i /*opt*/) {
        if (i===undefined) i= 0;
        if (i<0) i+= this.length;
        if (i<0) i= 0;
        for (var n= this.length; i<n; i++)
            if (i in this && this[i]===find)
                return i;
        return -1;
    };
}

Sourcemap = {};
_S = Sourcemap;

Sourcemap.ERROR = 1;
Sourcemap.WARNING = 2;
Sourcemap.INFO = 4;

Sourcemap.READ = 1;
Sourcemap.WRITE = 2;
Sourcemap.DELETE = 8;

Sourcemap.options = {
    "log_level": Sourcemap.ERROR | Sourcemap.WARNING// | Sourcemap.INFO
};

Sourcemap.log = function(message, level) {
    var level = typeof level === "undefined" ? Sourcemap.INFO : level;
    if(level & Sourcemap.options.log_level) {
        if(typeof console !== 'undefined' && console && console.log) console.log(message);
    }
    return true;
}

Sourcemap.log('Welcome to Sourcemap.', Sourcemap.INFO);

Sourcemap.deep_clone = function(o) {
    if(typeof o === "object") {
        if(o instanceof Function) {
            var r = function() { return o.apply(this, arguments); };
        } else if(o instanceof Array) {
            var r = [];
            for(var i=0; i<o.length; i++) r[i] = Sourcemap.deep_clone(o[i]);
        } else if(o instanceof Date) {
            var r = new o.constructor(o.getTime());
        } else if(o instanceof RegExp) {
            var r = new o.constructor(o.toString());
        } /*else if(o instanceof HTMLElement) {
            var r = o.cloneNode();
        }*/ else if(o) {
            var r = o.constructor ? new o.constructor() : {};
            for(var k in o) {
                r[k] = Sourcemap.deep_clone(o[k]);
            }
        } else {
            r = o;
        }
    } else {
        var r = o;
    }
    return r;
}

Sourcemap.hash = function(str) {
    var h = 5381;
    for(var i=0; i<str.length; i++) {
        h = ((h << 5) + h) + str.charCodeAt(i);
    }
    return (h & 0x7FFFFFFF);
}

Sourcemap._local_seq = {};
Sourcemap.instance_id = function(seq) {
    var seq = typeof seq === "string" ? seq : new String(seq);
    if(typeof Sourcemap._local_seq[seq] === "undefined") {
        Sourcemap._local_seq[seq] = 0;
    }
    var seq_val = ++Sourcemap._local_seq[seq];
    var id = [seq, seq_val].join("-");
    return id;
}

Sourcemap.Configurable = function(o) {
    var o = typeof o === "undefined" ? {} : o;
    var defaults = this.defaults ? Sourcemap.deep_clone(this.defaults) : {};
    this.options = {};
    for(var k in defaults) {
        if(typeof o[k] !== "undefined") {
            this.options[k] = o[k];
        } else {
            this.options[k] = defaults[k];
        }
    }
    for(var k in o) {
        if(typeof this.options[k] === "undefined") {
            this.options[k] = o[k];
        }
    }
    if(typeof this.init === "function" && this.options.auto_init) {
        this.init();
    }
}

Sourcemap.Configurable.prototype.defaults = {"auto_init": false};

Sourcemap.broadcast = function(evt) {
    var a = []; for(var i=0; i<arguments.length; i++) a.push(arguments[i]);
    var args = a.slice(1);
    $(document).trigger(evt, args);
    Sourcemap.log('Broadcast: '+evt);
}

Sourcemap.listen = function(evts, callback, scope) {
    if(evts instanceof Array)
        evts = evts.join(" ");
    if(callback instanceof Function) {
        if(scope) {
            $(document).bind(evts, $.proxy(callback, scope));
        } else {
            $(document).bind(evts, callback);
        }
    }
    return true;
}

Sourcemap.factory = function(type, data) {
    var instance;
    switch(type) {
        case 'supplychain':
            try {
                Sourcemap.validate(type, data);
            } catch(e) {
                Sourcemap.log(e, Sourcemap.ERROR);
            }
            instance = new Sourcemap.Supplychain();
            var sc = data;
            var stop_ids = {};
            sc.attributes = Sourcemap.deep_clone(sc.attributes);
            for(var i=0; i<sc.stops.length; i++) {
                var new_stop = new Sourcemap.Stop(
                    sc.stops[i].geometry, sc.stops[i].attributes
                );
                stop_ids[sc.stops[i].local_stop_id] = new_stop.instance_id;
                new_stop.local_stop_id = sc.stops[i].local_stop_id;
                instance.addStop(new_stop);
            }
            for(var i=0; i<sc.hops.length; i++) {
                var from_instance = stop_ids[sc.hops[i].from_stop_id];
                var to_instance = stop_ids[sc.hops[i].to_stop_id];
                var new_hop = new Sourcemap.Hop(
                    sc.hops[i].geometry, from_instance, to_instance,
                    sc.hops[i].attributes
                );
                instance.addHop(new_hop);
            }
            instance.owner = data.owner;
            instance.remote_id = sc.id;
            instance.created = sc.created;
            instance.modified = sc.modified;
            instance.attributes = sc.attributes;
            instance.usergroup_perms = sc.usergroup_perms;
            instance.other_perms = sc.other_perms;
            instance.user_featured = sc.user_featured;
            instance.editable = data.editable;
            break;
        case 'tree':
            instance = new Sourcemap.Supplychain();
            var sc = data;
            var stop_ids = {};
            sc.attributes = Sourcemap.deep_clone(sc.attributes);
            //stops
            for(var i=0; i<sc.stops.length; i++) {
                var new_stop = new Sourcemap.Stop(
                    sc.stops[i].geometry, sc.stops[i].attributes
                );
                stop_ids[sc.stops[i].local_stop_id] = new_stop.instance_id;
                new_stop.local_stop_id = sc.stops[i].local_stop_id;
                instance.addStop(new_stop);
            }
            //hops
            for(var i=0; i<sc.hops.length; i++) {
                var from_instance = stop_ids[sc.hops[i].from_stop_id];
                var to_instance = stop_ids[sc.hops[i].to_stop_id];
                var new_hop = new Sourcemap.Hop(
                    sc.hops[i].geometry, from_instance, to_instance,
                    sc.hops[i].attributes
                );
                instance.addHop(new_hop);
            }
            instance.owner = data.owner;
            instance.remote_id = sc.id;
            instance.created = sc.created;
            instance.modified = sc.modified;
            instance.attributes = sc.attributes;
            instance.usergroup_perms = sc.usergroup_perms;
            instance.other_perms = sc.other_perms;
            instance.user_featured = sc.user_featured;
            instance.editable = data.editable;
            // make instance tree
            var g = new Sourcemap.Supplychain.Graph2(instance);
            var stids = g.nids.slice(0);
			var max_plen = 0;
			var upperbound = 0;
			
			//Just for colors, which should match the map
			var stop_colors = {};
            for(var i=0; i<stids.length; i++) {
                stop_colors[stids[i]] = 0;
            }
            for(var i=0; i<g.paths.length; i++) {
                var p = g.paths[i];
                max_plen = p.length > max_plen ? p.length : max_plen;
                for(var j=0; j<p.length; j++) {
                    if(j > stop_colors[p[j]]) stop_colors[p[j]] = j;
                }
            }
			var max_plen = 0;
			
			// If there are predefined tiers, use those
			// But they have to shifted, because they go from + to - tiers (since the middle product tier will be 0)
			if (sc.stops[0].attributes.tier) {
				var tiers = {};	
				var offset = 0;			
	            for(var i=0; i<stids.length; i++) {
					if (!isNaN(sc.stops[i].attributes.tier)) {		
						// store the tier
		                tiers[stids[i]] = parseInt(sc.stops[i].attributes.tier);
						// store the highest tier in the whole stack
						upperbound = Math.max(upperbound,parseInt(sc.stops[i].attributes.tier));
					}
	            }
				// Shift them all over so that everything is greater than 0
				for(x in tiers) {
	                tiers[x] = upperbound-tiers[x];
					max_plen = Math.max(max_plen,parseInt(tiers[x]));
	            }
				max_plen++;		
			} else {
				// If tiers haven't been preset, create some
	            var tiers = {};
	            for(var i=0; i<stids.length; i++) {
	                tiers[stids[i]] = 0;
	            }
	            for(var i=0; i<g.paths.length; i++) {
	                var p = g.paths[i];
	                max_plen = p.length > max_plen ? p.length : max_plen;
	                for(var j=0; j<p.length; j++) {
	                    if(j > tiers[p[j]]) tiers[p[j]] = j;
	                }
	            }
			}
            //default_feature_colors
            var dfc = ["#35a297", "#b01560", "#e2a919"].slice(0);
            //var dfc = this.options.default_feature_colors.slice(0);
            for(var i=0; i<dfc.length; i++) {
                    dfc[i] = (new Sourcemap.Color()).fromHex(dfc[i]);
            }
            var palette = Sourcemap.Color.graduate(dfc, max_plen || 1);
            for(var i=0,length=instance.stops.length;i<length;i++)
            {
				
                var st = instance.stops[i];
                //var scolor = st.getAttr("color", palette[tiers[st.instance_id]].toString());
				
				var scolor = st.getAttr("color", palette[stop_colors[st.instance_id]].toString());
                st.attributes.tier = tiers[st.instance_id];
                st.attributes.color = scolor;
            }

            instance.tiers = tiers;
            instance.max_plen = max_plen;

            break;
        default:
            instance = false;
            break;
    }
    return instance;
}

Sourcemap.validate = function(type, data) {
    switch(type) {
        case 'supplychain':
            var sc = data;
            if(!(sc.stops instanceof Array))
                throw new Error('Stops array missing or invalid.');
            if(!(sc.hops instanceof Array))
                throw new Error('Hops array missing or invalid.');
            var stop_ids = [];
            for(var i=0; i<sc.stops.length; i++) {
                Sourcemap.validate('stop', sc.stops[i]);
                stop_ids.push(sc.stops[i].id);
            }
            for(var i=0; i<sc.hops.length; i++) {
                Sourcemap.validate('hop', sc.hops[i]);
                if(stop_ids.indexOf(sc.hops[i].from_stop_id) < 0)
                    throw new Error('From stop in hop is invalid.');
                if(stop_ids.indexOf(sc.hops[i].to_stop_id) < 0)
                    throw new Error('To stop in hop is invalid.');
            }
            if(!(sc.attributes instanceof Object)) {
                throw new Error('Missing or invalid attributes property.');
            }
            break;
        case 'stop':
            var stop = data;
            if(!(stop.attributes instanceof Object))
                throw new Error('Stop missing attributes object.');
            if(!stop.geometry)
                throw new Error('Stop missing geometry.');
            var parser = new OpenLayers.Format.WKT();
            var parsed = parser.read(stop.geometry)
            if(!parsed || !(parsed instanceof OpenLayers.Feature.Vector)) {
                throw new Error('Invalid geometry.');
            }
            break;
        case 'hop':
            var hop = data;
            if(!(hop.attributes instanceof Object))
                throw new Error('Hop missing attributes object.');
            if(!hop.geometry)
                throw new Error('Hop missing geometry.');
            var parser = new OpenLayers.Format.WKT();
            var parsed = parser.read(hop.geometry)
            if(!parsed || !(parsed instanceof OpenLayers.Feature.Vector)) {
                throw new Error('Invalid geometry.');
            }
            break;
        default:
            throw new Error('validation not implemented: '+type);
            break;
    }
    return false;
}

Sourcemap.initPasscodeInput = function(popID){
    var element = document.createElement('div');
    $(element).html(
        '<div id="passcode-input">'+
        '<form class="passcode-input">'+
        '<label id="passcode-msg" for="passcode"> This map is protected. Please enter the password:</label>'+
        '<input name="passcode" type="text" autocomplete="off"></input>'+
        '<input id="passcode-submit" type="submit"/>'+
        '</form>'
        +'</div>'
    );
    $(element).attr('id',popID);
    $(element).addClass("popup_block");
    $(element).prepend('<a href="#" class="close"></a>');
    $('body').append($(element));

    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();

    // Error behavior
    var onError = function(){ window.location = "/view/" + scid + "?private"; }

    
    // CSS setting of pop up window
    $('#' + popID).height(110);
    $('#' + popID).width(($('body').width()>750)?600:$('body').width()*.8);
    var popMargTop = ($('#' + popID).height() + 80) / 2;
    var popMargLeft = ($('#' + popID).width() + 80) / 2;

    $('#' + popID).css({
        'margin-top' : -popMargTop,
        'margin-left' : -popMargLeft,
        'overflow' : 'hidden'
    });     

    var loading = document.createElement('div');
    $(loading).attr('class','submit-status');
    $(loading).css({'display':'none'});
    $('body').append($(loading));


    $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
    $('#fade').css({'filter' : 'alpha(opacity=80)'});
    $('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
        $('#fade , .popup_block').fadeOut(function() {
            //$('#fade, a.close').remove();
        }); //fade them both out
        return false;
    });

    if(Sourcemap.passcode==''||Sourcemap.passcode==undefined){
        $('#' + popID).fadeIn();
        $('#fade').fadeIn(); //Fade in the fade layer 
        //Autofocus on password  
        $('#'+popID).find("input[name='passcode']").focus(); 
    } else {
        $(loading).fadeIn();
    }

}

Sourcemap.loadSupplychain = function(remote_id, passcode, callback) {
    // fetch and initialize supplychain
    var _that = this;
    var _remote_id = remote_id;
    $.ajax({
        url:'services/supplychains/'+remote_id,
        data:{ passcode : passcode },
        success : function(data) {
            var sc = Sourcemap.factory('supplychain', data.supplychain);
            sc.editable = data.editable;
            callback.apply(this, [sc]);
            _that.broadcast('supplychain:loaded', this, sc);
            // unlock the supply chain
            // disable loading icon
            $('.submit-status').fadeOut();
        },
        error : function(data){
            //console.log("Passcode incorrect");
            var error_response = eval('('+data.response+')');
            $('#popup').fadeIn();
            $('#passcode-msg').html(error_response.error+" Please enter passcode again:");
            $('.passcode-input').find("input[name='passcode']").focus();
            $("#fade").fadeIn();
            $('.submit-status').fadeOut();
        }
    });
}

Sourcemap.loadSupplychainToTree = function(remote_id, passcode, callback) {
    // fetch and initialize supplychain
    var _that = this;
    var _remote_id = remote_id;

    $.ajax({
        url:'services/supplychains/'+remote_id,
        data:{ passcode : passcode },
        success : function(data) {
            var sc = Sourcemap.factory('tree', data.supplychain);
            sc.editable = data.editable;
            callback.apply(this, [sc]);
            $('.submit-status').fadeOut();
        },
        error : function(data){
            $('.submit-status').fadeOut();
            var error_response = eval('('+data.response+')');
            $('#popup').fadeIn();
            $('#passcode-msg').html(error_response.error+" Please enter passcode again:");
            $('.passcode-input').find("input[name='passcode']").focus();
            $("#fade").fadeIn();
        }
    });
}

Sourcemap.buildTree = function(tree_id,sc) {
    var tiers = [],tier_list = [],hop_list = [];
    var max_plen = sc.max_plen;
    var max_length=(max_plen)?sc.max_plen:1,max_x=0,max_y=0;    
    // palette stuff

    var dfc = ["#35a297", "#b01560", "#e2a919"].slice(0);
    for(var i=0; i<dfc.length; i++) {
        dfc[i] = (new Sourcemap.Color()).fromHex(dfc[i]);
    }
    var palette = Sourcemap.Color.graduate(dfc, max_plen || 1);
    //tier for palette

    var g = new Sourcemap.Supplychain.Graph2(sc);
    var stids = g.nids.slice(0);
    var p_tiers = {};
    for(var i=0; i<stids.length; i++) {
        p_tiers[stids[i]] = 0;
    }
    for(var i=0; i<g.paths.length; i++) {
        var p = g.paths[i];
        for(var j=0; j<p.length; j++) {
            if(j > p_tiers[p[j]]) p_tiers[p[j]] = j;
        }
    }
    // Tiers for tree
    for(var i=0;i<max_length;i++)
    {   tiers[i] = new Array();  }
    // Create stop points
    for(var i=0,length=sc.stops.length;i<length;i++)
    {
	        tiers[sc.tiers[sc.stops[i].instance_id]].push(sc.stops[i]);

	        // default status
			var letitle  = sc.stops[i].attributes.title;
			if (letitle == undefined)
				letitle = sc.stops[i].attributes.location;		
			//	letitle = sc.stops[i].attributes.name;

	        if(sc.stops[i].attributes.size == undefined)
	            size = 2;
	        else
	            size = sc.stops[i].attributes.size;

	        tier_list[i] = { 
	            title:letitle,
	            index:i,
	            tiers:sc.tiers[sc.stops[i].instance_id],
	            instance:sc.stops[i].instance_id,
	            //y:(tiers[sc.tiers[sc.stops[i].instance_id]].length-1)*80+300,
	            //x:sc.tiers[sc.stops[i].instance_id]*150+100,
				size:size,
	            color:sc.stops[i].attributes.color
	        }
		//}
    }
    var max_height =  $(tree_id).height();
    var max_width = $(tree_id).width();


    // connctions of each points
    for(var i=0, length=sc.hops.length;i<length;i++)
    {
        var h = sc.hops[i];
        // for tier_list
		for (var j = 0; j< tier_list.length; j++) {
			if (h.from_stop_id == tier_list[j].instance||h.to_stop_id == tier_list[j].instance) {
                (tier_list[j].connections)?tier_list[j].connections++:tier_list[j].connections=1;                
            }
            // All points that don't have connections are set to zero
            (tier_list[j].connections)?0:tier_list[j].connections=0;
		}
        // for tiers
        for (var k =0;k<tiers.length;k++){
            for(var j=0;j<tiers[k].length;j++){
                if(h.from_stop_id == tiers[k][j].instance_id || h.to_stop_id == tiers[k][j].instance_id){ 
                    (tiers[k][j].connections) ? tiers[k][j].connections++ : tiers[k][j].connections=1;
                }
                // All points that don't have connections are set to zero
                (tiers[k][j].connections)? 0:tiers[k][j].connections=0;
            }
        }
    }
    
    // Sort #0 : Squeeze the tier together
    //var temp_tiers = tiers.slice(0);    // copy array ( not deep
    //var newObject = jQuery.extend(true, {}, oldObject); // clone object
    var temp_tiers = $.extend(true,[],tiers);
    var original_tiers = $.extend(true,[],tiers);
    var onchange = 0;
    var length = tiers.length-1; // last tiers should skip
    for(var i=0;i<length;i++){
        var length_of_tiers_i = tiers[i].length;
        for(var j=0;j<length_of_tiers_i;j++){
            // Scan every point in tiers from tiers 0 to end
            var target = tiers[i][j];
            console.log("-->"+target.attributes.title);
            var children_list = [];
            var tier_difference = [];
            (function(){
                // scan hops find how many target connect target 
                // get children list
                for(var k=0,length_hop=sc.hops.length;k<length_hop;k++){
                    var h = sc.hops[k];
                    if(h.from_stop_id!=target.instance_id)
                        continue;
                    (function(){    
                    var length_of_inner = tiers.length;
                    for(var m=0;m<length_of_inner;m++){
                        var length_of_inner_m = tiers[m].length;
                        for(var n=0;n<length_of_inner_m;n++){
                            var target_child = tiers[m][n];
                            if(h.to_stop_id!=target_child.instance_id)
                                continue;
                            children_list.push(target_child);
                        }
                    }
                    })(); 
                }// end of finding all children of each stop

                // calculate item[id1].tiers - target tiers one of them should be 1 
                var waiting_list = [];
                for(var counter=0;children_list.length>0;counter++){
                    var item = children_list.shift(); 
                    var target_pos,target_tier,item_pos,item_tier;
                    if(item.instance_id=="stop-21") // 6/ 21
                        console.log(target);
                    for(var m=0;m<temp_tiers.length;m++){
                        if(jQuery.inArray(target,temp_tiers[m])>=0){
                            target_pos = jQuery.inArray(target,temp_tiers[m]);
                            target_tier = m;
                            continue;
                        }
                        if(jQuery.inArray(item,temp_tiers[m])>=0){
                            item_pos = jQuery.inArray(item,temp_tiers[m]);
                            item_tier = m;
                            break;
                        }
                    } // get both pos and tier
                    tier_difference.push(item_tier-target_tier);
                    // put in waiting list
                    if(item_tier-target_tier>1){
                        waiting_list.push(item);
                    }
                } // end all children_list
                console.log(tier_difference);
                // if nothing in tier_difference
                if(tier_difference.length<1){
                    return;
                }
                // if one of them 1 return;
                if($.inArray(1,tier_difference)>=0){
                    return;
                } else {
                    // if all of them > 1 (including one item)
                    // move the target tier to right position in temp
                    // Sort it and do it once
                    var position = 0;
                    if(tier_difference.length!=1){
                        var lowest = Math.min.apply(null,tier_difference);
                        position = tier_difference.indexOf(lowest);
                        console.log("--- Smallest at "+position+" ---");
                    }
                    //tiers[k].sort(function(a,b){return a.connections - b.connections;});
                    
                    var item = waiting_list[position]; 
                    var target_pos,target_tier,item_pos,item_tier;
                    for(var m=0;m<temp_tiers.length;m++){
                        if(jQuery.inArray(target,temp_tiers[m])>=0){
                            target_pos = jQuery.inArray(target,temp_tiers[m]);
                            target_tier = m;
                            continue;
                        }
                        if(jQuery.inArray(item,temp_tiers[m])>=0){
                            item_pos = jQuery.inArray(item,temp_tiers[m]);
                            item_tier = m;
                            break;
                        }
                    } // get both pos and tier
                    if(item_tier-target_tier>1){
                        console.log("** Work **");
                        console.log("target:"+target_pos+","+target_tier);
                        console.log(target.attributes.title);
                        console.log("item:"+item_pos+","+item_tier);
                        console.log(item.attributes.title);
                        var temp = temp_tiers[target_tier][target_pos];
                        temp_tiers[target_tier].splice(target_pos,1);
                        temp_tiers[item_tier-1].push(temp);

                    } // else undefine or ==1
                    onchange = 1;
                }
                // rescan the list if something change
                return;
            })(); // end of tier[i][j]
            if (onchange>0){
                i=-1; // next for loop : i=0,j=0
                j=-1;
                onchange = 0;
                break;
            }
        } // end of tiers[i]
    } // end of tiers
    // copy the map into 
    tiers = temp_tiers;

    // Sort #1 : Stop with largest connections in mid;
    for(var k=0;k<tiers.length;k++){
        tiers[k].sort(function(a,b){return a.connections - b.connections;});
        // sort tiers[k]
        var new_arr = [];
        for(var bool=0,y=0,z=0;y<tiers[k].length;y++){
            if(bool){
                bool=0;
                new_arr.splice(z,0,tiers[k][y]); 
            } else {
                new_arr.splice(new_arr.length-z,0,tiers[k][y]); 
                bool=1;
                z+=1;
            }  
        }
        tiers[k] = new_arr;
    }
    // End Sort #1

    //console.log(sc.hops);
    // Sort #2 : stops with only 1 connection should be clustered and move order
    (function(){
        return;     //disable, jump to sort #3
    var finish_stop_id_list =[];
    for(var j=0;j<tiers.length;j++){
        // move order -> cluster
        // Move order close to its end
        for(var k=0;k<tiers[j].length;k++){
            if(tiers[j][k].connections==1){
                // with only single connection (end or start)
                //console.log(j+","+k);
                //console.log(tiers[j][k]);
                var child_stop_id = "";
                var parent_stop_id = "";
                for(var l=0;l<sc.hops.length;l++){
                    if(sc.hops[l].from_stop_id==tiers[j][k].instance_id){
                        child_stop_id = sc.hops[l].to_stop_id;
                        parent_stop_id = sc.hops[l].from_stop_id;
                        console.log(tiers[j][k].attributes.title);
                        continue;
                    }
                }
                if(child_stop_id =="")
                    continue;   // means its the end of the stop
                else if(jQuery.inArray(parent_stop_id,finish_stop_id_list)>=0)
                    continue;
                else{    
                    console.log(child_stop_id); 
                    //console.log(tiers[j][k]);
                    (function(){
                    for(var m=0;m<tiers.length;m++){
                        for(var n=0;n<tiers[m].length;n++){
                            if(tiers[m][n].instance_id == child_stop_id){
                                //if(tiers[m].length==1){
                                //    return;
                                //}else{
                                    // Do the order changing stuff
                                    var new_position,pos;
                                    var temp_item;
                                    var temp_item = tiers[j][k];
                                    pos = (tiers[m].length==1) ? (k/(tiers[j].length-1)) : (n/(tiers[m].length-1));

                                    if(pos>.5)
                                        new_position = tiers[j].length;
                                    else if(pos==.5)    
                                        new_position = parseInt(tiers[j].length/2);
                                    else
                                        new_position = 0;
    
                                    var attr = tiers[j][k].attributes;
                                    console.log(pos+":"+attr.title+" / to ("+new_position+")   Ins:"+tiers[j][k].instance_id);

                                    tiers[j].splice(k,1);
                                    tiers[j].splice(new_position,0,temp_item);

                                    finish_stop_id_list.push(parent_stop_id);
                                    k--; //
                                    return;
                                //}
                            }                                
                        }
                    }
                    })(); // end of function
                }
            }
        }
    }
    })();
    // End Sort #2

    // Sort #3 : stops with only 1 connection should be clustered and move order
    (function(){
    var finish_stop_id_list =[];
    var parent_stop_id_list =[];
    var children_stop_id_list =[];
    for(var j=0;j<tiers.length;j++){
        // move order -> cluster
        // Move order close to its end
        var len_of_tier = tiers[j].length;
        var default_mid,target_value;
        if((len_of_tier/2)==parseInt(len_of_tier/2))
            default_mid = (len_of_tier/2)-1;
        else
            default_mid = (len_of_tier-1)/2;
        for(var bool=0,v=0,k=0;k<len_of_tier;k++){
            if(bool==0){
                target_value = default_mid-v;
                v+=1;
                bool = 1;
            } else {
                target_value = default_mid+v;
                bool = 0;
            }
            //console.log(target_value);
            var target_stop_id = ""
            var child_stop_id = "",parent_stop_id = "";
            parent_stop_id_list = [];
            children_stop_id_list = [];
            for(var l=0;l<sc.hops.length;l++){
                //if(sc.hops[l].to_stop_id==tiers[j][k].instance_id){
                if(sc.hops[l].to_stop_id==tiers[j][target_value].instance_id){
                    // If Parent is 1
                    target_stop_id = sc.hops[l].to_stop_id;
                    for(var z=0;z<tier_list.length;z++)
                    {
                        if(sc.hops[l].from_stop_id==tier_list[z].instance){
                            if(tier_list[z].connections==1){
                            //if(tier_list[z].connections<=3){
                                parent_stop_id = sc.hops[l].from_stop_id;
                                parent_stop_id_list.push(parent_stop_id);
                            }
                            continue;
                        }
                    }
                } 
                if (sc.hops[l].from_stop_id==tiers[j][target_value].instance_id){
                    // If child is 1
                    target_stop_id = sc.hops[l].from_stop_id;
                    for(var z=0;z<tier_list.length;z++)
                    {
                        if(sc.hops[l].to_stop_id==tier_list[z].instance){
                            if(tier_list[z].connections==1){
                            //if(tier_list[z].connections<=3){
                                child_stop_id = sc.hops[l].to_stop_id;
                                children_stop_id_list.push(child_stop_id);
                            }
                            continue;
                        }
                    }
                }
            }
            //if(parent_stop_id_list.length<1||target_stop_id=="")
            if(target_stop_id=="")
                continue;
            else{
                // Parent order function
                (function(){
                    var target;
                    for(var q=0,q_len=parent_stop_id_list.length;q<q_len;q++){
                        if(parent_stop_id_list.length<1)
                            return; // nothing inside list
                        target = parent_stop_id_list.pop();
                        for(var m=0;m<tiers.length;m++){
                            for(var n=0;n<tiers[m].length;n++){
                                if(tiers[m][n].instance_id!=target)
                                    continue;
                                var new_position,pos;
                                var temp_item;
                                var temp_item = tiers[m][n];
                                //pos = (tiers[j].length==1) ? (n/(tiers[m].length-1)) : (k/(tiers[j].length-1));
                                pos = (tiers[j].length==1) ? (n/(tiers[m].length-1)) : (target_value/(tiers[j].length-1));
                                if(pos>.5)
                                    new_position = tiers[m].length-1;
                                else if(pos==.5)    
                                    new_position = parseInt(tiers[m].length/2);
                                else
                                    new_position = 0;
                                //var attr = tiers[m][n].attributes;
                                //console.log(pos+":"+attr.title+" / to ("+new_position+")   Ins:"+tiers[j][k].instance_id);
                                tiers[m].splice(n,1);
                                tiers[m].splice(new_position,0,temp_item);
                                continue;
                            }
                        }
                    }
                })(); // end parent order function
                //console.log(children_stop_id_list);
                // children order function
                (function(){
                    var target;
                    for(var q=0,q_len=children_stop_id_list.length;q<q_len;q++){
                        target = children_stop_id_list.pop();
                        for(var m=0;m<tiers.length;m++){
                            for(var n=0;n<tiers[m].length;n++){
                                if(tiers[m][n].instance_id!=target)
                                    continue;
                                var new_position,pos;
                                var temp_item;
                                var temp_item = tiers[m][n];
                                //pos = (tiers[j].length==1) ? (n/(tiers[m].length-1)) : (k/(tiers[j].length-1));
                                pos = (tiers[j].length==1) ? (n/(tiers[m].length-1)) : (target_value/(tiers[j].length-1));
                                if(pos>.5)
                                    new_position = tiers[m].length;
                                else if(pos==.5)    
                                    new_position = parseInt(tiers[m].length/2);
                                else
                                    new_position = 0;
                                //var attr = tiers[m][n].attributes;
                                //console.log(pos+":"+attr.title+" / to ("+new_position+")   Ins:"+tiers[j][k].instance_id);
                                tiers[m].splice(n,1);
                                tiers[m].splice(new_position,0,temp_item);
                                continue;
                            }
                        }
                    }
                })(); // end parent order function
            }//end else
            continue;
        }
    }
    })();
    // End Sort #3
    
    // Assign position for each point
    for(var i=0,order=0;i<tiers.length;i++)
    {
		var y_offset = ((i*2.5)%5)*5;
		//console.log(i+":"+y_offset);
        for(var j=0;j<tiers[i].length;j++){ 
            for(var k=0,tier_list_length=tier_list.length;k<tier_list_length;k++){
                if(tier_list[k].instance==tiers[i][j].instance_id){
                    //tier_list[k].y = (j+1)*(max_height)/(tiers[i].length+1);
					/*
					tier_list[k].y = ((500-(tiers[i].length*40))/2)+(tier_list[k].order)*40;
					
					if (parseInt(tier_list[k].order/2) == (tier_list[k].order/2)) {
						tier_list[k].y = y_offset + ((500-(tiers[i].length*40))/2)+ (((tier_list[k].order/2))*40); 
						//console.log("-"(tier_list[k].order/2));
					} else {
						tier_list[k].y = y_offset + (500-((500-(tiers[i].length*40))/2)) - ((Math.ceil(tier_list[k].order/2)-1)*40);
						//console.log(tier_list[k].y);
					} 
					*/
					var yspacing = 40;
					var xoffset = 0;
					if (tiers[i].length > 14) {
						yspacing = 30;
						/*
						if (parseInt(j/2) == j/2) {
						// Even - move it left
							xoffset = 20;				
						} else {
							xoffset = -20;
						}
						*/
					}
					tier_list[k].y = ((max_height-(tiers[i].length*yspacing))/2)+(j+1)*yspacing;
                    //tier_list[k].y = (j+1)*(max_height)/(tiers[i].length+1);
                    tier_list[k].x = ((i+1)*(max_width)/(tiers.length+1))+xoffset;
                    break;
                }
            }            
        }
    }
    // Set stop points for Arc diagram    
    /*
    for(var i=0,order=0;i<tiers.length;i++)
    {
        for(var j=0;j<tiers[i].length;j++){            
            for(var k=0,tier_list_length=tier_list.length;k<tier_list_length;k++){
                if(tier_list[k].instance==tiers[i][j].instance_id){
                    tier_list[k].y = 430;
                    tier_list[k].x = order*105+50;
                    order+=1;
                    break;
                }                                
            }            
        }
    }
    */

    // Create hop points
    for(var i=0, length=sc.hops.length;i<length;i++)
    {
        var h = sc.hops[i];
        var fc = palette[p_tiers[h.from_stop_id]];
        var tc = palette[p_tiers[h.to_stop_id]];
        var hc = h.getAttr("color", fc.midpoint(tc).toString());
        hop_list[i] = {            
            id:h.instance_id,
            from:h.from_stop_id,
            to:h.to_stop_id,
            //id:"hop"+i,
            x1:tier_list[sc.hops[i].from_local_stop_id-1].x,
            x2:tier_list[sc.hops[i].to_local_stop_id-1].x,
            y1:tier_list[sc.hops[i].from_local_stop_id-1].y,
            y2:tier_list[sc.hops[i].to_local_stop_id-1].y,
            color:hc
        }
    }

    var w = $(tree_id).width(),
    h = $(tree_id).height();
    //x = d3.scale.ordinal().domain(tier_x).rangePoints([0, w], 1),
    //y = d3.scale.ordinal().domain(tier_y).rangePoints([0, h], 3);    
    var svg = d3.select(tree_id).append("svg:svg")
        .attr("width", w)
        .attr("height", h);

    //def marker // TODO:make it work

    svg.append("svg:defs").selectAll("marker")
        .data(hop_list)
    .enter().append("svg:marker")
        .attr("id", function(d){return d.id;})
        .attr("viewBox", "0 0 10 10")
        .attr("refX", 0)
        .attr("refY", 0)
        .attr("markerWidth", 6)
        .attr("markerHeight", 6)
        .attr("orient", "auto")
        .attr("stroke-width",2)
    .append("svg:polyline")
        .attr("points","0,0 10,5 0,10 1,5")
        .attr("fill",function(d){return d.color;})
    //.append("svg:path")
    //    .attr("d", "M0,-5 L10,0 L0,5");

    // Arc
    /*
    svg.append("svg:g")
    .selectAll("path")
    .data(hop_list)
    .enter().append("svg:path")    
    .style("fill","none")
    .style("stroke",function(d){return d.color})
    //.attr("d","M20,400 l 90,-25 a25,25 -30 0,1 50,-25 l 50,-25 a25,50 -30 0,1 50,-25 l 50,-25 a25,75 -30 0,1 50,-25 l 50,-25 a25,100 -30 0,1 50,-25 l 50,-25");
    .attr("d",function(d){
        var diff_x = d.x2-d.x1
        var diff_y = d.y2-d.y1
        return "M "+d.x1+","+d.y1+" a45,50 0 0,1 "+diff_x+","+diff_y});
    */
    // Simple line    

    svg.append("svg:g").attr("class","line").selectAll("line")
        .data(hop_list)
        .enter().append("svg:line")
            .attr("x1",function(d){return d.x1})
            .attr("x2",function(d){return d.x2})
            .attr("y1",function(d){return d.y1})
            .attr("y2",function(d){return d.y2})
            .attr("stroke-width",2)
            .attr("marker-end",function(d){ return "url(#"+d.id+")";})
            //.on("click",function(d){alert(d.from+" to "+d.to);})
            .attr("stroke",function(d){return d.color});

	svg.append("svg:g").attr("class","arrow").selectAll("arrow").data(hop_list).enter()
	.append("svg:polygon") 
		.attr("points", function (d) { return parseInt(((d.x1+d.x2)/2)-5)+ ","  + parseInt(((d.y1+d.y2)/2)+5) + " " + parseInt((d.x1+d.x2)/2) + " , " + parseInt(((d.y1+d.y2)/2)+3) + " " + parseInt(((d.x1+d.x2)/2)+5) + " , " + parseInt(((d.y1+d.y2)/2)+5) + " " + parseInt((d.x1+d.x2)/2) + " , "+ parseInt(((d.y1+d.y2)/2)-5) + " "+ parseInt(((d.x1+d.x2)/2)-5) + " ," + parseInt(((d.y1+d.y2)/2)+5);}) 
		.attr("transform", function(d){ //console.log((Math.atan((d.y2-d.y1)/(d.x2-d.x1))*57.2957795)); 
                return "rotate("+(((Math.atan((d.y2-d.y1)/(d.x2-d.x1))*57.2957795)+90)+ " " + ((d.x1+d.x2)/2) + " " + ((d.y1+d.y2)/2))+")";}) 
		.style("fill", function(d){return d.color}) .attr("width", "10px") .attr("height", "10px");
   
    /*
    // path > line
    var path = svg.append("svg:g").selectAll("path")
    .data(hop_list)
    .enter().append("svg:path")    
        .style("fill",function(d){ return d.color})
        .style("stroke",function(d){return d.color})
        .attr("stroke-width", "3px")
        .attr("marker-end",function(d){ 
            return "url(#"+d.id+")";})        
        .attr("d",function(d){
            var diff_x = d.x2-d.x1
            var diff_y = d.y2-d.y1
            return "M "+d.x1+","+d.y1+"  l"+diff_x+","+diff_y});
    */
    
    svg.append("svg:g").attr("class","stop_title").selectAll("text")
    .data(tier_list)
    .enter().append("svg:text")
    .attr("x",function(d){return d.x})
    .attr("y",function(d){return d.y})
    .attr("dx",".1em") // padding
    .attr("dy","1.8em")
    .attr("text-anchor","middle")
	.style("fill",function(d){return d.color})
	.style("font-size","12px")
	.style("font-weight", "bold")
    .text(function(d){return d.title});
 
    svg.append("svg:g").attr("class","circle").selectAll("circle")
        .data(tier_list)
        .enter().append("svg:circle")
        .attr("class", "little")
        .attr("cx", function(d){return d.x})
        .attr("cy", function(d){return d.y})
        .attr("opacity",1)
        .on("mouseover",hover_circle(.1))
        .on("mouseout",hover_circle(1))
        .on("click",function(d){console.log(d.instance);})
        .style("fill", function(d){return d.color})
        .attr("r", "8");
    
    function hover_circle(opacity){
        return function(g,i){
            svg.selectAll("g.circle circle")
            .filter(function(d){
                    update_updown(i);
                    return check_stops(d.index,i);                        
                })
            .transition()
                .style("opacity",opacity);

            svg.selectAll("g.stop_title text")
            .filter(function(d){
                    return check_stops(d.index,i);                        
                })
            .transition()
                .style("opacity",opacity);
           // Hide Arrow
           svg.selectAll("g.arrow polygon")
           .filter(function(d){return check_hops(d,i);})
           .transition()
                .style("opacity",opacity);

           // Hide line
           svg.selectAll("g.line line")
           .filter(function(d){return check_hops(d,i);})
           .transition()
                .style("opacity",opacity);
       }
    }

    var upstream = [];
    var downstream = [];
    function update_updown(select)
    {        
        upstream = [];
        downstream = [];
        upstream.push(tier_list[select].instance);
        downstream.push(tier_list[select].instance);
        //downstream ~max
        (function(){
        for(var j=0,down_max=downstream.length;j<down_max;j++){
            for(var h=0,max=hop_list.length;h<max;h++){                        
                if(hop_list[h].from==downstream[j]){
                    //prevent circular supplychain
                    if(jQuery.inArray(hop_list[h].to,downstream)>0)
                        continue;
                    downstream.push(hop_list[h].to);
                    down_max = downstream.length; 
                }
            }
        }
        })(); // end of funciton
        //upstream
        (function(){
        for(var j=0,up_max=upstream.length;j<up_max;j++){
            for(var h=0,max=hop_list.length;h<max;h++){                        
                if(hop_list[h].to==upstream[j]){
                    //prevent circular supplychain
                    if(jQuery.inArray(hop_list[h].from,upstream)>0)
                        continue;
                    upstream.push(hop_list[h].from);
                    up_max = upstream.length; 
                }
            }
        }
        })(); // end of function
    }

    function check_hops(hop,select)
    {
        // hops that connect to select stop
        if(hop.from==tier_list[select].instance||hop.to==tier_list[select].instance)
            return false;
        if(jQuery.inArray(hop.from,upstream)>0){    
            if(jQuery.inArray(hop.to,upstream)>0)
                return false;
        }
        if(jQuery.inArray(hop.from,downstream)>0){    
            if(jQuery.inArray(hop.to,downstream)>0)
                return false;
        }
        return true;    
    }

    function check_stops(i,select)
    {
        // false : not change opacity 
        if(i==select)
            return false;
        if(jQuery.inArray(tier_list[i].instance,downstream)>0)
            return false;
        if(jQuery.inArray(tier_list[i].instance,upstream)>0) 
            return false;
        //else return true
        return true;         
    }

    // Tree-text
/*
    svg.append("svg:g").selectAll("text")
        .data(tier_list)
        .enter().append("svg:text")
        .attr("x",function(d){return d.x})
        .attr("y",function(d){return d.y})
        .attr("dx",".1em") // padding
        .attr("dy","1.8em")
        .attr("text-anchor","middle")
        .text(function(d){return d.title});
  */  
}


Sourcemap.saveSupplychain = function(supplychain, o) {
    window.onbeforeunload = function() {
      window.onbeforeunload = null;
      if(!($.browser.msie)){
          return "Your map is being saved, are you sure you want to navigate away?";
      }
    };
    var o = o || {};
    var scid = o.supplychain_id ? o.supplychain_id : null;
    var succ = o.success ? o.success : null;
    var fail = o.failure ? o.failure : null;
    var scid = scid || null;
    var payload = null;
    if(typeof supplychain === "string") payload = supplychain;
    else payload = JSON.stringify({"supplychain": supplychain});
    
    $.ajax({
        "url": 'services/supplychains/'+(scid ? scid : ''),
        "type": scid ? 'PUT' : 'POST', // put to update, post to create
        "contentType": 'json', "data": payload,
        "dataType": "json",        
        "success": $.proxy(function(data) {
    		window.onbeforeunload = null;
            var new_uri = null; // indicates 'created'
            if(data && data.created) {
                new_uri = data.created;
                var scid = data.created.split('/').pop();
            } else if(data && data.success) {
                var scid = this.supplychain_id;
            }
            
            if(this.success && ((typeof this.success) === "function")) {
                this.success(this.supplychain, scid, new_uri);
            }
            Sourcemap.broadcast("supplychainSaveSuccess", this.supplychain, scid, new_uri);
        }, {
            "supplychain_id": scid, "supplychain": supplychain, 
            "success": succ, "failure": fail
        }),
        "failure": $.proxy(function(data) {
            if(this.failure && ((typeof this.failure) === "function")) {
                this.failure(this.supplychain, this.supplychain_id);
            }
            Sourcemap.broadcast("supplychainSaveFailure", this.supplychain, this.supplychain_id);
        }, {
            "supplychain_id": scid, "supplychain": supplychain,
            "success": succ, "failure": fail
        })
    });
    return;
}

/*
 * Sourcemap.humanDate
 *  
 * now  : Current local time millisecond
 * then : Previous time in millisecond
 */

Sourcemap.humanDate = function(then, now) {
    var now = Math.floor((now ? now.getTime() : (new Date()).getTime())/1000);
    var then = Math.floor(then.getTime()/1000);
    var str = '';
    var dow = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    var moy = [
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"
    ];
    if(then > now) str = 'in the future';
    else if(now - then < 60) {
        str = 'just now';
    } else if(now - then < 60 * 60) {
        str = 'less than an hour ago';
    } else  if(now - then < 60 * 60 * 3) {
        str = 'a couple of hours ago';
    } else if(now - then < 60 * 60 * 6) {
        str = 'a few hours ago';
    } else if(now - then < 60 * 60 * 24) {
        str = 'today';
    } else if(now - then < 60 * 60 * 24 * 7) {
        str = 'on '+dow[(new Date(then*1000)).getDay()];
    } else if(now - then < 60 * 60 * 24 * 14) {
        str = 'last '+dow[(new Date(then*1000)).getDay()];
    } else if(now - then < 60 * 60 * 24 * 30) {
        str = 'in the last few weeks';
    } else if(now - then < 60 * 60 * 24 * 30 * 12) {
        str = 'in '+moy[(new Date(then*1000)).getMonth()];
    } else if(now - then < 60 * 60 * 24 * 30 * 16) {
        str = 'about a year ago';
    } else {
        str = 'a long time ago';
    }
    return str;
}

Sourcemap.htesc = function(str) {
    var esc = str;
    try {
        esc = $('<div/>').text(str).html();
    } catch(e) {
        // pass
        esc = str;
    }
    if(esc.length === 0) esc = str;
    return esc;
}

Sourcemap.ttrunc = function(str, lim, dots) {
    var suffix = '...';
    var tstr = str.substr(0, lim);
    if(str.length > tstr.length) {
        tstr = tstr.substr(0, tstr.length - 3) + '...';
    }
    return tstr;
}

Sourcemap.truncate_string = function(target){
    $(target).each(function(){

        // grab parent element dimensions
        var width  = $(this).parent().width();
        var height = $(this).parent().height();

        // clone div to a new element for measurement
        var testdiv = $(this).clone().css({'width': width, 'height': 'auto', 'overflow': 'auto', 'display': 'hidden'});
        $('<br />').appendTo($(this).parent());
        $(testdiv).appendTo($(this).parent());
        var text = $.trim($(this).text());
        replacementText = null;
       
        // remove one character at a time until the height equals the original 
        for(i=text.length; height < $(testdiv).height() && i > 0; i--){
            replacementText = text.substr(0, i);
            $(testdiv).text(replacementText + "...");
        }
        
        if(replacementText)
            $(this).text($.trim(replacementText.slice(0,-1)) + "...");
        $(testdiv).prev().remove();
        $(testdiv).remove();
    });
}


Sourcemap.tlinkify = function(str) {
    var txt = _S.htesc(str);
    var regex = /((((http|https):\/\/([\w\d]+\.){1,2})|www\.[\w\d-]+\.)([\w\d]{2,3})((\/[\d\w%\.]+)+)?(\?([\d\w%]+=[\d\w%]+&?)+)?)/g;
    regex = new RegExp(regex);
    return txt.replace(regex, '<a href="$1">$1</a>');
}

Sourcemap.markdown = function(mrkdn) {
    if(!Showdown || !Showdown.converter) 
        throw new Exception('Showdown missing.');
    return (new Showdown.converter()).makeHtml(_S.htesc(mrkdn));
}

Sourcemap.okeys = function(o) {
    var keys = [];
    for(var k in o) keys.push(k);
    return keys;
}

Sourcemap.ovals = function(o) {
    var vals = [];
    for(var k in o) vals.push(o[k]);
    return vals;
}

Sourcemap.oksort = function(o, cmp) {
    var cmp = cmp ? cmp : function(a, b) { return a.v > b.v ? 1 : (a.v < b.v ? -1 : 0); };
    var keys = Sourcemap.okeys(o);
    var vals = Sourcemap.ovals(o);
    var olist = [];
    for(var ki=0; ki<keys.length; ki++) olist.push({"k": keys[ki], "v": vals[ki]});
    olist.sort(cmp);
    var sorted = [];
    for(var ki=0; ki<olist.length; ki++) sorted.push(olist[ki].k);
    return sorted;
}

Sourcemap.hexc2rgb = function(hexc) {
    if(hexc.charAt(0) == "#") hexc = hexc.substr(1);
    if(hexc.match(/^[0-9A-Fa-f]{3}$/)) {
        hexc = hexc.replace((new RegExp("([0-9a-fA-F])", "g")), "$1$1");
    }
    var r, g, b;
    if(hexc.match(/^[0-9a-fA-F]{6}$/)) {
        hexc = parseInt(hexc, 16);
        r = (hexc & 0xff0000) >> (8*2);
        g = (hexc & 0x00ff00) >> 8;
        b = (hexc & 0x0000ff)
    } else throw new Error('Invalid hex color.');
    return [r,g,b];
}

Sourcemap.rgb2hexc = function(rgb) {
    if(!rgb.length || rgb.length < 3)
        throw new Error('Invalid rgb.');
    var r = Math.min(256, Math.max(0, rgb[0]));
    var g = Math.min(256, Math.max(0, rgb[1]));
    var b = Math.min(256, Math.max(0, rgb[2]));
    var hexc = 0;
    hexc = (new Number(r)) << (8*2);
    hexc |= (new Number(g)) << 8;
    hexc |= (new Number(b));
    hexc = hexc.toString(16);
    while(hexc.length < 6) hexc = "0"+hexc;
    return "#"+hexc;
}

Sourcemap.Color = function(r, g, b) {
    this.r = r || 0;
    this.g = g || 0;
    this.b = b || 0;
}

Sourcemap.Color.prototype.fromHex = function(hexc) {
    var rgb = Sourcemap.hexc2rgb(hexc);
    this.r = rgb[0];
    this.g = rgb[1];
    this.b = rgb[2];
    return this;
}

Sourcemap.Color.prototype.toString = function() {
    return Sourcemap.rgb2hexc([this.r, this.g, this.b]);
}

Sourcemap.Color.prototype.clone = function() {
    var rgb = Sourcemap.hexc2rgb(this.toString());
    return new Sourcemap.Color(rgb[0],rgb[1],rgb[2]);
}

Sourcemap.Color.prototype.midpoint = function(to_color) {
    var dr = to_color.r - this.r;
    var mr = this.r + (Math.round(dr/2))
    var dg = to_color.g - this.g;
    var mg = this.g + (Math.round(dg/2))
    var db = to_color.b - this.b;
    var mb = this.b + (Math.round(db/2))
    return new Sourcemap.Color(mr, mg, mb);
}

Sourcemap.Color.graduate = function(colors, ticks) {
    ticks = isNaN(parseInt(ticks)) ? colors.length : parseInt(ticks);
    if(!ticks) return [];
    var g = [];
    var colors = colors.slice(0);
    while(colors.length > ticks) {
        var ri = Math.floor(colors.length / 2);
        colors.splice(ri,1);
    }
    while(colors.length < ticks) {
        var g = [];
        var d = Math.min(ticks - colors.length, colors.length-1);
        for(var i=0; i<colors.length; i++) {
            var c = colors[i];
            g.push(c);
            if(d) {
                g.push(c.midpoint(colors[i+1]));
                d--;
            }
        }
        colors = g.slice(0);
    }
    return colors;
}

Sourcemap.R = 6371 //km = 3959 miles

Sourcemap.radians = function(deg) {
    return deg*Math.PI/180;
}

Sourcemap.degrees = function(rad) {
    return rad*180.0/Math.PI;
}

Sourcemap.dms2decdeg = function(d, m, s) {
    dd = Number(d);
    dd = dd + m/60.0;
    dd = dd + s/Math.pow(60.0,2.0);
    return dd;
}

Sourcemap.haversine = function(pt1, pt2) {
    // Calculate great circle distance between points on a spheriod
    var R = Sourcemap.R;
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    var lat2 = pt2.y;
    var lon2 = pt2.x;

    lat1 = Sourcemap.radians(lat1);
    lon1 = Sourcemap.radians(lon1);
    lat2 = Sourcemap.radians(lat2);
    lon2 = Sourcemap.radians(lon2);
    var dLat = lat2-lat1;
    var dLon = lon2-lon1;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) + 
        Math.cos(lat1) * Math.cos(lat2) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c;
    return d;
}

Sourcemap.great_circle_bearing = function(pt1, pt2) {       //Note longitude is 0 at Greenich - in western hemisphere and positive in eastern
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    var lat2 = pt2.y;
    var lon2 = pt2.x;
    lat1 = Sourcemap.radians(lat1)
    lon1 = Sourcemap.radians(lon1)
    lat2 = Sourcemap.radians(lat2)
    lon2 = Sourcemap.radians(lon2)
    var dLon = lon2 - lon1     //Longitude is east-west distance...note this returns directional value (might be bigger than pi but cos symetric around 0) 
                                       //Note we switch sin and cos for latitude b/c 0 latitude is at equator    
    var y = Math.sin(dLon)*Math.cos(lat2)  //This calculates y position in cartesian coordinates with radius earth=1
    var x = Math.cos(lat1)*Math.sin(lat2) - 
        Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLon)
    var brng = Sourcemap.degrees(Math.atan2(y, x))     //Note bearing is the differential direction of the arc given in degrees relative to east being 0
    return brng  //Using the plane carie (sp?) projection EPSG:4326 (sending long to x and lat to y) brng is also differential direction of arc in projection
}

Sourcemap.great_circle_midpoint = function(pt1, pt2) {
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    var lat2 = pt2.y;
    var lon2 = pt2.x;
    lat1 = Sourcemap.radians(lat1);
    lon1 = Sourcemap.radians(lon1);   
    lat2 = Sourcemap.radians(lat2);
    lon2 = Sourcemap.radians(lon2);
    var dLon = lon2 - lon1;
    var Bx = Math.cos(lat2) * Math.cos(dLon);
    var By = Math.cos(lat2) * Math.sin(dLon);
    var lat3 = Math.atan2(Math.sin(lat1)+Math.sin(lat2),
        Math.sqrt((Math.cos(lat1)+Bx)*(Math.cos(lat1)+Bx) + By*By));
    var lon3 = lon1 + Math.atan2(By, Math.cos(lat1) + Bx);
    return {"y": Sourcemap.degrees(lat3), "x": Sourcemap.degrees(lon3)};
}

Sourcemap.great_circle_endpoint = function(pt1, brng, d) {
    var R = Sourcemap.R;
    var lat1 = pt1.y;
    var lon1 = pt1.x;
    lat1 = Sourcemap.radians(lat1);
    lon1 = Sourcemap.radians(lon1);
    lat2 = Math.asin(Math.sin(lat1)*Math.cos(d/R) + 
            Math.cos(lat1)*Math.sin(d/R)*Math.cos(brng));
    lon2 = lon1 + Math.atan2(Math.sin(brng)*Math.sin(d/R)*Math.cos(lat1), 
            Math.cos(d/R)-Math.sin(lat1)*Math.sin(lat2));
    return {"y": Sourcemap.degrees(lat2), "x": Sourcemap.degrees(lon2)};
}

Sourcemap.great_circle_route = function(pt1, pt2, ttl) {
    var mp = Sourcemap.great_circle_midpoint(pt1, pt2);
    var rt = [pt1];
    if(ttl > 0) {
        var ttl = ttl - 1;
        rt = rt.concat(Sourcemap.great_circle_route(pt1, mp, ttl));
        rt = rt.concat(Sourcemap.great_circle_route(mp, pt2, ttl));
    }
    rt.push(pt2);
    //var rtuniq = []
    // TODO: find and discard duplicates...
    return rt;
}

Sourcemap.Units = {};

Sourcemap.Units.si_prefixes = {
    "y": {"label": "yocto", "mult":-24}, "z": {"label": "zepto", "mult": -21},
    "a": {"label": "atto", "mult": -18}, "f": {"label": "femto", "mult": -15},
    "p": {"label": "pico", "mult": -12}, "n": {"label": "nano", "mult": -9},
    "u": {"label": "micro", "mult": -6}, "m": {"label": "milli", "mult": -3},
    //"c": {"label": "centi", "mult": -2}, "d": {"label": "deci", "mult": -1},
    //"D": {"label": "deca", "mult": 1},  "h": {"label": "hecto", "mult": 2},
    "k": {"label": "kilo", "mult": 3}, "M": {"label": "mega", "mult": 6},
    "G": {"label": "giga", "mult": 9}, "T": {"label": "tera", "mult": 12},
    "P": {"label": "peta", "mult": 15}, "E": {"label": "exa", "mult": 18},
    "Z": {"label": "zetta", "mult": 21}, "Y": {"label": "yotta", "mult": 24}
}

Sourcemap.Units.si_equiv = {
    "g": {"abbrev": "g", "singular": "gram", "plural": "grams"},
    "Mg": {"abbrev": "t", "singular": "tonne", "plural": "tonnes"},
    "Gg": {"abbrev": "kt", "singular": "kilotonne", "plural": "kilotonnes"},
    "Tg": {"abbrev": "Mt", "singular": "megatonne", "plural": "megatonnes"},
    "Pg": {"abbrev": "Gt", "singular": "gigatonne", "plural": "gigatonne"}
}

Sourcemap.Units.to_base_unit = function(value, unit) {
    var value = parseFloat(value);
    var unit = new String(unit);
    var prefix = null;
    var max_prefix_len = 2;
    var prefix_len = max_prefix_len;
    while(prefix === null && prefix_len > 0) {
        if(unit.length > prefix_len) {
            if(Sourcemap.Units.si_prefixes[unit.substr(0, prefix_len)] !== undefined) {
                prefix = unit.substr(0, prefix_len);
                break;
            }
        }
        prefix_len--;
    }
    var from_power_of_ten = prefix ? Sourcemap.Units.si_prefixes[prefix].mult : 0;
    var base_unit = prefix !== null ? unit.substr(prefix_len) : unit+"";
    var base_value = value * Math.pow(10, from_power_of_ten);
    var base = {"unit": base_unit, "value": base_value};
    return base;
}

Sourcemap.Units.scale_unit_value = function(value, unit, precision) {       //For showing two significant figures and correct unit
    if(isNaN(value)) return 0;
    var precision = isNaN(parseInt(precision)) ? 2 : parseInt(precision);
    var base = Sourcemap.Units.to_base_unit(value, unit);
    var pot = base.value === 0 ? 0 : Math.floor((Math.log(base.value)/Math.log(10))+.000000000001);
    var new_unit = null;
    //if(value == 0 || pot === 0) {
    if(pot < 2) {
        //new_unit = {"label": base.unit, "mult": 0};
        if(base.value < 10) base.value = parseFloat(base.value).toFixed(1);
        else base.value = Math.round(base.value);
        return base;
    } else {
        new_unit = false;
        while(new_unit === false) {
            for(var p in Sourcemap.Units.si_prefixes) {
                var u = Sourcemap.Units.si_prefixes[p];
                if(u.mult === pot) {
                    new_unit = p;
                    break;
                }
            }
            if(new_unit !== false) break;
            if(pot <= -24) {
                new_unit = p; 
                break;
            } else if(pot >= 24) {
                new_unit = p;
                break;
            }
            if((pot % 3) === 2) pot++;
            else if(pot % 3) pot--;
            else pot = pot - 3;
        }
        new_unit += base.unit;
        new_unit = {"label": new_unit, "mult": pot};
    }
    var scaled_value = parseFloat(base.value * Math.pow(10, -new_unit.mult)).toFixed(1);
    if(scaled_value < 10) scaled_value = parseFloat(scaled_value).toFixed(1);
    else scaled_value = parseFloat(Math.round(scaled_value));
    var scaled_unit = new_unit;
    var scaled = {"unit": scaled_unit.label, "value": scaled_value};
    if(Sourcemap.Units.si_equiv[scaled.unit] !== undefined) 
        scaled.unit = Sourcemap.Units.si_equiv[scaled.unit].abbrev;
    return scaled;
}

// Date fxns
Sourcemap.Date = {};

Sourcemap.Date.months = [
    "January","February","March","April","May","June",
    "July","August","September","October","November","December"
];

Sourcemap.Date.get_month_name = function(num) { // 1-12
    return this.months[num];
}

Sourcemap.Date.get_month_abbr = function(num) {
    return this.get_month_name(num).substr(0,3);
}

Sourcemap.Date.format = function(dt) {
    var s = this.get_month_abbr(dt.getMonth());
    s += " "+dt.getDate()+", "+dt.getFullYear();
    return s;
}

Sourcemap.fmt_date = function(dt) {
    return Sourcemap.Date.format(dt);
}

