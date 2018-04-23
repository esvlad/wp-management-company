<div class="wrap">
	<h1 class="wp-heading-inline mc_title"><?=$title;?></h1>
	<a href="<?=$link_add;?>" class="page-title-action">Добавить</a>
	<div class="mc_section">
		<h2>Управляющие компании</h2>
		<div class="mc_block">
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-1">Выделить все</label>
							<input id="cb-select-all-1" type="checkbox">
						</td>
						<th scope="col" id="id" class="manage-column" width="20px">
							<span>ID</span>
						</th>
						<th scope="col" id="title" class="manage-column">
							<span>Название</span>
						</th>
						<th scope="col" id="active" class="manage-column">
							<span>Статус</span>
						</th>
					</tr>
				</thead>
				<tbody>
					<? foreach($org as $value) : ?>
						<tr class="iedit author-self level-0 type-post status-publish format-standard has-post-thumbnail hentry category-news_content">
							<th scope="row" class="check-column">
								<label class="screen-reader-text" for="org-<?=$value['id'];?>"></label>
								<input id="org-<?=$value['id'];?>" type="checkbox" name="post[]" value="<?=$value['id'];?>">
								<div class="locked-indicator">
									<span class="locked-indicator-icon" aria-hidden="true"></span>
									<span class="screen-reader-text"></span>
								</div>
							</th>
							<td class="categories column-categories">
								<span><?=$value['id'];?></span>
							</td>
							<td class="title column-title has-row-actions column-primary page-title">
								<strong><a class="row-title" href="<?= $link_edit . $value['id'];?>"><?=$value['name'];?></a></strong>
								<div class="row-actions">
									<span class="edit"><a href="<?= $link_edit . $value['id'];?>">Редактировать</a> | </span><span class="trash"><a href="<?=wp_nonce_url($link_trash . $value['id']);?>" class="submitdelete">Удалить</a> | </span><span class="view"><a href="<?= $link_view . $value['id'];?>" rel="bookmark">Перейти</a></span>
								</div>
							</td>
							<td class="categories column-categories">
								<? if($value['active'] == 1) : ?>
									<span class="mc_admin_active" data-id="<?=$value['id'];?>" data-active="<?=$value['active'];?>">Активна</span>
								<? else : ?>
									<span class="mc_admin_active" data-id="<?=$value['id'];?>" data-active="<?=$value['active'];?>">Не активна</span>
								<? endif; ?>
							</td>
						</tr>
					<? endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-2">Выделить все</label>
							<input id="cb-select-all-2" type="checkbox">
						</td>
						<th scope="col" id="id" class="manage-column" width="20px">
							<span>ID</span>
						</th>
						<th scope="col" class="manage-column">
							<span>Название</span>
						</th>
						<th scope="col" id="active" class="manage-column">
							<span>Статус</span>
						</th>
					</tr>
				</tfoot>

			</table>
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