jQuery(document).ready(function(){
    var $lefty = $("#info-pane");            
    
    $lefty.animate({
      left: parseInt($lefty.css('left'),10) == 0 ? -$lefty.outerWidth() : 0
    });
    $( "#map, #info-close" ).click(function() {
            var $lefty = $("#info-pane");            
            if($lefty.css("display") != "block") {
                $lefty.css("display","block");
            }
            $lefty.animate({
              left: parseInt($lefty.css('left'),10) == 0 ? -$lefty.outerWidth() : 0
            }, function() {
                if($lefty.css("display") == "block" && parseInt($lefty.css('left'),10) != 0) {
                    $lefty.css("display","none");
                } 
            });
    });

});


