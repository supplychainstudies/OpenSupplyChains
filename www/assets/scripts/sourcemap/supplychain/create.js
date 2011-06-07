$(document).ready(function(){
    // Set text inputs to inactive, create status & error boxes
    $('.form input[type="text"]')
        .addClass("inactive")
        .click(function(){
            $(this).removeClass("inactive");
        })
        .after("<div class='error' style='display:none'></div>");
    
    // Add asterisk to required fields
    $('.form input.required')
        .prev("label")
        .append('<span class="highlighted">*</span>')
        .next()
        .after("<div class='status inactive'></div>");

    // Disable submit until required fields are filled in 
    $(".form input[type='submit']").attr("disabled","disabled");

    var validated = function(){
        if ($('.status.inactive').length > 0){
            return false;
        }
        else {
            return true;
        }
    };
    
    // Enable submit on validation
    $(".form input.required").bind('keyup change', function(){
        if ($(this).val().length > 0){
            $(this).next().removeClass("inactive");    
        }
        else {
            $(this).next().addClass("inactive");
        }
        if (validated()){
            $(".form input[type='submit']").removeAttr("disabled");
        }
        else{
            $(".form input[type='submit']").attr("disabled","disabled");
        }
});
    

});

$('.form fieldset form').submit(function(e) {
    $(".form .error").hide();
 
    // Create new supplychain object
    var sc = new Sourcemap.Supplychain();
    sc.attributes.title = $("#title").val();
    sc.attributes.description = $("#desc").val();
    sc.attributes.tags = $("#tags").val();
    sc.category = $('#category').val();
    
    // Save the supplychain and retrieve its url using a custom callback
    var o = {};
    o.success = function(sc, scid, uri){
        document.location.href = "map/view/" + scid;
    } 
    Sourcemap.saveSupplychain(sc,o);

    return false;
});

