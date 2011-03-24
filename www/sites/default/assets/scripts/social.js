$("#password1").keyup(function(e) { changePasswordField(e); });
$("#password2").keyup(function(e) { changePasswordField(e); });

function changePasswordField(e) {
    var target = $(e.target);
    if(target.attr("type") == "text") {
	$('<input type="password" value="' + target.val() + '"/>').replaceAll(target);
    }
}