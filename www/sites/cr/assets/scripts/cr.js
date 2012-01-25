$(document).ready(function() {

 $("#sourcemap-placeholder, .close").click(function() {
	$("#overlay").css("display", "none");
	$("#content-overlay").css("display", "none");	
	$("iframe#sourcemap").css("visibility", "visible");	
 });
 $(".trailer-link").click(function(e) {
	 e.stopPropagation();	 
	$("#overlay").css("display", "block");
	$("#content-overlay").css("display", "none");
	
 	$("#trailer").css("visibility","visible");	
	$("iframe#sourcemap").css("visibility", "hidden");	
 });
 $(".learnmore-link").click(function(e) {
	 e.stopPropagation();
  	$("#trailer").css("visibility","hidden");	
	$("#overlay").css("display", "none");	
	$("#content-overlay").css("display", "block");
	
	$("iframe#sourcemap").css("visibility", "hidden");	
 });
 
 $("#trailer").load(function() {
	 $("#trailer").css("visibility","visible");
 });
});