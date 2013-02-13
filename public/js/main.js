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
	
	$('#profile-edit-personal-submit').click(function(){
		$('#registration').css('display','block');
		$('#registration-bg').css('display','block');
	});
	
	$('img#refresh').click(function(){  
		change_captcha();
	});
 
	$('#auth-forgot-pass').click(function(){
		$('#forgot-pass-messbox').css('display','block');
		$('#registration-bg').css('display','block');
		$('#forgot-pass-submit-mess').css('display','none');
	});
	$('#forgot-pass-messbox-cancel').click(function(){
		$('#forgot-pass-messbox').css('display','none');
		$('#registration-bg').css('display','none');
	});
	
	$('#company-name.uniq-company-registr').blur(function(){
		if($('#company-name').val()!=""){
			$('input#company-name').prev(".registration-caption")
			.children(".reg-error")
			.html('');
			$.ajax({
				dataType: 'jsonp'	
				, url: URL+'auth/checkuniqcompany'
				, data: {
					'name': encodeURIComponent($('input#company-name').val())
		        }
		        , success: function(response) {
		        	if (response.success) {
	
		        	}
		        	else {
		        		$('#company-name').prev(".registration-caption")
						.children(".reg-error")
						.html(response.error);
		        	}
		        }			
			});
		}
	});
	
	$('#company-name.uniq-company-profile').blur(function(){
		if($('#company-name.uniq-company-profile').val() != ''){
			$('input#company-name').prev(".registration-caption")
			.children(".reg-error")
			.html('');
			if($('#company-name').val()!=""){
				var cid = $('#cid').val()
				$.ajax({
					dataType: 'jsonp'	
					, url: URL+'auth/checkuniqcompany'
					, data: {
						'name': encodeURIComponent($('input#company-name').val()),
						'cid':cid
			        }
			        , success: function(response) {
			        	if (response.success) {
		
			        	}
			        	else {
			        		$('#company-name').prev(".registration-caption")
							.children(".reg-error")
							.html(response.error);
			        	}
			        }			
				});
			}
		}
	});
	
	$('input#company-email').blur(function(){
		if($('input#company-email').val() != ''){
			$('input#company-email').prev(".registration-caption")
			.children(".reg-error")
			.html('');
			var email = $("input#company-email").val();
		    var regEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			if((email != "") && (regEmail.test(email))){
				$.ajax({
					dataType: 'jsonp'	
					, url: URL+'auth/checkuniqemail'
					, data: {
						'email':$('input#company-email').val()
			        }
			        , success: function(response) {
			        	if (!response.success) {
			        		$('input#company-email').prev(".registration-caption")
							.children(".reg-error")
							.html(response.error);
			        	}
			        }			
				});
			}
		}
	});	
	
	function change_captcha(){
		document.getElementById('captcha').src=URL+"captcha/get_captcha.php?rnd=" + Math.random();
		$("#code").val("");
	}
	$('#registration-submit').click(function(){
		
		$(".reg-error").html("");
		var error=false;
	    
		$(	"input#company-pass-confirm," +
			"input#company-pass," +
			"input#responsible-person," +
			"input#company-name," +
			"input#company-email"
	    ).each(function(){
	    	if($(this).val()==""){
	    		if(!error){
	    			$(this).focus();
	    		}
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
	    	$('input#company-pass').focus();
    		error=true;
	    }
	    
	    var email = $("input#company-email").val();
	    
	   	var regEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	    if (regEmail.test(email) == false) {  
	    	$("input#company-email").focus().prev(".registration-caption")
    									.children(".reg-error")
    									.html('некорректный email');
	    	if(!error){
	    		$("input#company-email").focus();
    		}
	    	error=true; 
	    }  
	    if(!$("input#license-check").attr("checked")){
	    	$("input#license-check").parent().children(".reg-error").html("ознакомьтесь с лицензионным соглашением");
	    	if(!error){
	    		$("input#license-check").focus();
    		}
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
	    
	 // check captcha
		$.post(URL + "checkcaptcha?"+$("#code").val($("#code").val().toLowerCase()).serialize(), function(response){
			if (response==1)
			{
				$('#registration-bg').css('cursor','wait');
				var zI = $('#registration-bg').css('z-index');
		    	$('#registration-bg').css('z-index','10');
				$.ajax({
					dataType: 'jsonp'	
					, url: URL+'auth/register'
					, data: {
							'name': encodeURIComponent(name),
							'password': encodeURIComponent(pass),
							'passconfirm': encodeURIComponent(passConfirm),
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
			        		window.location.href = window.location.href;
			        		//$('#registration-bg').css('z-index',zI);
			        		$('#registration-bg').css('cursor','default');
			        	}
			        	else {
			        		$('#registration-bg').css('z-index',zI);
			        		$('#registration-bg').css('cursor','default');
			        		if(typeof(response.error) == 'object'){
			        			$('input#company-name').prev(".registration-caption").children(".reg-error").html(response.error.name);
			        			$('input#company-pass').prev(".registration-caption").children(".reg-error").html(response.error.password);
			        			$('input#company-pass-confirm').prev(".registration-caption").children(".reg-error").html(response.error.passconfirm);
			        			$('input#company-email').prev(".registration-caption").children(".reg-error").html(response.error.email);
			        			$('input#responsible-person').prev(".registration-caption").children(".reg-error").html(response.error.person);
			        		}else{
			        			$(".registration-form-title").children(".reg-error").html(response.error);
			        		}
			        	}
			        }			
				});
				change_captcha();
				//$("input#js-name").val('');
				//$("input#js-email").val('');
				//$("input#js-phone").val('');
				//$("textarea#js-mess").val('');
				//$("input#code").val('');
			}
			else
			{	
				$("div#wrap .reg-error").html("Строки не совпадают!");
		    	//alert("yo");
				//$('#js-mail-result').html("Пожалуйста, проверьте, правильно ли введены символы с картинки?");
        		//$('#js-mail-result').show();
				//$("#after_submit").html('');
				//$("#submit-button").after('<label class="error" id="after_submit">Error ! invalid captcha code .</label>');
			}
			
		});
	});
	
	$('#profile-edit-submit').click(function(){
		
		$(".reg-error").html("");
	/*	var error=false;
	    
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
	*/	
	    var pass = $("input#company-pass").val();
	    var passConfirm = $("input#company-pass-confirm").val();
	 /*   
	    if(pass!=passConfirm){
	    	$(	"input#company-pass," +
	    		"input#company-pass-confirm").focus().prev(".registration-caption")
	    									.children(".reg-error")
	    									.html('пароли не совпадают');
    		error=true;
	    }
	  */ 
	    var email = $("input#company-email").val();
	  /*  
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
	  */
	    var name = $("input#company-name").val();
	    var person = $("input#responsible-person").val();
	    var desc = $("textarea#company-desc").val();
	    
	    var site = $("input#site").val();
	    var phone = $("input#company-phone").val();
	    var skype = $("input#company-skype").val();
	    var icq = $("input#company-icq").val();
	    var cid = $("input#cid").val();
	    
		$('#registration-bg').css('cursor','wait');
		var zI = $('#registration-bg').css('z-index');
		$('#registration-bg').css('z-index','10');
		
		$.ajax({
					dataType: 'jsonp'	
					, url: URL+'auth/register'
					, data: {
							'name': encodeURIComponent(name),
							'password': encodeURIComponent(pass),
							'passconfirm': encodeURIComponent(passConfirm),
							'email': encodeURIComponent(email), 
							'desc': encodeURIComponent(desc), 
							'person': encodeURIComponent(person),
							'site': encodeURIComponent(site),
							'phone': encodeURIComponent(phone),
							'skype': encodeURIComponent(skype),
							'icq': encodeURIComponent(icq),
							'cid': cid
				    }
			        , success: function(response) {
			        	if (response.success) {
			        		window.location.href = window.location.href;
			        		//$('#registration-bg').css('z-index',zI);
			        		$('#registration-bg').css('cursor','default');
			        	}
			        	else {
			        		$('#registration-bg').css('z-index',zI);
			        		$('#registration-bg').css('cursor','default');
			        		if(typeof(response.error) == 'object'){
			        			$('input#company-pass-confirm').prev(".registration-caption").children(".reg-error").html(response.error.passconfirm);
			        			$('input#company-pass').prev(".registration-caption").children(".reg-error").html(response.error.password);
			        			$('input#responsible-person').prev(".registration-caption").children(".reg-error").html(response.error.person);
			        			$('input#company-name').focus().prev(".registration-caption").children(".reg-error").html(response.error.name);
			        			$('input#company-email').prev(".registration-caption").children(".reg-error").html(response.error.email);
			        			}else{
			        			$(".registration-form-title").children(".reg-error").html(response.error);
			        		}
			        	}
			        }			
		
				//change_captcha();
				//$("input#js-name").val('');
				//$("input#js-email").val('');
				//$("input#js-phone").val('');
				//$("textarea#js-mess").val('');
				//$("input#code").val('');
		});
	});
	
	$('#forgot-pass-submit').click(function(){
		$("#forgot-pass-messbox-center").children(".reg-error").html("");
		var email = $('#company-forgot-pass').val();
		var regEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	    if(regEmail.test(email)){
	    	var zI = $('#registration-bg').css('z-index');
	    	$('#registration-bg').css('z-index','10');
	    	$('body').css('cursor','wait');
			$.ajax({
				dataType: 'jsonp'	
				, url: URL+'auth/sendpassrecover'
				, data: {
					'email': email
		        }
		        , success: function(response) {
		        	if (response.success) {
		        		$('#registration-bg').css('z-index',zI);
		        		$('#forgot-pass-submit-mess').css("display","block");
		        		//window.location.href = window.location.href;
		        		//alert("success");
		        		//$('#form_link').hide();
			        	//$('#js-mail-result').html("Спасибо, Ваше письмо успешно отправлено!");
			        	//$('#js-mail-result').show();
		        	}
		        	else {
		        		$('#registration-bg').css('z-index',zI);
		        		if(response.error=="Invalid data"){
		        			$("#forgot-pass-messbox-center").children(".reg-error").html("Пользователь с таким email адресом не зарегистрирован.");
		        		}
		        		
		        		//alert("error");
		        		//$('#js-mail-result').html("Ошибка!");
		        		//$('#js-mail-result').show();
		        	}
		        	$('body').css('cursor','default');
		        }			
			});
		
	    }else{
	    	$("#forgot-pass-messbox-center").children(".reg-error").html("Введите корректный email адрес!");
	    }
	    
	});
	
	$('#company-forgot-pass').keydown(function(event){
		if(event.keyCode==13){// нажата клавиша enter
			$("#forgot-pass-submit").trigger('click');
		}
	});
	
	$('body').keydown(function(event){
		if(event.keyCode==27){// нажата клавиша Esc
			//$("#forgot-pass-submit").trigger('click');
			$('#registration-bg').css('display','none');
			$('#forgot-pass-messbox').css('display','none');
			$('#registration').css('display','none');
		}
	});
	
	$('#auth-enter').click(function(){
		
		var error=false;
	    $("input#auth-login," +
	    		"input#auth-password").each(function(){
	    	if($(this).val()==""){
	    		$(this).focus();
	    		return false;
	    	}
		});
	    var email=$("input#auth-login").val();
	    var regEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	    if (regEmail.test(email) == false) { 
	    	$("#auth").children(".reg-error").html("Неккоректный email адрес!");
	    	return false;
	    }  
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
	        		if(response.error=="User not found"){
	        			$("#auth").children(".reg-error").html("Неверные данные!");
	        		}
	        		//alert("error");
	        		//$('#js-mail-result').html("Ошибка!");
	        		//$('#js-mail-result').show();
	        	}
	        }			
		});
	});
	
	$('#auth-login').keydown(function(event){
		if(event.keyCode==13){// нажата клавиша enter
			$("#auth-enter").trigger('click');
		}
	});
	
	$('#auth-password').keydown(function(event){
		if(event.keyCode==13){// нажата клавиша enter
			$("#auth-enter").trigger('click');
		}
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
	        		//alert("error");
	        	}
	        }			
		});
	});
	
	$('#not-active-submit').click(function(){
		$('#not-active-submit-mess').css("display","none");
		$("#auth").children(".reg-error").html("");
		var email = $('#not-active-email').val();
		var regEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	    if(regEmail.test(email)){
	    	//var zI = $('#registration-bg').css('z-index');
	    	//$('#registration-bg').css('z-index','10');
	    	$('body').css('cursor','wait');
			$.ajax({
				dataType: 'jsonp'	
				, url: URL+'auth/sendactivation'
				, data: {
					'email': email,
					'person': encodeURIComponent($('#not-active-user').html())
		        }
		        , success: function(response) {
		        	if (response.success) {
		        		$('body').css('cursor','default');
		        		$('#not-active-submit-mess').css("display","block");
		        		//window.location.href = window.location.href;
		        		//alert("success");
		        		//$('#form_link').hide();
			        	//$('#js-mail-result').html("Спасибо, Ваше письмо успешно отправлено!");
			        	//$('#js-mail-result').show();
		        	}
		        	else {
		        		
		        		$('body').css('cursor','default');
		        		if(response.error=="User not found"){
		        			$("#auth").children(".reg-error").html("Пользователь с таким email адресом не зарегистрирован.");
		        		} else {
		        			$("#auth").children(".reg-error").html(response.error);
		        		}
		        		//alert("error");
		        		//$('#js-mail-result').html("Ошибка!");
		        		//$('#js-mail-result').show();
		        	}
		        }			
			});
		
	    }else{
	    	$("#auth").children(".reg-error").html("Не корректный email адрес!");
	    }	
	});
	
	$('#not-active-email').keydown(function(event){
		if(event.keyCode==13){// нажата клавиша enter
			$("#not-active-submit").trigger('click');
		}
	});
	
	// create new object
	$('#js-object-create-submit').click(function(){
		$('#center').children(".reg-error").html('');
		var name = $('#js-object-name').val();
		var location = $('#js-object-location').val();
		var error = false;
		if (location == '') {
			$('#js-object-location').focus();
			$('#js-object-location').next().next(".reg-error").html('Поле обязательно к заполнению!');
			error=true;
		}
		if (name == '') {
			$('#js-object-name').focus();
			$("#js-object-name").next().next(".reg-error").html('Поле обязательно к заполнению!');
			error=true;
		}
		if(error){
			return;
		}
		
		var desc = $('#js-object-desc').val();
		var type = $('#js-object-type').val();
		// photo param
		var video = $('#js-object-video').val();
		var square = $('#js-object-square').val();
		var floors = $('#js-object-floors').val();
		var qualityClass = $('#js-object-class option:selected').val();
		var year = $('#js-object-year option:selected').val();
		var remoteness = $('#js-object-metro-remote').val();
		var arendators = $('#js-object-arendators').val();
		//var cid = 57;

		
		$.ajax({
			dataType: 'jsonp'	
			, url: URL + 'objects/create'
			, data: {
				object:{
					'name': encodeURIComponent(name),
					'desc': encodeURIComponent(desc),
					'location': encodeURIComponent(location),
					'type': type,
					'photo': null,
					'video': video,
					'square': square,
					'floors': floors,
					'class': qualityClass,
					'year': year,
					'remoteness': remoteness,
					'arendators': encodeURIComponent(arendators)
				}
	        }
	        , success: function(response) {
	        	if(response.success) {
	        		//$('body').css('cursor','default');
	        		//$('#not-active-submit-mess').css("display","block");
	        		//window.location.href = window.location.href;
	        		//alert("success");
	        		//$('#form_link').hide();
		        	//$('#js-mail-result').html("Спасибо, Ваше письмо успешно отправлено!");
		        	//$('#js-mail-result').show();
	        	}else{
	        		if(typeof(response.error) == 'object'){
	        			
	        			$('#js-object-floors').next().next(".reg-error").html(response.error.floors);
	        			$('#js-object-square').next().next(".reg-error").html(response.error.square);
	        			if(response.error.floors || response.error.square){
	        				$("html,body").animate({scrollTop: $('#js-object-square').offset().top - 100}, 5);
	        			}
	        			
	        			$('#js-object-location').next().next(".reg-error").html(response.error.location);
	        			$("#js-object-name").next().next(".reg-error").html(response.error.name);
	        			if(response.error.location || response.error.name){
	        				$("html,body").animate({scrollTop: $('#js-object-name').offset().top - 100}, 5);
	        			}
	        			//$('input#company-pass').prev(".registration-caption").children(".reg-error").html(response.error.password);
	        			//$('input#company-pass-confirm').prev(".registration-caption").children(".reg-error").html(response.error.passconfirm);
	        			//$('input#company-email').prev(".registration-caption").children(".reg-error").html(response.error.email);
	        			//$('input#responsible-person').prev(".registration-caption").children(".reg-error").html(response.error.person);
	        		}else{
	        			$("#room-create-caption").children(".reg-error").html(response.error);
	        			$("html,body").animate({scrollTop: $('#room-create-caption').offset().top - 100}, 5);
	        		}
	        	}
	        }			
		});
		
	});
	     
	// update object
	$('#js-object-update-submit').click(function(){
		$('#center').children(".reg-error").html('');
		var oid = $('#object-id').val();
		if(oid==''){
			return;
		}
		var name = $('#js-object-name').val();
		var location = $('#js-object-location').val();
		var error = false;
		if (location == '') {
			$('#js-object-location').focus();
			$('#js-object-location').next().next(".reg-error").html('Поле обязательно к заполнению!');
			error=true;
		}
		if (name == '') {
			$('#js-object-name').focus();
			$("#js-object-name").next().next(".reg-error").html('Поле обязательно к заполнению!');
			error=true;
		}
		if(error){
			return;
		}
		
		var desc = $('#js-object-desc').val();
		var type = $('#js-object-type').val();
		// photo param
		var video = $('#js-object-video').val();
		var square = $('#js-object-square').val();
		var floors = $('#js-object-floors').val();
		var qualityClass = $('#js-object-class option:selected').val();
		var year = $('#js-object-year option:selected').val();
		var remoteness = $('#js-object-metro-remote').val();
		var arendators = $('#js-object-arendators').val();
		
		$.ajax({
			dataType: 'jsonp'	
			, url: URL+'objects/create'
			, data: {
				object:{
					'name': encodeURIComponent(name),
					'desc': encodeURIComponent(desc),
					'location': encodeURIComponent(location),
					'type': type,
					'photo': null,
					'video': video,
					'square': square,
					'floors': floors,
					'class': qualityClass,
					'year': year,
					'remoteness': remoteness,
					'arendators': encodeURIComponent(arendators),
					'oid': oid
				}
	        }
	        , success: function(response) {
	        	if(response.success) {
	        		//$('body').css('cursor','default');
	        		//$('#not-active-submit-mess').css("display","block");
	        		//window.location.href = window.location.href;
	        		//alert("success");
	        		//$('#form_link').hide();
		        	//$('#js-mail-result').html("Спасибо, Ваше письмо успешно отправлено!");
		        	//$('#js-mail-result').show();
	        	}else{
	        		if(typeof(response.error) == 'object'){
	        			
	        			$('#js-object-floors').next().next(".reg-error").html(response.error.floors);
	        			$('#js-object-square').next().next(".reg-error").html(response.error.square);
	        			if(response.error.floors || response.error.square){
	        				$("html,body").animate({scrollTop: $('#js-object-square').offset().top - 100}, 5);
	        			}
	        			
	        			$('#js-object-location').next().next(".reg-error").html(response.error.location);
	        			$("#js-object-name").next().next(".reg-error").html(response.error.name);
	        			if(response.error.location || response.error.name){
	        				$("html,body").animate({scrollTop: $('#js-object-name').offset().top - 100}, 5);
	        			}
	        			//$('input#company-pass').prev(".registration-caption").children(".reg-error").html(response.error.password);
	        			//$('input#company-pass-confirm').prev(".registration-caption").children(".reg-error").html(response.error.passconfirm);
	        			//$('input#company-email').prev(".registration-caption").children(".reg-error").html(response.error.email);
	        			//$('input#responsible-person').prev(".registration-caption").children(".reg-error").html(response.error.person);
	        		}else{
	        			$("#room-create-caption").children(".reg-error").html(response.error);
	        			$("html,body").animate({scrollTop: $('#room-create-caption').offset().top - 100}, 5);
	        		}
	        	}
	        }			
		});
		
	});
	
	$('.profile-object-link').click(function(){
		var oid = $(this).attr('id');
		$.ajax({
			dataType: 'jsonp'	
			, url: URL+'objects/getItem'
			, data: {
				'oid': oid
			}
			, success: function(response){			        	
				if (response.success) {
			        $.each(response.object, function(index, value){ 
			        	$("#info-"+index).html(value);
			        });
			        $('#profile-object-edit').attr('href',URL+'object/'+response.object.id);
			        $('#profile-objects-links a').attr('class','profile-object-link');
			        $('#profile-objects-links #'+response.object.id).attr('class','profile-object-link-hover');
			        //$('#profile-object-link').attr('href',URL+'object/'+response.object.id);
			        
				}
				else {
			       
				}
			}
		});
	});
});