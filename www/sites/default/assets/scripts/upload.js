$('#submit').click(function() {
	var key = $('#key').val();

         $.ajax({url : "https://spreadsheets.google.com/feeds/cells/"+key+"/od6/public/basic?alt=json",
		 success : myFunc
		});
});


 var a = "";
     var myFunc = function(resp){
	 a = eval(resp);
};


