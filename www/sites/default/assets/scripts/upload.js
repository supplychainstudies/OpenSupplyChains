$('#submit').click(function() {
	var key = $('#key').val();
        var a = "";
   $.getJSON("https://spreadsheets.google.com/feeds/cells/"+key+"/od6/public/basic?alt=json-in-script&callback=?", function(data){
              a = data;
		 
	     });
								  
});







