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

    // get scid from inline script
    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();

    // fetch supplychain
    var passcode = "";
    if(!Sourcemap.passcode_exist){
        Sourcemap.loadSupplychain(scid, passcode, function(sc) {
            new Sourcemap.Map.List(sc);
        });
    } else {
        var popID = "popup";
        Sourcemap.initPasscodeInput(popID);
        $('form.passcode-input').submit(function(evt){
            evt.preventDefault();
            passcode = $('#' + popID).find("input[name='passcode']").val();
            var cb = function(sc){
                new Sourcemap.Map.List(sc);                
                $('#fade , .popup_block').fadeOut(function() {
                    //$('#fade, a.close').remove();
                });
            };
            Sourcemap.loadSupplychain(scid, passcode,cb);
                    
        }); //end submit  
    }
});
