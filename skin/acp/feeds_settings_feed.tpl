{* Шаблон параметров ленты *}

<div class="panel-heading">
	Параметры ленты
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_settings&page={$feed['id']}" role="form" class="form-horizontal">
		<div class="form-group">
			<label for="inputRss" class="col-lg-3 control-label">
				RSS вывод: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Включить/Выключить RSS ленту" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default{if $feed['rss'] == 1} active{/if}">
						<input type="radio" name="rss" value="1" id="flag_rss_on"{if $feed['rss'] == 1} checked{/if}> <span class="text-success"><i class="fa fa-fw fa-power-off"></i> Вкл</span>
					</label>
					<label class="btn btn-default{if $feed['rss'] == 0} active{/if}">
						<input type="radio" name="rss" value="0" id="flag_rss_off"{if $feed['rss'] == 0} checked{/if}> <span class="text-danger"><i class="fa fa-fw fa-power-off"></i> Выкл</span>
					</label>
				</div>
				{if $feed['rss_warn']}<p class="text-warning">Внимание! RSS ленты не будут отображаться, потому что запрещены по всему сайту. Вы можете отменить запрет в <a href="{$SCRIPT_NAME}?act=config&part=rss">настройках сайта</a>.</p>{/if}
			</div>
		</div>

		<div class="form-group">
			<label for="inputShowChildFeeds" class="col-lg-3 control-label">
				Показ публикаций:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Использование этой опции добавит публикации из подчиненных лент в текущую ленту. Отобразится только в пользовательской части сайта." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<select name="show_child_feeds" id="inputShowChildFeeds" class="selectpicker show-tick" required data-size="auto" data-width="50%">
					<option value="none" {if $feed['show_child_feeds'] == "none"}selected{/if}>Публикации только из текущей ленты</option>
					<option value="default" {if $feed['show_child_feeds'] == "default"}selected{/if}>Публикации из подчиненных лент в пределах их разрешений.</option>
					<option value="forced" {if $feed['show_child_feeds'] == "forced"}selected{/if}>Публикации из всех подчиненных лент, не взирая на разрешения.</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label for="inputItems" class="col-lg-3 control-label">
				Кол-во новостей на страницу: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Устанавливает кол-во новостей выводимых на странице. По-умолчанию:{$feed['global_items_per_page']}. При значении 0 используется значение по-умолчанию." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="items_per_page" id="inputItems" class="form-control" value="{$feed['items_per_page']}">
				{if $feed['items_per_page'] == 0}<p class="text-primary">Используется значение по-умолчанию: <b>{$feed['global_items_per_page']}</b></p>{/if}
			</div>
		</div>

		<div class="form-group">
			<label for="inputItemsSorting" class="col-lg-3 control-label">
				Порядок сортировки элементов:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Задает порядок сортировки элементов в ленте. По-умолчанию элементы сортируются по Дате Публикации." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<select name="items_sorting" id="inputItemsSorting" class="selectpicker show-tick" required data-header="Сортировать ..." data-size="auto" data-width="50%">
					<option value="datepublication" {if $feed['items_sorting'] == "datepublication"}selected{/if}>по Дате Публикации</option>
					<option value="title_asc" {if $feed['items_sorting'] == "title_asc"}selected{/if}>по Названию от А до Я</option>
					<option value="title_desc" {if $feed['items_sorting'] == "title_desc"}selected{/if}>по Названию от Я до А</option>
					<option value="manual_sorting" {if $feed['items_sorting'] == "manual_sorting"}selected{/if}>вручную</option>
				</select>
			</div>
		</div>

		{* Миниаютры *}
		<div class="form-group">
			<label for="inputThumbWidth" class="col-lg-3 control-label">
				Ширина миниатюр картинок у ленты:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="thumb_img_width" id="inputThumbWidth" class="form-control" pattern="^[ 0-9]+$" value="{$feed['thumb_img_width']}">
				<small{if $feed['thumb_img_width'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['width']}px</small>
			</div>
		</div>
		<div class="form-group">
			<label for="inputThumbHeight" class="col-lg-3 control-label">
				Высота миниатюр картинок у ленты:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="thumb_img_height" id="inputThumbHeight" class="form-control" pattern="^[ 0-9]+$" value="{$feed['thumb_img_height']}">
				<small{if $feed['thumb_img_height'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['height']}px</small>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-9 col-md-offset-3">
				<input type="submit" name="update_settings" class="btn btn-success" value="Сохранить настройки">
			</div>
		</div>
	</form>
</div>