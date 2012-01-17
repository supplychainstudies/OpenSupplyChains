$(document).ready(function() {

 $(".placeholder, .close").click(function() {
	$("#overlay").css("display", "none");
	$("iframe#sourcemap").css("visibility", "visible");	
 });
 $("#trailer-link").click(function(e) {
	 e.stopPropagation();
	$("#overlay").css("display", "block");
	$("iframe#sourcemap").css("visibility", "hidden");	
 });
 $("#trailer").load(function() {
	 $("#trailer").css("visibility","visible");
 });
});