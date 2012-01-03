$(document).ready(function() {
    loadAjaxStripe();
});

function loadAjaxStripe(){
  $('.sourcemap-form.ajax.stripe input[type=submit]').click(function(e){
    var amount = 9900; //amount you want to charge in cents
    Stripe.createToken({
        name: $('input[name="card-name"]').val(),
        number: $('input[name="card-number"]').val(),
        cvc: $('input[name="card-cvc"]').val(),
        exp_month: $('input[name="card-expiry-month"]').val(),
        exp_year: $('input[name="card-expiry-year"]').val()
    }, amount, stripeResponseHandler);
   
    // prevent the form from submitting with the default action
    return false;
  });
}

function stripeResponseHandler(status, response) {
    if (response.error) {
        
        submitStatus = $(form).find('.submit-status');
        var success = false;
        switch (response.error.message){
            case "exp_year should be an int (is )":
                response.error.message = "Please enter an expiration year" 
            case "exp_month should be an int (is )":
                response.error.message = "Please enter an expiration month"
            default:
        }

        $(submitStatus)
            .addClass('text')
            .append('<ul><li>' + response.error.message + '</li></ul>')
            .addClass('failed');

        // expand height to fit parent
        $(submitStatus).animate({
            height : $(submitStatus).find('ul').height() + 14
        });
    
    } else {
        // we've passed the CC validation.  time for normal validation.
        // remove class that keeps it from submitting
        var form$ = $(".sourcemap-form.stripe");
        form$.removeClass('stripe');
        
        // token contains id, last4, and card type
        var token = response['id'];
        // insert the token into the form so it gets submitted to the server
        $('.sourcemap-form.ajax input[type=submit]').after("<input type='hidden' name='stripeToken' value='" + token + "'/>");
  
        // submit!
        var action = $(form).attr('action');
        $.post(action, $(form).serialize(), function(data){
            $(submitStatus).empty();
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

                var failure = false;

                if(success)
                    $(submitStatus).addClass('succeeded');
                else{
                    // failed.  back to square one...
                    $(submitStatus).addClass('failed');
                    failure = true;
                }
                
                if (failure){
                    form$.addClass('stripe');
                    loadAjaxStripe();
                    $(form).find('input[name="stripeToken"]').remove();
                }

                // expand height to fit parent
                $(submitStatus).animate({
                    height : $(submitStatus).find('ul').height() + 14
                });
            }
        });

    }
}
