<div class="wrap">
	<h1 class="wp-heading-inline mc_title"><?=$title;?></h1>
	<div class="mc_section">
		<h2>Настройки импорта файла</h2>
		<div class="mc_block">
			<form id="mc_form_import_receipts" class="mc_form form_import" action="" method="POST" enctype="multipart/form-data">
				<table class="mc_table">
					<thead>
						<tr class="file_upload_bar">
							<td colspan="2">
								<div class="file_upload_bar_block">
									<div class="file_upload_bar_line"></div>
								</div>
								<div class="file_import_result clearfix none">
									<div id="contractors" class="row">
										<h3>Поставщики услуг</h3>
									</div>
									<div id="hmeters" class="row">
										<h3>Домовые счетчики</h3>
									</div>
									<div id="personal_account" class="row">
										<h3>Аккаунты</h3>
									</div>
									<div id="receipts" class="row">
										<h3>Квитации</h3>
									</div>
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr valign="center">
							<td>Файл для загрузки:</td>
							<td><input type="file" name="file_import" value=""></td>
						</tr>
						<tr valign="center">
							<td>Параметры квитанций:</td>
							<td>
								<label><input type="radio" name="opt_receipt" value="1" checked="checked"> - добавить новые</label>
								<label><input type="radio" name="opt_receipt" value="2"> - обновить ранее загруженные</label>
							</td>
						</tr>
						<tr valign="center">
							<td>Обновление периода:</td>
							<td>
								<label><input type="radio" name="opt_period" value="1" checked="checked"> - создать новый период</label>
								<label><input type="radio" name="opt_period" value="2"> - обновить ранее загруженный период</label>
							</td>
						</tr>
						<tr valign="center">
							<td>Пользователи:</td>
							<td>
								<label><input type="checkbox" name="create_user" value="1" checked="checked"> - Автоматически создавать пользователей для импортированных лицевых счетов</label>
							</td>
						</tr>
						<tr valign="center" style="display:none;">
							<td>Действия над лицевыми счетами и счетчиками, которых нет в файле:</td>
							<td>
								<label><input type="radio" name="update_paccount" value="1" checked="checked"> - ничего</label>
								<label><input type="radio" name="update_paccount" value="2"> - деактивировать</label>
								<label><input type="radio" name="update_paccount" value="3"> - удалить</label>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr valign="center">
							<td colspan="2">
								<input id="btn_mc_import_receipts_click" class="display_none" type="submit" value="Импортировать">
								<input id="reset" class="btn mc_form_reset" type="reset" value="Очистить">
							</td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>
<script>
	var ajax_url = '<?=$ajax_url;?>';
</script>
<? if(!empty($script)) : ?>
	<? foreach($script as $value) : ?>
		<script src="<?=$value['src'];?>"></script>
	<? endforeach; ?>
<? endif; ?>