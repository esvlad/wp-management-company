<div class="mc_profile_view clearfix">
	<div class="mc_profile_view_nav block_left">
		<div class="mc_profile_btn_group">
			<? foreach($mc_btn_links as $links) : ?>
				<a href="<?=$links['page']?>" class="profile_btn"><?=$links['name']?></a>
			<? endforeach; ?>
		</div>
	</div>
	<div class="mc_content block_right">
		<div class="mc_content_view mc_profile_view">
			<p>Логин: <span><?=$mc_user['login'];?></span></p>
			<p>ФИО: <span><?=$mc_user['name'];?></span></p>
			<p>Адрес: <span><?=$mc_user['xml_id'];?></span></p>
			<? if(isset($mc_user['commonarea'])) : ?>
				<p>Площать помещения: <span><?=$mc_user['commonarea'];?> м<sup>2</sup></span></p>
			<? endif; ?>
			<? if(isset($mc_user['people'])) : ?>
				<p>Количество проживающих человек: <span><?=$mc_user['people'];?></span></p>
			<? endif; ?>
			<br>
			<p>Управляющая компания: <span><?=$mc_user['mc_object']['name'];?></span></p>
			<p>Адрес: <span><?=$mc_user['mc_object']['adress'];?></span></p>
			<p>Телефон: <span>+7 <?=$mc_user['mc_object']['phone'];?></span></p>
			<p>Телефон диспетчерской: <span>+7 <?=$mc_user['mc_object']['receipt_phone'];?></span></p>
			<p>Реквизиты:</p>
			<ul>
				<li>Банк: <span><?=$mc_user['mc_object']['bank'];?></span></li>
				<li>ИНН: <span><?=$mc_user['mc_object']['inn'];?></span></li>
				<li>КПП: <span><?=$mc_user['mc_object']['kpp'];?></span></li>
				<li>Расчетный счет: <span><?=$mc_user['mc_object']['rs'];?></span></li>
				<li>Корреспондетский счет: <span><?=$mc_user['mc_object']['ks'];?></span></li>
				<li>БИК: <span><?=$mc_user['mc_object']['bik'];?></span></li>
			</ul>
		</div>
	</div>
</div>