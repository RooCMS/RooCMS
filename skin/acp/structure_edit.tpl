{* Шаблон редактирования структуры страницы *}
<div class="card-header">
	Редактириуем параметры страницы
</div>
<form method="post" action="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['id']}" role="form">
<div class="card-body">
	<div class="form-group row">
		<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Название страницы: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Будет использовано в меню и хлебных крошках." data-placement="left"></span></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required value="{$data['title']}">
		</div>
	</div>

	<div class="form-group row">
		<label for="inputAlias" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Alias страницы:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></span></small>
		</label>
		<div class="col-md-7 col-lg-8">
			{if $data['id'] == 1}
				<span class="text-danger form-control-plaintext">Нельзя изменять алиас главной страницы!</span>
				<input type="hidden" name="alias" class="f_input" value="{$data['alias']}" required readonly>
			{else}
				<input type="text" name="alias" id="inputAlias" class="form-control" value="{$data['alias']}">
			{/if}
			<input type="hidden" name="old_alias" value="{$data['alias']}" readonly>
		</div>
	</div>

	<div class="form-group row">
		<label for="inputMetaTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Мета заголовок: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Meta Title" data-placement="left"></span></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="text" name="meta_title" id="inputMetaTitle" class="form-control" value="{$data['meta_title']}" spellcheck>
		</div>
	</div>

	<div class="form-group row">
		<label for="inputMetaDesc" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Мета описание: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Meta Description" data-placement="left"></span></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="text" name="meta_description" id="inputMetaDesc" class="form-control" value="{$data['meta_description']}" spellcheck>
		</div>
	</div>

	<div class="form-group row">
		<label for="inputMetaKeys" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Ключевые слова: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Meta Keywords" data-placement="left"></span></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="text" name="meta_keywords" id="inputMetaKeys" class="form-control" value="{$data['meta_keywords']}" spellcheck>
		</div>
	</div>

	<div class="form-group row">
		<label for="inputNoindex" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			SEO индексация: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Запрещает индексировать страницу поисковыми роботами." data-placement="left"></span></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
				<label class="btn btn-light{if !$data['noindex']} active{/if}">
					<input type="radio" name="noindex" value="0" id="flag_noindex_false"{if !$data['noindex']} checked{/if}><i class="far fa-fw fa{if !$data['noindex']}-check{/if}-circle text-success"></i>Разрешить индексацию
				</label>
				<label class="btn btn-light{if $data['noindex']} active{/if}">
					<input type="radio" name="noindex" value="1" id="flag_noindex_true"{if $data['noindex']} checked{/if}><i class="far fa-fw fa{if $data['noindex']}-check{/if}-circle text-danger"></i>Запретить индексацию
				</label>
			</div>
		</div>
	</div>

	{* Thumbnails *}
	{if $data['page_type'] != "php"}
		<div class="form-group row">
			<label for="inputThumbWidth" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Ширина миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="number" name="thumb_img_width" id="inputThumbWidth" class="form-control" pattern="^[ 0-9]+$" value="{$data['thumb_img_width']}">
				<small{if $data['thumb_img_width'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['width']}px</small>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputThumbHeight" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Высота миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="number" name="thumb_img_height" id="inputThumbHeight" class="form-control" pattern="^[ 0-9]+$" value="{$data['thumb_img_height']}">
				<small{if $data['thumb_img_height'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['height']}px</small>
			</div>
		</div>
	{/if}

	<div class="form-group row">
		<label for="inputSort" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Порядок расположения страницы в структуре:
		</label>
		<div class="col-md-7 col-lg-8">
			<input type="number" name="sort" id="inputSort" class="form-control" value="{$data['sort']}" pattern="^[ 0-9]+$">
		</div>
	</div>

	<div class="form-group row">
		<label for="inputStructure" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Расположение страницы в структуре:
		</label>
		<div class="col-md-7 col-lg-8">
			<div class="row">
				<div class="col-12 col-lg-6">
					{if $data['id'] != 1}
						<select name="parent_id" id="inputStructure" class="selectpicker" required data-header="Структура сайта" data-size="auto" data-live-search="true" data-width="100%">
						{foreach from=$tree item=p}
							<option value="{$p['id']}" data-subtext="{$p['alias']}" {if $p['id'] == $data['parent_id']}selected{/if}>{section name=level loop=$p['level']}&middot; {/section} {$p['title']}</option>
						{/foreach}
						</select>
					{else}
						<p class="text-primary form-control-plaintext">Это корневая страница!</p>
					{/if}
					<input type="hidden" name="now_parent_id" value="{$data['parent_id']}" readonly>
				</div>
			</div>
		</div>
	</div>

	{if !empty($groups)}
	<div class="form-group row">
		<label for="inputGroupAccess" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Доступ для групп:
			<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Укажите какие группы пользователей смогут просматривать эту страницу" data-placement="left"></span></small>
		</label>
		<div class="col-md-7 col-lg-8">
			<div class="btn-group-toggle btn-group-vertical roocms-crui" data-toggle="buttons" id="inputGroupAccess">
				{foreach from=$groups item=group}
					<label class="btn btn-light {if isset($gids[$group['gid']])}active{/if}">
						<input type="checkbox" name="gids[]" value="{$group['gid']}" autocomplete="off"{if isset($gids[$group['gid']])} checked{/if}><i class="far fa-fw fa-{if isset($gids[$group['gid']])}check-{/if}square"></i> {$group['title']}
					</label>
				{/foreach}
			</div>
		</div>
	</div>
	{/if}

	<div class="form-group row">
		<label for="inputNoNav" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Навигация: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Данный раздел можно обозначить как часть общей навигации" data-placement="left"></span></small>
		</label>
		{if $data['id'] != 1}
			<div class="col-md-7 col-lg-8">
				<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
					<label class="btn btn-light {if $data['nav']} active{/if}">
						<input type="radio" name="nav" value="1" id="flag_nav_false" {if $data['nav']} checked{/if}><i class="far fa-fw fa{if $data['nav']}-check{/if}-circle text-success"></i>Отображать
					</label>
					<label class="btn btn-light {if !$data['nav']} active{/if}">
						<input type="radio" name="nav" value="0" id="flag_nav_true" {if !$data['nav']} checked{/if}><i class="far fa-fw fa{if $data['nav']}-check{/if}-circle text-danger"></i>Скрыть
					</label>
				</div>
			</div>
		{else}
			<div class="col-md-7 col-lg-8">
				<p class="text-primary form-control-plaintext">Главная страница всегда будет частью навигации сайта. :)</p>
			</div>
		{/if}
	</div>
</div>
<div class="card-footer">
	<div class="row">
		<div class="col-md-7 offset-md-5 col-lg-8 offset-lg-4">
			<input type="submit" name="update_unit" class="btn btn-success btn-lg" value="Сохранить">
			<input type="submit" name="update_unit['ae']" class="btn btn-outline-success btn-lg" value="Сохранить и выйти">
		</div>
	</div>
</div>
</form>