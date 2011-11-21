Stripe.setPublishableKey('pk_8JAyhmjBKpJz9oZQPgs7dfAEfirlX');

$(document).ready(function() {
  $(".sourcemap-form form").submit(function(event) {
    // disable the submit button to prevent repeated clicks
    $('.submit-button').attr("disabled", "disabled");

    var amount = 9995; //amount you want to charge in cents
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
});

function stripeResponseHandler(status, response) {
    if (response.error) {
        $(".payment-errors").html(response.error.message);
    } else {
        var form$ = $(".sourcemap-form form");
        // token contains id, last4, and card type
        var token = response['id'];
        // insert the token into the form so it gets submitted to the server
        form$.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        // and submit
        form$.get(0).submit();
    }
}
