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
    var tree_element_id = '#sourcemap-tree-view';
    $('#tree-container').css("min-height", $(window).height()).css("min-width", $(window).width());
    $(tree_element_id).css("height",$(window).height()*.95).css("width",$(window).width()*.95);
    var passcode = ""; 
    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();    

    //if($('#exist-passcode').attr("value")){
    if(Sourcemap.passcode_exist){
        var popID = 'popup';
        Sourcemap.initPasscodeInput(popID);
        $('form.passcode-input').submit(function(evt){
            evt.preventDefault();
            passcode = $('#' + popID).find("input[name='passcode']").val();

            $('#fade , .popup_block').fadeOut(function() {
                //$('#fade, a.close').remove();
            }); //fade them both out

            // fetch from supplychain
            Sourcemap.loadSupplychainToTree(scid, passcode, function(sc) {
                Sourcemap.buildTree(tree_element_id,sc);
            });
        });
    } else {
        Sourcemap.loadSupplychainToTree(scid, passcode, function(sc) {
            Sourcemap.buildTree(tree_element_id,sc);
        });
    }

});
