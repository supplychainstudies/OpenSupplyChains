// Set up .messages div

if ($('.status-message').length > 0) {
    $('.status-messages').click(function(){
        $(this).fadeOut(0);
    });
    $('.status-messages').fadeIn(400);
}