/* Author: 
<link rel="shortcut icon" href="../images/favicon.ico">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

*/


function toggleDetailPane() {
    var left = (parseInt($("#detail-pane").css('left'),10) == 5 ? 220 : 5);
    $("#detail-pane").css("left", left);
    var width = (parseInt($("#detail-pane").css('width'),10) == 200 ? 350 : 200);
    $("#detail-pane").css("width", width);
}

jQuery(document).ready(function(){

    $(".place-details, #detail-pane .close").click(function() {toggleDetailPane();});
    $("#fullscreen-button").click(function() {
        if($("#map").css("position") == "fixed") {
            $("#map").css("z-index", "").css("position","static").css("height","600px")
                     .css("left","").css("top","").css("width","100%")
                     .css("-webkit-box-shadow","inset 5px 5px 5px rgba(0, 0, 0, 0.2)");
            $("#map").css("width",(parseInt($("#map").css('width'),10)-221));
        } else {
            $("#map").css("z-index", 100).css("position","fixed").css("width","100%")
                     .css("height","100%").css("left",0).css("top",0).css("margin",0)
                     .css("-webkit-box-shadow","none");
        }
    });
});


















