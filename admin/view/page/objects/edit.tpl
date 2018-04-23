<div class="wrap mc">
	<h1 class="wp-heading-inline mc_title"><?=$title;?></h1>
	<div class="mc_section">
		<h2>Информация об объекте управления</h2>
		<div class="mc_block">
			<form id="mc_form_object" class="mc_form form_import form_object" action="<?=$action_url;?>" method="POST">
				<input type="hidden" name="reload_url" value="<?=$reload_url;?>">
				<input type="hidden" name="object_id" value="<?=$f_id;?>">
				<div class="mc_form_container clearfix">
					<div class="mc_form_row clearfix">
						<label for="name" class="mc_form_label required">Название</label>
						<input id="name" type="text" name="name" value="<?=$f_name;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<label for="adress" class="mc_form_label required">Адрес</label>
						<input id="adress" type="text" name="adress" value="<?=$f_adress;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<label for="phone" class="mc_form_label required">Телефон</label>
						<input id="phone" type="text" name="phone" value="<?=$f_phone;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<label for="disp_phone" class="mc_form_label required">Телефон диспетчерской</label>
						<input id="disp_phone" type="text" name="disp_phone" value="<?=$f_disp_phone;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<label for="receipt_phone" class="mc_form_label required">Телефон для квитанций</label>
						<input id="receipt_phone" type="text" name="receipt_phone" value="<?=$f_receipt_phone;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<label for="bank" class="mc_form_label required">Банк</label>
						<input id="bank" type="text" name="bank" value="<?=$f_bank;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<label for="inn" class="mc_form_label required">ИНН</label>
						<input id="inn" type="text" name="inn" value="<?=$f_inn;?>" required></td>
					</div>
					<div class="mc_form_row clearfix">
						<label for="kpp" class="mc_form_label required">КПП</label>
						<input id="kpp" type="text" name="kpp" value="<?=$f_kpp;?>" required></td>
					</div>
					<div class="mc_form_row clearfix">
						<label for="bik" class="mc_form_label required">БИК</label>
						<input id="bik" type="text" name="bik" value="<?=$f_bik;?>" required></td>
					</div>
					<div class="mc_form_row clearfix">
						<label for="rs" class="mc_form_label required">Расчетный счет</label>
						<input id="rs" type="text" name="rs" value="<?=$f_rs;?>" required></td>
					</div>
					<div class="mc_form_row clearfix">
						<label for="ks" class="mc_form_label required">Корреспондентский счет</label>
						<input id="ks" type="text" name="ks" value="<?=$f_ks;?>" required></td>
					</div>
					<div class="mc_form_row clearfix">
						<label for="period_start" class="mc_form_label required">Начало периода (день месяца)</label>
						<input id="period_start" type="number" name="period_start" value="<?=$f_period_start;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<label for="period_end" class="mc_form_label required">Окончание периода (день месяца)</label>
						<input id="period_end" type="number" name="period_end" value="<?=$f_period_end;?>" required>
					</div>
					<div class="mc_form_row clearfix">
						<div class="mc_form_textarea_box">
							<label for="name" class="mc_form_label">Описание</label>
							<? wp_editor($f_caption, 'caption', array('wpautop' => 1, 'media_buttons' => 0, 'textarea_name' => 'caption', 'textarea_rows' => 3, 'tabindex' => null, 'teeny' => 1, 'tinymce' => 1, 'drag_drop_upload' => false)); ?>
						</div>
					</div>
					<div class="mc_form_row clearfix">
						<div class="mc_form_textarea_box">
							<label for="name" class="mc_form_label">Заметка для админитратора</label>
							<? wp_editor($f_admin_notes, 'adminnotes', array('wpautop' => 1, 'media_buttons' => 0, 'textarea_name' => 'admin_notes', 'textarea_rows' => 3, 'tabindex' => null, 'teeny' => 1, 'tinymce' => 1, 'drag_drop_upload' => false)); ?>
						</div>
					</div>
					<div class="mc_form_row clearfix">
						<div class="mc_form_btn">
							<input id="obkect_save" class="mc_form_btn_save" type="submit" value="Сохранить">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<? if(!empty($script)) : ?>
	<? foreach($script as $value) : ?>
		<script src="<?=$value['src'];?>"></script>
	<? endforeach; ?>
<? endif; ?>