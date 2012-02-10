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

$(document).ready(function() {
   
   // Disable create form attrs on "replace a map" selection
   var replace_into = $('[name="replace_into"]');
   var elements = $('.sourcemap-form input:not([type="submit"]), .sourcemap-form textarea');
   replace_into.change(function() {
       // Not sure why .selectedIndex doesn't work here...
       if (replace_into.find('option:selected').text() == replace_into.find('option:eq(0)').text()){
           elements.each(function(){ $(this).removeAttr('disabled'); });
           $('.sourcemap-form input[type="submit"]').val("Create");
           $('.sourcemap-form form input[name="replace_into"]').remove();
       } else {
           elements.each(function(){ $(this).attr('disabled', 'disabled'); });
           $('.sourcemap-form input[type="submit"]').val("Update");
           hidden_element = '<input name="replace_into" value="' + replace_into.find('option:selected').val() + '">';
           $(hidden_element).hide().appendTo($('.sourcemap-form form'));
       }
   });
    
   $('select[name="replace_into"]').hide();
   $('[name="file_front"]').click(function() {
        $('[name="file"]').click();
    });	
   $('[name="file"]').change(function() {
       $('select[name="replace_into"]').show();
		var filename = $('[name="file"]').val();
		var parts = filename.split("\\");
		var filetype = parts[parts.length-1].split(".");
		if (filetype[filetype.length-1] == "xls") {
			$('[name="file_front"]').val(parts[parts.length-1]);
			$('.sourcemap-form form').append($('[name="file"]'));
		} else {
			$('[name="file_front"]').val("File not supported...");
			$('.sourcemap-form form').attr('action','/create');
			$('[name="file"]').val("");
		}
        replace_into.trigger("change");
    });
   
   $('#form-description').before('<div id="desc-counter"></div>');
   $('.sourcemap-form textarea').keyup(function() {
        var maxlength = $(this).attr('maxlength');
        if(maxlength > 1) {
            var val = $(this).val();
            var lettersleft = maxlength - val.length;

            if (lettersleft == maxlength)
                $('#desc-counter').html('&nbsp;')
            else if(lettersleft>1)
                $('#desc-counter').text(lettersleft+' characters remaining');
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
        submitButton = $(this);
        $(submitButton).attr('disabled', '').addClass('disabled');

        form = $(this).parent();
        submitStatus = $(form).find('.submit-status');

        submitStatus.addClass("active");
        $(form).append('<input type="hidden" name="_form_ajax" value="true"></input>');
        $(submitStatus).empty().removeClass('failed').show().animate({height: 40});

        // get form attrs
        var action = $(form).attr('action')
        
        if ($(this).hasClass('stripe'))
            return false;

        // ajax validate
        $.post(action, $(form).serialize(), function(data){
            submitStatus.removeClass('active');
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
                else{
                    $(submitStatus).addClass('failed');
                    $(submitButton).removeAttr('disabled', '').removeClass('disabled');
                }

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
