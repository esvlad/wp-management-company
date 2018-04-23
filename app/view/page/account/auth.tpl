<div class="mc_login">
	<p>Авторизация:</p>
	<form class="mc_form" action="<?=$action_auth;?>" method="POST">
		<div class="mc_forms_row">
			<input type="text" name="mc_name" value="" placeholder="Введите ваш логин" required/>
		</div>
		<div class="mc_forms_row">
			<input type="password" name="mc_password" value="" placeholder="Введите ваш пароль" required/>
		</div>
		<div class="mc_forms_row mc_forms_submit">
			<input class="btn_mc_submit" type="submit" value="Авторизоваться"/>
		</div>
	</form>
</div>