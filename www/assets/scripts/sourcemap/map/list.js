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

Sourcemap.Map.List = function(o) {    
    this.broadcast('map_list:instantiated', this);
    var o = o || {};
    Sourcemap.Configurable.call(this, o);
    this.instance_id = Sourcemap.instance_id("sourcemap-list");
}

Sourcemap.Map.List.prototype = new Sourcemap.Configurable();

Sourcemap.Map.List.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.List.prototype.defaults = {
    "auto_init": true
}

Sourcemap.Map.List.prototype.init = function() {
    this.render();
}
Sourcemap.Map.List.prototype.render = function() {
    for(var i in this.options.stops) {
		if(this.options.stops[i].attributes) {
	        this.options.stops[i].attributes.kind = "stop";
	        Sourcemap.template('map/details/item', function(p, tx, th) {
	            $("#list-container").append(th);
	        }, {"base": this, "item": this.options.stops[i]} );
		}
    }
    for(var i in this.options.hops) {
		if(this.options.hops[i].attributes) {	
	        this.options.hops[i].attributes.kind = "hop";        
	        Sourcemap.template('map/details/item', function(p, tx, th) {
	            $("#list-container").append(th);
	        }, {"base": this, "item": this.options.hops[i]} );
		}
    }

}

