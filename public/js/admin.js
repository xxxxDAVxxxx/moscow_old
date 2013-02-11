$(document).ready(function() {	
	URL = 'http://moscow.atplus.com.ua/';
	
    function strip_tags( str ){ // Strip HTML and PHP tags from a string
		return str.replace(/<\/?[^>]+>/gi, '');
	};
	
	var url = document.location.pathname.toString();
	var urli = url.split("/");
	
    if((urli[2] == 'banner')) {

    };
});  