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

Sourcemap.Supplychain = function() {
    this.remote_id = null;
    this.instance_id = Sourcemap.instance_id("supplychain");
    this.stops = [];
    this.hops = [];
    this.attributes = {};
    this.usergroup_perms = 0;
    this.other_perms = 0;
    this.user_featured = false;
    this.editable = true;
    this.broadcast('supplychain:instantiated', this);
}

Sourcemap.Supplychain.TEASER_LEN = 80;

Sourcemap.Supplychain.prototype.getAttr = function(k, d) {
    if(arguments.length == 1) {
        return this.attributes[k];
    } else if(arguments.length > 2) {
        for(var i=0, args=[]; i<arguments.length; args.push(arguments[i++]));
        var d = args.pop();
        for(var i=0; i<args.length; i++) {
            var k = args[i];
            if(this.attributes[k] !== undefined) return this.attributes[k];
        }
        return d;
    }
    if(this.attributes[k] !== undefined) return this.attributes[k];
    else return d;
}

Sourcemap.Supplychain.prototype.getLabel = function() {
    return this.getAttr("name", "label", "title", "A Sourcemap");
}

Sourcemap.Supplychain.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Supplychain.prototype.stopIds = function() {
    var ids = [];
    for(var i=0; i<this.stops.length; i++) ids.push(this.stops[i].instance_id);
    return ids;
}

Sourcemap.Supplychain.prototype.localStopIds = function() {
    var lids = [];
    for(var i=0; i<this.stops.length; i++) lids.push(this.stops[i].local_stop_id);
    return lids;
}

Sourcemap.Supplychain.prototype.findStop = function(target_id) {
    var found = false;
    for(var i=0; i<this.stops.length; i++) {
        if(this.stops[i].instance_id === target_id) {
            found = this.stops[i];
            break;
        }
    }
    return found;
}

Sourcemap.Supplychain.makeTiers = function (sc) {
    // make instance tree
    var g = new Sourcemap.Supplychain.Graph2(sc);
    var stids = g.nids.slice(0);
	var max_plen = 0;
	var upperbound = 0;
	
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

    sc.tiers = tiers;
    sc.max_plen = max_plen;

    var tiers = [],tier_list = [],hop_list = [];
    var max_plen = sc.max_plen;
    var max_length=(max_plen)?sc.max_plen:1,max_x=0,max_y=0;    
    // palette stuff

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

            var label = Sourcemap.ttrunc(letitle, 24);
            
	        if(sc.stops[i].attributes.size == undefined)
	            size = 2;
	        else
	            size = sc.stops[i].attributes.size;

	        tier_list[i] = { 
	            title:letitle,
                label:label,
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
                        continue;
                    }
                }
                if(child_stop_id =="")
                    continue;   // means its the end of the stop
                else if(jQuery.inArray(parent_stop_id,finish_stop_id_list)>=0)
                    continue;
                else{    
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

    // Create hop points
    for(var i=0, length=sc.hops.length;i<length;i++)
    {
        var h = sc.hops[i];
        hop_list[i] = {            
            id:h.instance_id,
            from:h.from_stop_id,
            to:h.to_stop_id
        }
    }

	sc.tier_list = tier_list;
	sc.hop_list = hop_list;
	return sc;
}

Sourcemap.Supplychain.prototype.addStop = function(stop) {
    if(stop instanceof Sourcemap.Stop) {
        if(this.stopIds().indexOf(stop.instance_id) >= 0) {
            throw new Error("Stop already exists in this supplychain.");
        }
        stop.supplychain_id = this.instance_id;
        lsids = this.localStopIds();
        var last_id = lsids.length ? Math.max.apply(window, this.localStopIds()) : 0;
        stop.local_stop_id = last_id + 1;
        this.stops.push(stop);
        this.broadcast('supplychain:stop_added', this, stop);
    } else throw new Error("Sourcemap.Stop expected.");
    return this;
}

Sourcemap.Supplychain.prototype.removeStop = function(target_id) {
    if(target_id instanceof Sourcemap.Stop)
        target_id = target_id.instance_id;
    var removed = false;
    var hrmd = [];
    for(var i=0; i<this.hops.length; i++) {
        var h = this.hops[i];
        if(h.from_stop_id === target_id || h.to_stop_id === target_id) {
            //this.removeHop(h);
            hrmd.push(i);
            h.supplychain_id = null;
            h.from_stop_id = null;
            h.to_stop_id = null;
        }
    }
    for(var i=hrmd.length; i>0; i--) this.hops.splice(hrmd[i-1],1);
    for(var i=0; i<this.stops.length; i++) {
        if(this.stops[i].instance_id === target_id) {
            removed = this.stops.splice(i,1)[0];
            //removed.supplychain_id = null;
            break;
        }
    }
    this.broadcast('supplychain:stop_removed', this, removed);
    return removed;
}

Sourcemap.Supplychain.prototype.stopHops = function(stop_id) {
    if(stop_id instanceof Sourcemap.Stop)
        stop_id = stop_id.instance_id;
    var stop_hops = {
        'in': [], 'out': []
    };
    for(var i=0; i<this.hops.length; i++) {
        var hop = this.hops[i];
        if(hop.from_stop_id === stop_id) {
            stop_hops.out.push(hop.instance_id);
        } else if(hop.to_stop_id === stop_id) {
            stop_hops["in"].push(hop.instance_id);
        }
    }
    return stop_hops;
}

Sourcemap.Supplychain.prototype.cycleCheck = function() {
    var vector = [];
    var stack = [];
    for(var i=0; i<this.hops.length; i++) {
        if(this.hops[i].from_stop_id === this.hops[i].to_stop_id) {
            throw new Error("Hop '"+this.hops[i].instance_id+"' is circular.");
        }
        var n = this.hops[i].from_stop_id;
        var v = [];
        var st = [{"n": n, "v": v}];
        while(st.length) {
            var sti = st.pop();
            var n = sti.n;
            var v = sti.v;
            var new_v = Sourcemap.deep_clone(v);
            new_v.push(n);
            var outgoing = this.stopHops(n).out;
            for(var oi=0; oi<outgoing.length; oi++) {
                var out_hop = this.findHop(outgoing[oi]);
                if(new_v.indexOf(out_hop.to_stop_id) >= 0) {
                    throw new Error("Found cycle at hop from '"+n+"' to '"+
                        out_hop.to_stop_id+"' in '"+this.instance_id+"'.");
                }
                st.push({"n": out_hop.to_stop_id, "v": new_v});
            }
        }
    }
    return true;
}

Sourcemap.Supplychain.prototype.hopIds = function() {
    var ids = [];
    for(var i=0; i<this.hops.length; i++) ids.push(this.hops[i].instance_id);
}

Sourcemap.Supplychain.prototype.findHop = function(target_id) {
    var found = false;
    for(var i=0; i<this.hops.length; i++) {
        if(this.hops[i].instance_id === target_id) {
            found = this.hops[i];
            break;
        }
    }
    return found;
}

Sourcemap.Supplychain.prototype.hopExists = function(from_stop_id, to_stop_id) {
    var exists = false;
    for(var i=0; i<this.hops.length; i++) {
        var hop = this.hops[i];
        if(hop.from_stop_id === from_stop_id &&
            hop.to_stop_id === to_stop_id) {
            exists = hop.instance_id;
            break;
        }
    }
    return exists;
}

Sourcemap.Supplychain.prototype.addHop = function(hop) {
    if(hop instanceof Sourcemap.Hop) {
        if(this.hopExists(hop.from_stop_id, hop.to_stop_id) || this.hopExists(hop.to_stop_id, hop.from_stop_id)) {
            throw new Error("Hop exists.");
        }
        this.hops.push(hop);
        hop.supplychain_id = this.instance_id;
        hop.from_local_stop_id = this.findStop(hop.from_stop_id).local_stop_id;
        hop.to_local_stop_id = this.findStop(hop.to_stop_id).local_stop_id;
        this.broadcast('supplychain:hop_added', this, hop);
    } else throw new Error("Sourcemap.Hop expected.");
    return this;
}

Sourcemap.Supplychain.prototype.removeHop = function(hop_id) {
    if(hop_id instanceof Sourcemap.Hop)
        hop_id = hop_id.instance_id;
    var removed = false;
    for(var i=0; i<this.hops.length; i++) {
        if(hop_id === this.hops[i].instance_id) {
            removed = this.hops.splice(i,1)[0];
            //removed.supplychain_id = null;
        }
    }
    this.broadcast('supplychain:hop_removed', this, removed);
    return removed;
}

Sourcemap.Supplychain.prototype.stopAttrRange = function(attr_nm) {
    var min = null;
    var max = null;
    var total = 0;
    for(var i=0; i<this.stops.length; i++) {
        var stop = this.stops[i];
        var val = null;
        if(attr_nm instanceof Function) {
            val = attr_nm(stop);
        } else if(stop.attributes[attr_nm] === undefined) {
            continue;
        } else {
            val = parseFloat(stop.attributes[attr_nm]);
        }
        if(isNaN(val))
            continue;
        if(min === null) min = val;
        if(max === null) max = val;
        min = Math.min(val, min);
        max = Math.max(val, max);
        total += val;
    }
    return {
        "min": min, "max": max, "total": total
    };
}

Sourcemap.Supplychain.prototype.hopAttrRange = function(attr_nm) {
    var min = null;
    var max = null;
    var total = 0;
    for(var i=0; i<this.hops.length; i++) {
        var hop = this.hops[i];
        var val = null;
        if(attr_nm instanceof Function) {
            val = attr_nm(hop);
        } else if(hop.attributes[attr_nm] === undefined) {
            continue;
        } else {
            val = parseFloat(hop.attributes[attr_nm]);
        }
        if(isNaN(val))
            continue;
        if(min === null) min = val;
        if(max === null) max = val;
        min = Math.min(val, min);
        max = Math.max(val, max);
        total += val;
    }
    return {
        "min": min, "max": max, "total": total
    };
}

Sourcemap.Supplychain.prototype.attrRange = function(attr_nm) {
    var sr = this.stopAttrRange(attr_nm);
    var hr = this.hopAttrRange(attr_nm);
    var min = Math.min(sr.min, hr.min);
    var max = Math.max(sr.max, hr.max);
    var tot = sr.total + hr.total;
    return {"stops": sr, "hops": hr, "min": min, "max": max, "total": tot};
}

Sourcemap.Stop = function(geometry, attributes) {
    this.instance_id = Sourcemap.instance_id("stop");
    this.supplychain_id = null;
    this.local_id = null; // local id unique to supplychain
    this.geometry = geometry;
    this.attributes = attributes ? Sourcemap.deep_clone(attributes) : {};
}

Sourcemap.Stop.prototype.getAttr = function(k, d) {
    if(arguments.length == 1) {
        return this.attributes[k];
    } else if(arguments.length > 2) {
        for(var i=0, args=[]; i<arguments.length; args.push(arguments[i++]));
        var d = args.pop();
        for(var i=0; i<args.length; i++) {
            var k = args[i];
            if(this.attributes[k] !== undefined) return this.attributes[k];
        }
        return d;
    }
    if(this.attributes[k] !== undefined) return this.attributes[k];
    else return d;
}

Sourcemap.Stop.prototype.setAttr = function(k, v) {
    if(((typeof k) === "object") && !v) {
        for(var ok in k) {
            this.setAttr(ok, k[ok]);
        }
    } else if(((typeof v) === "undefined") && ((typeof this.attributes[k]) !== "undefined")) {
        delete this.attributes[v];
    } else this.attributes[k] = v;
    return this;
}

Sourcemap.Stop.prototype.getLabel = function() {
    var label = false;
    var search_keys = ["title", "name", "label"
    ];
    for(var ki=0; ki<search_keys.length; ki++) {
        var k = search_keys[ki];
        if(this.getAttr(k, false)) {
            label = this.getAttr(k, false);
        }   
    }
    return label;
}

Sourcemap.Stop.prototype.makeHopTo = function(to_stop) {
    var fromll = Sourcemap.Stop.toLonLat(this);
    var toll = Sourcemap.Stop.toLonLat(to_stop);
    var rt = Sourcemap.great_circle_route(fromll, toll);
    var pts = [];
    for(var i=0; i<rt.length; i++) {
        var pt = rt[i];
        pt = new OpenLayers.Geometry.Point(pt.lon, pt.lat);
        pts.push(pt);
    }
    var new_geom = new OpenLayers.Geometry.MultiLineString(
        new OpenLayers.Geometry.LineString(pts)
    );
    new_geom = new_geom.transform(
        new OpenLayers.Projection('EPSG:4326'),
        new OpenLayers.Projection('EPSG:900913')
    );
    new_geom = new OpenLayers.Feature.Vector(new_geom);
    new_geom = (new OpenLayers.Format.WKT()).write(new_geom);

    var new_hop = new Sourcemap.Hop(new_geom, this.instance_id, to_stop.instance_id);
    return new_hop;
}

Sourcemap.Stop.fromLonLat = function(ll, proj) {
    var proj = proj || 'EPSG:4326';
    var geom = new OpenLayers.Geometry.Point(ll.lon, ll.lat);
    geom = geom.transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:900913')
    );
    var wkt = new OpenLayers.Format.WKT();
    var stop = new Sourcemap.Stop(wkt.write(new OpenLayers.Feature.Vector(geom)));
    return stop;
}

Sourcemap.Stop.toLonLat = function(st, proj) {
    var proj = proj || 'EPSG:900913';
    var geom = (new OpenLayers.Format.WKT()).read(st.geometry).geometry;
    geom = geom.transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:4326')
    );
    return {"lon": geom.x, "lat": geom.y};
}

Sourcemap.Stop.geocode = function(st, cb) {
    var cb = cb || $.proxy(function(data) {
        if(data && data.results) {
            this.setAttr("address", data.results[0].placename);
           // this.map.broadcast('map:feature_selected', this.map, this.map.stopFeature(sc.instance_id, new_stop.instance_id));
        }
    }, st);
    var url = 'services/geocode';
    var ll = false;
    var pl = false;
    if(st instanceof Sourcemap.Stop) {
        ll = Sourcemap.Stop.toLonLat(st);
    } else if(st.lon != undefined && st.lat != undefined) {
        ll = st;
    } else if(typeof st == "string") {
        ll = false;
        pl = st;
    }
    $.ajax({"url": url, "type": "GET", "data": ll ? {"ll": ll.lat+','+ll.lon} : {"placename": pl}, 
        "success": cb, "failure": cb, "dataType": "json"
    });
}

Sourcemap.Hop = function(geometry, from_stop_id, to_stop_id, attributes) {
    this.instance_id = Sourcemap.instance_id("hop");
    this.supplychain_id = null;
    this.from_stop_id = from_stop_id;
    this.to_stop_id = to_stop_id;
    this.geometry = geometry;
    this.attributes = attributes ? Sourcemap.deep_clone(attributes) : {};
   
    if (!this.attributes.distance) {
        this.attributes.distance = this.gc_distance();
    }
}

Sourcemap.Hop.prototype.toJSON = function() {
    var j = Sourcemap.deep_clone(this);
    j.from_stop_id = this.from_local_stop_id;
    j.to_stop_id = this.to_local_stop_id;
    return j;
}

Sourcemap.Hop.prototype.getAttr = function(k, d) {
    if(this.attributes[k] !== undefined) return this.attributes[k];
    else return d;
}

Sourcemap.Hop.prototype.setAttr = function(k, v) {
    if(((typeof k) === "object") && !v) {
        for(var ok in k) {
            this.setAttr(ok, k[ok]);
        }
    } else if(((typeof v) === "undefined") && ((typeof this.attributes[k]) !== "undefined")) {
        delete this.attributes[v];
    } else this.attributes[k] = v;
    return this;
}

Sourcemap.Hop.prototype.getLabel = function() {
    var label = false;
    var search_keys = ["title", "name", "label"
    ];
    for(var ki=0; ki<search_keys.length; ki++) {
        var k = search_keys[ki];
        if(this.getAttr(k, false)) {
            label = this.getAttr(k, false);
        }   
    }
    return label;
}

Sourcemap.Hop.prototype.gc_distance = function() {
    
    var proj = proj || 'EPSG:900913';
    if(!this.geometry) return 0.0;
    var geom = (new OpenLayers.Format.WKT()).read(this.geometry).geometry;
    var from_geom = geom.components[0].components[0].clone().transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:4326')
    );
    var to_geom = geom.components[0].components[1].clone().transform(
        new OpenLayers.Projection(proj),
        new OpenLayers.Projection('EPSG:4326')
    );
    var gc_distance = Sourcemap.haversine(from_geom, to_geom);
    return gc_distance;

}
