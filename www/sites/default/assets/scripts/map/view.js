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
    Sourcemap.view_params = Sourcemap.view_params || {};
    Sourcemap.view_instance = new Sourcemap.Map.Base(Sourcemap.view_params);

    Sourcemap.listen("map:supplychain_mapped", function(evt, map, sc) {
        var view = Sourcemap.view_instance;
        view.user_loc = Sourcemap.view_params.iploc ? Sourcemap.view_params.iploc[0] : false;
        if(view.options.locate_user) {
            view.showLocationDialog();
        }
    });

    // get scid from inline script
    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();

    // fetch supplychain
    Sourcemap.loadSupplychain(scid, function(sc) {
        var m = Sourcemap.view_instance.map;
        m.addSupplychain(sc);

        if(sc.editable){
            // /view/[map_num]#edit
            if(document.location.href.split("?")[1]=="edit") {
                new Sourcemap.Map.Editor(Sourcemap.view_instance);
            }
        }
    });
});
