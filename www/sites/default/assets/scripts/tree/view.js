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
    var passcode = ""; 
    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();    

    if($('#exist-passcode').attr("value")){
        var popID = 'popup';
        var element = document.createElement('div');
        $(element).html(
            '<div id="passcode-input">'+
            '<form class="passcode-input">'+
            '<label for="passcode"> This map is protected. Please enter the password:</label>'+
            '<input name="passcode" type="text"></input>'+
            '<input id="passcode-submit" type="submit"/>'+
            '</form>'
            +'</div>'
        );
        $(element).attr('id',popID);
        $(element).addClass("popup_block");
        $(element).prepend('<a href="#" class="close"></a>');
        $('body').append($(element));

        $('form.passcode-input').submit(function(evt){
            evt.preventDefault();
            passcode = $(element).find("input[name='passcode']").val();

            // fetch from supplychain
            Sourcemap.loadSupplychainToTree(scid, passcode, function(sc) {
                Sourcemap.buildTree(tree_element_id,sc);
            });
            $('#fade , .popup_block').fadeOut(function() {
                $('#fade, a.close').remove();
            }); //fade them both out
        });

        //css
        $('#' + popID).height(110);
        $('#' + popID).width(600);
        var popMargTop = ($('#' + popID).height() + 80) / 2;
        var popMargLeft = ($('#' + popID).width() + 80) / 2;

        $('#' + popID).css({
            'margin-top' : -popMargTop,
            'margin-left' : -popMargLeft,
            'overflow' : 'hidden'
        });

        $('#' + popID).fadeIn();

        $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
        $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 
        $('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
            $('#fade , .popup_block').fadeOut(function() {
                $('#fade, a.close').remove();
            }); //fade them both out
            return false;
        });

    } else {
        Sourcemap.loadSupplychainToTree(scid, passcode, function(sc) {
            Sourcemap.buildTree(tree_element_id,sc);
        });
    }

});
