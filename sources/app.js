window.onload = function(){



	function getCookie(cname) {
		console.log("running getCookie");
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i = 0; i < ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	        }
	        if (c.indexOf(name) == 0) {
	            return c.substring(name.length, c.length);
	        }
	    }
	    return "";
	}

	var checkCookie = function(){
		console.log("running check check check cookie");
		var is18 = getCookie("eighteen=true");
	 	if(!is18){
	 		 $("#eighteen-modal").modal("show");
 		}
	}

	checkCookie();

	console.log("kinky shit!!")

	$( "#over-18" ).click(function() {
	  console.log("Setting a cookie");
	  document.cookie = "eighteen=true";
	});
}
