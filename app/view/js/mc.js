/**/


var progress_line;
(function($) {
	console.log($('.mc_section > h2').text());
	
	progress_line = $('.file_upload_bar_line');
	
	$('#mc_form_import_receipts').submit(function(e){
		e = e || event;
		e.preventDefault();
		
		var result_upload = false;
		//var import_file = $(this).elements.file_import;
		var file = $(this)[0].elements.file_import.files[0];
		console.log(file);
		
		var option = {};
		option.opt_period = $(this).find('input[name="opt_period"][checked="checked"]');
		option.opt_receipt = $(this).find('input[name="opt_receipt"][checked="checked"]');
		
		if($(this).find('input[name="create_user"]').attr('checked') == 'checked'){
			option.create_user = 1;
		} else {
			option.create_user = 0;
		}
		
		if (file) {
			$('.form_import .mc_table tbody').addClass('none');
			$('.file_upload_bar_block').fadeIn(200);
			result_upload = upload(file, option);
			
			console.log(result_upload);
		}
		return false;
	});
})(jQuery);


function upload(file, option){
	var message = false;
	var xhr = new XMLHttpRequest();
	var progress_line_width = 0;
	
	console.log(xhr);
	
	xhr.onload = xhr.onerror = function() {
		if (this.status == 200) {
			console.log("success");
			console.log(xhr.response);
		} else {
			console.log("error " + this.status);
		}
	};
	
	xhr.upload.onprogress = function(event) {
		progress_line_width = event.loaded / (event.total / 100);
		progress_line.animate({width:progress_line_width+'%'},200);
		console.log(event.loaded + ' / ' + event.total);
	}
	
	var data = {
		action : 'file_import',
		opts : option,
		files : file
	};
	
	var formData = new FormData();
	
	formData.append('action', 'file_import');
	formData.append('opts', option);
	formData.append('file_import', file);

	xhr.open("POST", ajaxurl, true);
	message = xhr.send(formData);
}


