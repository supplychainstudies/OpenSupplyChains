Sourcemap.checkusername_url = "services/users/get/";

$("#password1").keyup(function(e) { changePasswordField(e); });
$("#password2").keyup(function(e) { changePasswordField(e); });

function changePasswordField(e) {
    var target = $(e.target);
    if(target.attr("type") == "text") {
	$('<input type="password" value="' + target.val() + '"/>').replaceAll(target);
    }
}

$("#username").change(function(e) {
     var username = $(e.target).val();	
     var url = Sourcemap.checkusername_url + username;
     $.get('services/users/'+username, function(data) { 
	       if (data == "not found") {
		   $("#available_or_not").html("Available");
	       } else {
		   $("#available_or_not").html("Unavailable");
	       }
    	   });

});
