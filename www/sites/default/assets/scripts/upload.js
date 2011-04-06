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

function include(key)
{
 
  var script  = document.createElement('script');
  script.src  = "https://spreadsheets.google.com/feeds/cells/"+key+"/od6/public/basic?alt=json&callback=myFunc";
  script.type = 'text/javascript';
  script.defer = true;
 
  document.getElementsByTagName('head').item(0).appendChild(script);
 
}
