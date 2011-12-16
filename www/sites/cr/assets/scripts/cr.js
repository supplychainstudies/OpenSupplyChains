$(document).ready(function() {
 $("#contentoptions li").click(function() {
	if($(this).text() == "Sourcemap") {
		$("#overlay").css("display","block");
		$("#cotton").css("left","-50px");
		$("#cotton").css("background-image","url(assets/images/cotton-left.png)");
	} else {
		$("#cotton").css("left","825px");
		$("#cotton").css("background-image","url(assets/images/cotton-right.png)");		
	}
    $("iframe").addClass("hidden");
    $("iframe#"+$(this).attr("class")).removeClass("hidden");
 });
 $(".close").click(function() {
	$("#overlay").remove();
 });
});