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

Sourcemap.Map.Blog = function(o) {    
    this.broadcast('map_blog:instantiated', this);
    var o = o || {};
    Sourcemap.Configurable.call(this, o);
    this.instance_id = Sourcemap.instance_id("sourcemap-blog");
}

Sourcemap.Map.Blog.prototype = new Sourcemap.Configurable();

Sourcemap.Map.Blog.prototype.broadcast = function() {
    Sourcemap.broadcast.apply(Sourcemap, arguments);
    return this;
}

Sourcemap.Map.Blog.prototype.defaults = {
    "auto_init": true
}

Sourcemap.Map.Blog.prototype.init = function() {
    console.log(this);
    this.render();
}
Sourcemap.Map.Blog.prototype.render = function() {


    for(var i in this.options.stops) {
        this.options.stops[i].attributes.kind = "stop";
        console.log(this.options.stops[i]);
        Sourcemap.template('map/details/item', function(p, tx, th) {
            $("#blog-container").append(th);
        }, {"base": this, "item": this.options.stops[i]} );
    }
    for(var i in this.options.hops) {
        this.options.hops[i].attributes.kind = "hop";        
        Sourcemap.template('map/details/item', function(p, tx, th) {
            $("#blog-container").append(th);
        }, {"base": this, "item": this.options.hops[i]} );
    }

}

