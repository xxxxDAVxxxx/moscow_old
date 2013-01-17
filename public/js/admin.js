$(document).ready(function() { 
	URL = 'http://atcompany.com.ua';

	var url = document.location.pathname.toString();	
    $("#admin-menu a").filter(
    		function () {    	
    			return url.indexOf($(this).attr('id')) != -1;
    		}).addClass("active");
	
    // sending article listener
	$("#js-article-submit").click(
			function() {	
				// hide all elements
				$('.error').hide();  
			    var title = $("input#js-article-name").val();
			    if (title == "") {  
			    	$("label#js-art-name").show();  
			    	$("input#js-article-name").focus();  
			    	return false;  
			    }  
			    var slug = $("input#js-article-slug").val();
			    if (slug == "") {  
			    	$("label#js-art-slug").show();  
			    	$("input#js-article-slug").focus();  
			    	return false;  
			    }
			    var status = $("select#js-article-status").val();
			    
			    var content = nicEditors.findEditor('js-article-content').getContent();	   
			    if (content == "") {  
			    	$("label#js-article-error").show();  
			    	$("textarea#js-article-content").focus();  
			    	return false;  
			    }
			    var meta_t = $("input#admin-edit-metat").val();
			    var meta_k = $("input#admin-edit-metak").val();
			    var meta_d = $("textarea#admin-edit-metad").val();
			    // check data id for update
			    var a_id = 0;
			    if ($("input#js-article-id").length > 0) {
			    	a_id = $("input#js-article-id").val();
			    }	    
			    // send ajax request for article
			    var aid=$("#admin-article-image-id").val();
			    var image=($("#admin-article-image").attr("src")).toString();
			    image=image.split("/");
			    image=image[image.length-1];
			    image=image.split("?");
			    image=image[0];
			    //alert(image);
			    //alert(image+" "+title+" "+slug+" "+status+" "+content+" "+image+" "+a_id+" "+aid);
			    $.ajax({
			    	dataType: 'jsonp'	
					, url: URL + '/articles/edit'
					, data: ({
						'title': encodeURIComponent(title)
						, 'slug': encodeURIComponent(slug)
						, 'status': encodeURIComponent(status)
						, 'content': encodeURIComponent(content)
						, 'meta_t': encodeURIComponent(meta_t)
						, 'meta_k': encodeURIComponent(meta_k)
						, 'meta_d': encodeURIComponent(meta_d)
						, 'a_id': a_id
						, 'aid': aid
						, 'image':encodeURIComponent(image)
			        })
			    	, success: function(response) { 
			        	if (response.success) {
			        		setTimeout(function(){window.location.href = URL + '/articles/read';}, 2000);
			        	}
			        	else {}
			    	}		
			    });
    }); 
	
    // sending portfolio listener
	$("#js-project-submit").click(function() {
		// hide all elements
		
		$('.error').hide();  
	    var title = $("input#js-project-name").val();
	    if (title == ""){  
	    	$("label#js-project-name").show();  
	    	$("input#js-project-name").focus();  
	    	return false;  
	    }  
	    var pslug = $("input#js-project-slug").val();
	    if (pslug == "") {  
	    	$("label#js-project-slug").show();  
	    	$("input#js-project-slug").focus();  
	    	return false;  
	    }
	  
	    var order_date = $("input#js-project-order_date").val();
	    var status = $("input#js-project-status").val();
	    
	    var content = nicEditors.findEditor('js-project-content').getContent();	   
	    
	    if (content == "") { 
	    	$("label#js-project-error").show();  
	    	$("textarea#js-project-content").focus();  
	    	return false;  
	    } 
	    
	    var meta_t = $("input#admin-edit-metat").val();
	    var meta_k = $("input#admin-edit-metak").val();
	    var meta_d = $("textarea#admin-edit-metad").val();
	    // check data id for update
	    
	    var p_id = 0;
	    if ($("input#js-project-id").length > 0) {
	    	p_id = $("input#js-project-id").val();
	    }
	    
	    // send ajax request for portfolio
	    var pid=$("#admin-project-image-id").val();
	    //var image=($("#admin-project-image").attr("src")).toString();
	    //image=image.split("/");
	    //image=image[image.length-1];
	    //image=image.split("?");
	    //image=image[0];
	    
	    var images_array=new Array();
	    $(".project-images-item").each(function(){
	    	var image=($(this).find('.admin-project-image').attr('src')).toString();
	    	image=image.split("/");
		    image=image[image.length-1];
		    image=image.split("?");
		    image=image[0];
		    
	    	images_array.push([image,$(this).find('.admin-project-image-title').val()]);
		});		
	    //alert(JSON.stringify(images_array));
	    //return false;
	    //alert("yofalse");
	    $.ajax({
	    	dataType: 'jsonp'	
			, url: URL + '/projects/edit'
			, data: {
				'title': encodeURIComponent(title)
				, 'pslug': encodeURIComponent(pslug)
				, 'order_date': order_date
				, 'status': encodeURIComponent(status)
				, 'content': encodeURIComponent(content)
				, 'meta_t': encodeURIComponent(meta_t)
				, 'meta_k': encodeURIComponent(meta_k)
				, 'meta_d': encodeURIComponent(meta_d)
				, 'p_id':p_id
				, 'pid':pid
				, 'image':encodeURIComponent(JSON.stringify(images_array))
			}
	    	, success: function(response) {
	        	if (response.success) {
	        		setTimeout(function(){window.location.href= URL + '/projects/read';}, 1000);
	        	}
	        	else {	      
	        		
	        	}
	        }	    	
		});
    }); 
	
	// sending seo listener
	
	   
	
	// saving article image
	$("#admin-article-image-uploader").ajaxfileupload({
		'params':{
			'a_id':$('#admin-article-image-id').val()
		},
		'action': URL + '/articles/image',
	    'onComplete': function(response) {//alert("yo");alert(response);
	    	var result = JSON.parse(strip_tags(response));	
	    	console.log(result);//alert(result.aid);
	    	$("#admin-article-image-id").val(result.aid);
	    	$("#admin-article-image").attr('src', result.src).show();
	    }
	});
	//$('#admin-project-image-id').val()
	$("#admin-project-image-uploader").ajaxfileupload({
		'params':{
			'p_id':$('#admin-project-image-id').val()
		},
		'action': URL + '/projects/image',
	    'onComplete': function(response) {
	    	var result = JSON.parse(strip_tags(response));	
	    	console.log(result);
	    	$("#admin-project-image-id").val(result.pid);
	    	$(".admin-edit-right-first").append('<div class="project-images-item">'+
	    								'<hr>'+
	    								'<img class="admin-project-image" src="'+result.src+'">'+
	    								'<input class="admin-project-image-title" type="text" placeholder="Добавить title" name="image'+result.name+'">'+
	    								'<hr>'+
	    								'<span class="image_delete_link" title="Удалить"><img src="'+URL + '/images/action_delete.png" class="action-delete"></span>'+
	    								'</div>');
	    	//$("#admin-project-image").attr('src', result.src).show();
	    }
	});
	
	$('.image_delete_link').click(function() {
		var file_name=$(this).parents('.project-images-item').find('.admin-project-image').attr('src');
		file_name=file_name.split("/");
		file_name=file_name[file_name.length-1];
		file_name=file_name.split("?");
		file_name=file_name[0];
		
	    //alert(file_name);
	    //return false;
		$.ajax({
	    	dataType: 'jsonp'	
			, url: URL + '/projects/deleteimage'
			, data: {
				'file_name': encodeURIComponent(file_name)
			}
			, success:function(success) {
	        	if (success["success"]) {
	        		alert("true");
	        		//setTimeout(function(){window.location.href= URL + '/projects/read';}, 1000);
	        	}
	        	else {	      
	        		alert("false");
	        	}
			}

	    		    	
		});
		var elem=$(this).parents('.project-images-item');
		elem.remove();
	});
	
	function strip_tags( str ){ // Strip HTML and PHP tags from a string
		return str.replace(/<\/?[^>]+>/gi, '');
	}
		
	$('#submit').val('Загрузить');
	$('#more-info-hide').click(function() {	
		$('#admin-biblio-info-maxi').hide(); 
		$('#more-info-hide').hide(); 
		$('#more-info-show').show();
	});
	$('#more-info-show').hide(); 
	$('#more-info-show').click(function() {	
		$('#admin-biblio-info-maxi').show(); 
		$('#more-info-hide').show(); 
		$('#more-info-show').hide();
	});
	$('#ImageLinkType').change(function() {
		if ($("#ImageLinkType option:selected").val() == "articles") {
			$('#ImageLinkPort').hide();
			$('#ImageLinkArt').show(); 
		};
		if ($("#ImageLinkType option:selected").val() == "portfolio") {
			$('#ImageLinkPort').show();
			$('#ImageLinkArt').hide();
		}
	});
	
	

});