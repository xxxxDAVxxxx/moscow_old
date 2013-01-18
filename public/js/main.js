function maxSizeForIE(jQSelect,maxWidth,maxHeight){
	if (navigator.appName == "Microsoft Internet Explorer") {
		jQSelect.width()==maxWidth ? jQSelect.width("") : {};
		jQSelect.height()==maxHeight ? jQSelect.height("") : {};
		if(jQSelect.width()>maxWidth || jQSelect.height()>maxHeight){
			(jQSelect.width()/maxWidth)>(jQSelect.height()/maxHeight)?jQSelect.width(maxWidth+"px"):jQSelect.height(maxHeight+"px");
		}
	}
	//jQSelect.width()>maxWidth ? jQSelect.width(maxWidth+"px") : jQSelect.width("");
	//jQSelect.height()>maxHeight ? jQSelect.height(maxHeight+"px") : jQSelect.height("");
	//$("#view_project_img").width()>640 ? $("#view_project_img").width(640+"px") : $("#view_project_img").width("");
}

$(document).ready(function() { 
	URL = 'http://moscow.atplus.com.ua/';
	
	$('#registration-link').click(function(){
		$('#registration').css('display','block');
		$('#registration-bg').css('display','block');
	});
	$('#registration-cancel').click(function(){
		$('#registration').css('display','none');
		$('#registration-bg').css('display','none');
	});
	
	$('#registration-submit').click(function(){
		/*
		var name = $("input#company-name").val();
	    if (name == "") {   
	    	$("input#company-name").focus();
	    	$("input#company-name").prev(".registration-caption").append('<br><span class="reg-error">поле, обязательно к заполнению</span>');
	    	return false;  
	    }
	    var pass = $("input#company-pass").val();
	    if (pass == "") {   
	    	$("input#company-pass").focus();
	    	$("input#company-pass").prev(".registration-caption").append('<br><span class="reg-error">поле, обязательно к заполнению</span>');
	    	return false;  
	    }
	    var passConfirm = $("input#company-pass-confirm").val();
	    if (passConfirm == "") {   
	    	$("input#company-pass-confirm").focus();
	    	$("input#company-pass-confirm").prev(".registration-caption").append('<br><span class="reg-error">поле, обязательно к заполнению</span>');
	    	return false;  
	    }
	    var email = $("input#company-email").val();
	    if (email == "") {   
	    	$("input#company-email").focus();
	    	$("input#company-email").prev(".registration-caption").append('<br><span class="reg-error">поле, обязательно к заполнению</span>');
	    	return false;  
	    }
	    var person = $("input#responsible-person").val();
	    if (person == "") {   
	    	$("input#responsible-person").focus();
	    	$("input#responsible-person").prev(".registration-caption").append('<br><span class="reg-error">поле, обязательно к заполнению</span>');
	    	return false;  
	    }*/
		$(".reg-error").html("");
		var error=false;
	    $("input#company-name," +
	    		"input#company-pass," +
	    		"input#company-pass-confirm," +
	    		"input#company-email," +
	    		"input#responsible-person").each(function(){
	    	if($(this).val()==""){
	    		$(this).prev(".registration-caption").children(".reg-error").html('поле, обязательно к заполнению');
	    		error=true;
	    	}
		});
	    if(error){
	    	return false;
	    }

	    var pass = $("input#company-pass").val();
	    var passConfirm = $("input#company-pass-confirm").val();
	    
	    if(pass!=passConfirm){
	    	$(	"input#company-pass," +
	    		"input#company-pass-confirm").focus().prev(".registration-caption")
	    									.children(".reg-error")
	    									.html('пароли не совпадают');
    		error=true;
	    }
	    
	    var email = $("input#company-email").val();
	    var regEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	    if (regEmail.test(email) == false) {  
	    	$("input#company-email").focus().prev(".registration-caption")
    									.children(".reg-error")
    									.html('некорректный email');
	    	error=true; 
	    }  
	    if(!$("input#license-check").attr("checked")){
	    	$("input#license-check").parent().children(".reg-error").html("ознакомьтесь с лицензионным соглашением");
	    	error=true;
	    }
	    if(error){
	    	return false;
	    }
	    var name = $("input#company-name").val();
	    var person = $("input#responsible-person").val();
	    var desc = $("textarea#company-desc").val();
	    
	    var site = $("input#site").val();
	    var phone = $("input#company-phone").val();
	    var skype = $("input#company-skype").val();
	    var icq = $("input#company-icq").val();
	    
	    $.ajax({
			dataType: 'jsonp'	
			, url: URL+'auth/register'
			, data: {
				'name': encodeURIComponent(name),
				'password': encodeURIComponent(pass),
				'email': encodeURIComponent(email), 
				'desc': encodeURIComponent(desc), 
				'person': encodeURIComponent(person),
				'site': encodeURIComponent(site),
				'phone': encodeURIComponent(phone),
				'skype': encodeURIComponent(skype),
				'icq': encodeURIComponent(icq)
	        }
	        , success: function(response) {
	        	if (response.success) {
	        		//alert("success");
	        		//$('#form_link').hide();
		        	//$('#js-mail-result').html("Спасибо, Ваше письмо успешно отправлено!");
		        	//$('#js-mail-result').show();
	        	}
	        	else {
	        		$(".registration-form-title").children(".reg-error").html(response.error);
	        		//alert("error");
	        		//$('#js-mail-result').html("Ошибка!");
	        		//$('#js-mail-result').show();
	        	}
	        }			
		});
	});
	$('#auth-enter').click(function(){
		
		var error=false;
	    $("input#auth-login," +
	    		"input#auth-password").each(function(){
	    	if($(this).val()==""){
	    		$(this).focus();
	    		error=true;
	    	}
		});
	    if(error){
	    	return false;
	    }
		
	    var email=$("input#auth-login").val();
	    var pass=$("input#auth-password").val();
	    
		$.ajax({
			dataType: 'jsonp'	
			, url: URL+'auth/login'
			, data: {
				'email': encodeURIComponent(email),
				'password': encodeURIComponent(pass)
	        }
	        , success: function(response) {
	        	if (response.success) {
	        		window.location.href = window.location.href;
	        		//alert("success");
	        		//$('#form_link').hide();
		        	//$('#js-mail-result').html("Спасибо, Ваше письмо успешно отправлено!");
		        	//$('#js-mail-result').show();
	        	}
	        	else {
	        		alert("error");
	        		//$('#js-mail-result').html("Ошибка!");
	        		//$('#js-mail-result').show();
	        	}
	        }			
		});
	});
	$('#logined-logout').click(function(){
		$.ajax({
			dataType: 'jsonp'	
			, url: URL+'auth/logout'
	        , success: function(response) {
	        	if (response.success) {
	        		window.location.href = window.location.href;
	        	}
	        	else {
	        		alert("error");
	        	}
	        }			
		});
	});
});