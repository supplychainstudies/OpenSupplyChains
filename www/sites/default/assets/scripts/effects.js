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

if ($('.status-message').length > 0) {
    $('.status-messages').click(function(){
        $(this).fadeOut(0);
    });
    $('.status-messages').fadeIn(400);
}

/* modal window fxn courtesy http://www.sohtanaka.com/web-design/examples/modal-window/ */

$(document).ready(function(){
    $('a.modal').click(function(e) {
        e.preventDefault();
        var popURL = $(this).attr('href'); 

        // parse URL options
        var popDestURL = popURL.split('?')[0];
        var popDest = popURL.split('#')[0] + " #" + popURL.split('#')[1]; 
        var query = popURL.split('?');
        var dim = query[1].split('&');
        var popWidth = dim[0].split('=')[1]; //Gets the first query string value
        
        var popID = 'popup';

        // if an actual URL is specified, get it via a jQuery load()
        if (popDest){
            var element = document.createElement('div'); 
            $(element).load(popDest, function(){
                this.id = popID;

                $(this).addClass("popup_block");
                
                $(this).prepend('<a href="#" class="close"><img src="close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');

                $('body').append($(element));
               
                $('#' + popID).height(370);
                var popMargTop = ($('#' + popID).height() + 80) / 2;
                var popMargLeft = ($('#' + popID).width() + 80) / 2;
                
                $('#' + popID).css({ 
                    'margin-top' : -popMargTop,
                    'margin-left' : -popMargLeft
                });

                console.log($('#' + popID));

                $('#' + popID).width(popWidth); 
                $('#' + popID).fadeIn();
            });
        }

        // otherwise, grab the element whose id === rel
        else {
            var popID = $(this).attr('rel'); 
            $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close"><img src="close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');
        }
            

        $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
        $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 
        
        return false;
    });
    
    $('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
        $('#fade , .popup_block').fadeOut(function() {
            $('#fade, a.close').remove();  
    }); //fade them both out
        return false;
    });

});

