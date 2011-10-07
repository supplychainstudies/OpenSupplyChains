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
 * program. If not, see <http://www.gnu.org/licenses/>.
 */



$(document).ready(function(){

    /* unsupported browser detection */
    if (($.browser.msie)) {
        
        //document.getElementById("status-message").className = "status-message browser";
        $(".messages").html(
            "<div class=\"status-message\" "
            +" style=\"padding:10px 20px 10px 20px;background:#3884ab;line-height:1.5em;\">"
            +"<div class=\"browser\" "
            +" style=\"font-size:20px;color:#999;padding:20px 20px 40px 20px;background:#eee;\"></div></div>"        
        );
        $(".browser").html(
            " <h1><font color=\"#333\">Dear IE user,</font></h1>"
          + " You're currently using a browser that is unsupported by Sourcemap." 
          + " While we won't stop you from experimenting, we highly recommend using"
          + " a recent version of "
          + " <a href=\"http://www.google.com/chrome\">Chrome</a>,"
          + " <a href=\"http://www.apple.com/safari/\">Safari</a>, or "
          + " <a href=\"http://www.mozilla.org/en-US/firefox/new/\">Firefox</a>."
        );
    }

    /* status message fade */
    if ($('.status-message').length > 0) {
        $('.status-messages').click(function(){
            $(this).fadeOut(0);
        });
        $('.status-messages').fadeIn(400);
    }

    /* modal window fxn courtesy http://www.sohtanaka.com/web-design/examples/modal-window/ */
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

    Sourcemap.truncate_string(".preview-title");

}); // end of document ready


function DisableOption(input)
{
    // Reest disable function
    $("OPTION").removeAttr("disabled");

    // Set selected value not available to others
    if(input!="0")
        $("OPTION[value="+input+"]").attr('disabled', "disabled");
}

function beforeSubmit()
{
    $("OPTION").removeAttr("disabled");    
    return true;
}

