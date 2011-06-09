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

