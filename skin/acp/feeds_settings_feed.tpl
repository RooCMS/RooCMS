{* Feed settings template *}

<div class="card-header">
	Параметры ленты
</div>
<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_settings&page={$feed['id']}" role="form">
<div class="card-body">
	<div class="form-group row">
		<label for="inputRss" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			RSS вывод: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Включить/Выключить RSS ленту" data-placement="right"></i></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
				<label class="btn btn-light{if $feed['rss']} active{/if}">
					<input type="radio" name="rss" value="1" id="flag_rss_on"{if $feed['rss']} checked{/if}> <i class="far fa-fw fa{if $feed['rss']}-check{/if}-circle text-success"></i> Вкл
				</label>
				<label class="btn btn-light{if !$feed['rss']} active{/if}">
					<input type="radio" name="rss" value="0" id="flag_rss_off"{if !$feed['rss']} checked{/if}> <i class="far fa-fw fa{if !$feed['rss']}-check{/if}-circle text-danger"></i> Выкл
				</label>
			</div>
			{if $feed['rss_warn']}<p class="text-warning">Внимание! RSS ленты не будут отображаться, потому что запрещены по всему сайту. Вы можете отменить запрет в <a href="{$SCRIPT_NAME}?act=config&part=rss">настройках сайта</a>.</p>{/if}
		</div>
	</div>

	<div class="form-group row">
		<label for="inputShowChildFeeds" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Показ публикаций:
			<small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Использование этой опции добавит публикации из подчиненных лент в текущую ленту. Отобразится только в пользовательской части сайта." data-placement="left"></i></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<div class="row">
				<div class="col-12 col-lg-6">
					<select name="show_child_feeds" id="inputShowChildFeeds" class="selectpicker" required data-size="auto" data-width="100%">
						<option value="none" {if $feed['show_child_feeds'] == "none"}selected{/if}>Публикации только из текущей ленты</option>
						<option value="default" {if $feed['show_child_feeds'] == "default"}selected{/if}>Публикации из подчиненных лент в пределах их разрешений.</option>
						<option value="forced" {if $feed['show_child_feeds'] == "forced"}selected{/if}>Публикации из всех подчиненных лент, не взирая на разрешения.</option>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group row">
		<label for="inputItems" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Кол-во новостей на страницу: <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Устанавливает кол-во новостей выводимых на странице. По-умолчанию:{$feed['global_items_per_page']}. При значении 0 используется значение по-умолчанию." data-placement="left"></i></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="number" name="items_per_page" id="inputItems" class="form-control" value="{$feed['items_per_page']}">
			{if $feed['items_per_page'] == 0}<p class="text-primary">Используется значение по-умолчанию: <b>{$feed['global_items_per_page']}</b></p>{/if}
		</div>
	</div>

	<div class="form-group row">
		<label for="inputItemsSorting" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Порядок сортировки элементов:
			<small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Задает порядок сортировки элементов в ленте. По-умолчанию элементы сортируются по Дате Публикации." data-placement="left"></i></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<div class="row">
				<div class="col-12 col-lg-6">
					<select name="items_sorting" id="inputItemsSorting" class="selectpicker" required data-header="Сортировать ..." data-size="auto" data-width="100%">
						<option value="datepublication" {if $feed['items_sorting'] == "datepublication"}selected{/if}>по Дате Публикации</option>
						<option value="title_asc" {if $feed['items_sorting'] == "title_asc"}selected{/if}>по Названию от А до Я</option>
						<option value="title_desc" {if $feed['items_sorting'] == "title_desc"}selected{/if}>по Названию от Я до А</option>
						<option value="manual_sorting" {if $feed['items_sorting'] == "manual_sorting"}selected{/if}>вручную</option>
					</select>
				</div>
			</div>
		</div>
	</div>

	{* Thumbnail options *}
	<div class="form-group row">
		<label for="inputThumbWidth" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Ширина миниатюр картинок у ленты:
			<small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></i></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="number" name="thumb_img_width" id="inputThumbWidth" class="form-control" pattern="^[ 0-9]+$" value="{$feed['thumb_img_width']}">
			<small{if $feed['thumb_img_width'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['width']}px</small>
		</div>
	</div>
	<div class="form-group row">
		<label for="inputThumbHeight" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Высота миниатюр картинок у ленты:
			<small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></i></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="number" name="thumb_img_height" id="inputThumbHeight" class="form-control" pattern="^[ 0-9]+$" value="{$feed['thumb_img_height']}">
			<small{if $feed['thumb_img_height'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['height']}px</small>
		</div>
	</div>

	<script type="text/javascript" src="plugin/ckeditor.php"></script>

	{* Миниаютры *}
	<div class="form-group row">
		<label for="inputAppendInfoBefore" class="col-12 col-xl-4 form-control-plaintext text-right">
			Информационный блок (верх ленты):
			<small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Содержимое блока будет распологаться сразу под заголовком ленты, над выводом элементов." data-placement="left"></i></small>
		</label>
		<div class="col-12 col-xl-8">
			<textarea id="inputAppendInfoBefore" class="form-control ckeditor" name="append_info_before" required>{$feed['append_info_before']}</textarea>
		</div>
	</div>
	<div class="form-group row">
		<label for="inputAppendInfoAfter" class="col-12 col-xl-4 form-control-plaintext text-right">
			Информационный блок (низ ленты):
			<small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Содержимое блока будет распологатьсяпосле вывода элементов ленты." data-placement="left"></i></small>
		</label>
		<div class="col-12 col-xl-8">
			<textarea id="inputAppendInfoAfter" class="form-control ckeditor" name="append_info_after" required>{$feed['append_info_after']}</textarea>
		</div>
	</div>
</div>
<div class="card-footer">
	<div class="row">
		<div class="col-md-7 offset-md-5 col-lg-8 offset-lg-4">
			<input type="submit" name="update_settings" class="btn btn-lg btn-success" value="Сохранить настройки">
		</div>
	</div>
</div>
</form>