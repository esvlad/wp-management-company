/**/

var import_result_html;

var progress_line;
(function($) {
	console.log($('.mc_section > h2').text());
	
	progress_line = $('.file_upload_bar_line');

	$('.form_import .mc_form_reset').click(function(){
		$('.file_import_result > div').css('display','none');
		$('.file_upload_bar_block').css('display','none');
		$('.form_import .mc_table tbody').removeClass('none');

		$('.file_import_result > div > p').detach();
	});
	
	$('#mc_form_import_receipts').submit(function(e){
		e = e || event;
		e.preventDefault();
		
		var result_upload = false;
		//var import_file = $(this).elements.file_import;
		var file = $(this)[0].elements.file_import.files[0];
		console.log(file);
		
		var option = {};
		var create_user;
		var opt_period = parseInt($(this).find('input[name="opt_period"]:checked').val());
		var opt_receipt = parseInt($(this).find('input[name="opt_receipt"]:checked').val());
		
		if($(this).find('input[name="create_user"]').attr('checked') == 'checked'){
			create_user = 1;
		} else {
			create_user = 0;
		}
		
		//console.log(option);
		
		if (file) {
			$('.form_import .mc_table tbody').addClass('none');
			$('.file_import_result').removeClass('none');
			$('.file_upload_bar_block').fadeIn(200);
			//result_upload = upload(file, opt_period, opt_receipt, create_user);
			
			console.log(result_upload);
			
			var message = false;
			var xhr = new XMLHttpRequest();
			var progress_line_width = 0;
			
			xhr.onload = xhr.onerror = function() {
				if (this.status == 200) {
					console.log("success");
					result = $.parseJSON(xhr.response);

					console.log(result);
					
					$('#contractors').append('<p>Всего: ' + result.contractors.count_all + '</p>');
					$('#contractors').append('<p>Добавлено: ' + result.contractors.count_insert + '</p>');
					$('#contractors').append('<p>Обновлено: ' + result.contractors.count_update + '</p>');

					$('#hmeters').append('<p>Всего: ' + result.hmeters.count_all + '</p>');
					if(result.hmeters.count_all != undefined) $('#hmeters').append('<p> Добавлено: ' + result.hmeters.count_insert + '</p>');
					if(result.hmeters.count_all != undefined) $('#hmeters').append('<p> Обновлено: ' + result.hmeters.count_update + '</p>');

					$('#personal_account').append('<p>Всего: ' + result.personal_account.count_all + '</p>');
					$('#personal_account').append('<p>Добавлено: ' + result.personal_account.count_insert + '</p>');
					$('#personal_account').append('<p>Обновлено: ' + result.personal_account.count_update + '</p>');

					$('#receipts').append('<p>Обработано: ' + result.receipts + '</p>');

					$('.file_import_result > div').each(function(){
						$(this).delay(200).fadeIn(200);
					});
				} else {
					console.log("error " + this.status);
				}
			};
			
			xhr.upload.onprogress = function(event) {
				progress_line_width = event.loaded / (event.total / 100);
				progress_line.animate({width:progress_line_width+'%'},200);
				//console.log(event.loaded + ' / ' + event.total);
			}
			
			var formData = new FormData();
	
			formData.append('action', 'file_import');
			formData.append('period', opt_period);
			formData.append('receipt', opt_receipt);
			formData.append('create_user', create_user);
			formData.append('file_import', file);

			xhr.open("POST", ajaxurl, true);
			message = xhr.send(formData);
		}
		return false;
	});
})(jQuery);


function upload(file, opt_period, opt_receipt, create_user){
	var message = false;
	var xhr = new XMLHttpRequest();
	var progress_line_width = 0;
	
	//console.log(xhr);
	
	xhr.onload = xhr.onerror = function() {
		if (this.status == 200) {
			console.log("success");
			//console.log(xhr.response);
		} else {
			console.log("error " + this.status);
		}
	};
	
	xhr.upload.onprogress = function(event) {
		progress_line_width = event.loaded / (event.total / 100);
		progress_line.animate({width:progress_line_width+'%'},200);
		//console.log(event.loaded + ' / ' + event.total);
	}
	
	
	var formData = new FormData();
	
	formData.append('action', 'file_import');
	formData.append('period', opt_period);
	formData.append('receipt', opt_receipt);
	formData.append('create_user', create_user);
	formData.append('file_import', file);

	xhr.open("POST", ajaxurl, true);
	message = xhr.send(formData);
	
	return xhr.response;
}


