window.onload = function(){


	// getting the cookie
	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i = 0; i < ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	        }
	        if (c.indexOf(name) == 0) {
	        	var foo = c.substring(name.length, c.length);
	            return foo;
	        }
	    }
	    return "";
	}
	//checking to see if the right cookie exists
	var checkCookie = function(){
		var is18 = getCookie("eighteen");
	 	if(!is18){
	 		 $("#eighteen-modal").modal("show");
 		}
	}

	checkCookie();

	// Sets a cookie for the page
	// if the person says they're 18
	$( "#over-18" ).click(function() {
	  document.cookie = "eighteen=true;";
	});
	$('#more').click(function () {
		$('#bit').toggleClass('expanded');
		return false;
	});

	$("#gallery").unitegallery({
		slider_control_zoom: false,
		gallery_height: 400,
		theme_enable_text_panel: false
	});
};
