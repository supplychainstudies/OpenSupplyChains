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
 *
 */

/*$.fn.listenForChange = function(options) {
    settings = $.extend({
        interval: 200 // in microseconds
    }, options);

    var jquery_object = this;
    var current_focus = null;

    jquery_object.filter(":input").add(":input", jquery_object).focus( function() {
        current_focus = this;
    }).blur( function() {
        current_focus = null;
    });

    setInterval(function() {
        // allow
        jquery_object.filter(":input").add(":input", jquery_object).each(function() {
            // set data cache on element to input value if not yet set
            if ($(this).data('change_listener') == undefined) {
                $(this).data('change_listener', $(this).val());
                return;
            }
            // return if the value matches the cache
            if ($(this).data('change_listener') == $(this).val()) {
                return;
            }
            // ignore if element is in focus (since change event will fire on blur)
            if (this == current_focus) {
                return;
            }
            // if we make it here, manually fire the change event and set the new value
            $(this).trigger('change');
            $(this).data('change_listener', $(this).val());
        });
    }, settings.interval);
    return this;
};
*/

$(document).ready(function() {

   $('#form-description').before('<div id="desc-counter"></div>');
   $('.sourcemap-form textarea').keyup(function() {
        var maxlength = $(this).attr('maxlength');
        if(maxlength > 1) {
            var val = $(this).val();
            var lettersleft = maxlength - val.length;

            if (lettersleft == maxlength)
                $('#desc-counter').html('&nbsp;')
            else if(lettersleft>1)
                ('#desc-counter').text(lettersleft+' characters remaining');
            else if(lettersleft == 1)
                $('#desc-counter').text(lettersleft+' character remaining');
            else
                $('#desc-counter').text('No characters remaining');

            if (val.length > maxlength) {
              $(this).val(val.slice(0, maxlength));
            }
        }
    });
    loadAjaxForms();
});


function loadAjaxForms(){
    /* AJAX-y forms */
    // The goal is to give jQuery-enabled users access to a much better form submission experience.
    // Keep in mind that javascript breaks all the time, so our forms need to work regardless of this code.
    $('.sourcemap-form.ajax input[type=submit]').click(function(e){
        e.preventDefault ? e.preventDefault() : e.returnValue = false;
        $(this).attr('disabled', '');

        console.log($(this));
        form = $(this).parent();
        submitStatus = $(form).find('.submit-status');

        $(form).append('<input type="hidden" name="_form_ajax" value="true"></input>');
        $(submitStatus).empty().removeClass('failed').show().animate({height: 40});

        // get form attrs
        var action = $(form).attr('action')
        
        if ($(this).hasClass('stripe'))
            return false;

        // ajax validate
        $.post(action, $(form).serialize(), function(data){
            if (data.substring(0,8) === 'redirect'){
                var data = data.split(" ");
                window.location = data[1];
            } else {
                var success = false;
                $(submitStatus).addClass('text').append('<ul />');
                $(data).find('.status-message').each(function(){
                    if($(this).hasClass('success')){
                        success = true;
                    }
                    $(submitStatus).find('ul').append('<li>' + $(this).text().trim() + '</li>');
                });
                
                if(success)
                    $(submitStatus).addClass('succeeded');
                else
                    $(submitStatus).addClass('failed');

                // expand height to fit parent
                $(submitStatus).animate({
                    height : $(submitStatus).find('ul').height() + 14
                });

                if(typeof Recaptcha != "undefined")
                    Recaptcha.reload();
            }
        });
    });
}
