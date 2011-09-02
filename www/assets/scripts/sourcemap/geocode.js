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

Sourcemap.geocode_url = "services/geocode/";
Sourcemap.geocode_cache = {};

Sourcemap.geocode = function(placename, cb) {
    var url = Sourcemap.geocode_url;
    var __cb = cb instanceof Function ? cb : function() {};
    var cached = Sourcemap.geocode_cache[placename];
    if(cached) {
        __cb({"results": [cached]});
    } else {
        $.get(url, {"placename": placename}, function(data) {
            Sourcemap.geocode_cache[placename] = data.results[0];
            for(var i=0; i<data.results.length; i++) {
                data.results[i].lon = data.results[i].lng;
                Sourcemap.geocode_cache[data.results[i].placename] = data.results[i];
            }
            __cb(data);
        }, "json");
    }
}

Sourcemap.geodecode = function(pt, cb) {
    var url = Sourcemap.geocode_url;
    var ll = false;
    if(!(pt.lat == undefined || pt.lon == undefined)) {
        ll = pt.lat+","+pt.lon;
    } else if(!(pt.y == undefined || pt.x == undefined)) {
        ll = pt.y+","+pt.x;
    }
    var __cb = cb instanceof Function ? cb : function() {};
    $.get(url, {"ll": ll}, function(data) {
        __cb(data);
    }, "json");
};

(function($) {
    var methods = {
        "init": function(o) {
            return this.each(function() {
                var $this = $(this);
                $this.autocomplete({
                    "source": function(rq, rscb) {
                        Sourcemap.geocode($this.val(), function(data) {
                            var results = [];
                            for(var i=0; i<data.results.length; i++) {
                                results.push({
                                    "label": data.results[i].placename, 
                                    "value": data.results[i].placename
                                });
                            }
                            return rscb(results);
                        });
                    },
                    "change": function(evt, ui) {
                        $this.geocomplete("select", ui.item.label, ui.item.value);
                    }
                });
                var data = $this.data("geocomplete");
                if(!data) {
                    $this.data("geocomplete", {
                        "min_term_len": 5,
                        "on_select": function(data) { }
                    });
                }
            });
        },
        "enable": function() {
            return this.each(function() {
                var $this = $(this);
                $this.autocomplete("enable");
            });
        },
        "select": function(l,v) {
            return this.each(function() {
                var $this = $(this);
                Sourcemap.geocode(v, function(data) {
                    var scb = $this.data("geocomplete").on_select;
                    if(scb instanceof Function) {
                        scb(data);
                    }
                });
            });
        },
        "destroy": function() {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data("geocomplete");
                data.geocomplete.remove();
                $this.removeData("geocomplete");
                $this.autocomplete("destroy");
            });
        }
    };
    $.fn.geocomplete = function(method) {
        if ( methods[method] ) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if(typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method '+method+' does not exist on jQuery.geocomplete.');
        }  
    }
})(jQuery);
