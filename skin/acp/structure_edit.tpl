{* Шаблон редактирования структуры страницы *}
<div class="panel-heading">
	Редактириуем параметры страницы
</div>
<form method="post" action="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['id']}" role="form" class="form-horizontal">
<div class="panel-body">
	<div class="form-group">
		<label for="inputTitle" class="col-lg-3 control-label">
			Название страницы: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Будет использовано в мета теге title." data-placement="left"></span></small>
		</label>
		<div class="col-lg-9">
			<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required value="{$data['title']}">
		</div>
	</div>

	<div class="form-group">
		<label for="inputAlias" class="col-lg-3 control-label">
			Alias страницы:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></span></small>
		</label>
		<div class="col-lg-9">
			{if $data['id'] == 1}
				<p class="text-danger form-control-static">Нельзя изменять алиас главной страницы!</p>
				<input type="hidden" name="alias" class="f_input" value="{$data['alias']}" required readonly>
			{else}
				<input type="text" name="alias" id="inputAlias" class="form-control" value="{$data['alias']}">
			{/if}
			<input type="hidden" name="old_alias" value="{$data['alias']}" readonly>
		</div>
	</div>

	<div class="form-group">
		<label for="inputMetaDesc" class="col-lg-3 control-label">
			Мета описание страницы:
		</label>
		<div class="col-lg-9">
			<input type="text" name="meta_description" id="inputMetaDesc" class="form-control" value="{$data['meta_description']}" spellcheck>
		</div>
	</div>

	<div class="form-group">
		<label for="inputMetaKeys" class="col-lg-3 control-label">
			Ключевые слова страницы:
		</label>
		<div class="col-lg-9">
			<input type="text" name="meta_keywords" id="inputMetaKeys" class="form-control" value="{$data['meta_keywords']}" spellcheck>
		</div>
	</div>

	<div class="form-group">
		<label for="inputNoindex" class="col-lg-3 control-label">
			SEO NOINDEX: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Запрещает индексировать страницу поисковыми роботами." data-placement="left"></span></small>
		</label>
		<div class="col-lg-9">
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-default{if !$data['noindex']} active{/if}">
					<input type="radio" name="noindex" value="0" id="flag_noindex_false"{if !$data['noindex']} checked{/if}><i class="fa fa-fw fa-eye"></i>Разрешить индексацию
				</label>
				<label class="btn btn-default{if $data['noindex']} active{/if}">
					<input type="radio" name="noindex" value="1" id="flag_noindex_true"{if $data['noindex']} checked{/if}><i class="fa fa-fw fa-eye-slash"></i>Запретить индексацию
				</label>
			</div>
		</div>
	</div>

	{* Миниаютры *}
	{if $data['page_type'] != "php"}
		<div class="form-group">
			<label for="inputThumbWidth" class="col-lg-3 control-label">
				Ширина миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="thumb_img_width" id="inputThumbWidth" class="form-control" pattern="^[ 0-9]+$" value="{$data['thumb_img_width']}">
				<small{if $data['thumb_img_width'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['width']}px</small>
			</div>
		</div>
		<div class="form-group">
			<label for="inputThumbHeight" class="col-lg-3 control-label">
				Высота миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="thumb_img_height" id="inputThumbHeight" class="form-control" pattern="^[ 0-9]+$" value="{$data['thumb_img_height']}">
				<small{if $data['thumb_img_height'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['height']}px</small>
			</div>
		</div>
	{/if}

	<div class="form-group">
		<label for="inputSort" class="col-lg-3 control-label">
			Порядок расположения страницы в структуре:
		</label>
		<div class="col-lg-9">
			<input type="text" name="sort" id="inputSort" class="form-control" value="{$data['sort']}" pattern="^[ 0-9]+$">
		</div>
	</div>

	{*<div class="form-group">
		<label for="inputType" class="col-lg-3 control-label">
			Тип страницы:
		</label>
		<div class="col-lg-9">
			<p class="form-control-static text-muted"><span class="label label-default">{$page_types[$data['page_type']]}</span> Невозможно изменить после создания.</p>
		</div>
	</div>*}

	<div class="form-group">
		<label for="inputStructure" class="col-lg-3 control-label">
			Расположение страницы в структуре:
		</label>
		<div class="col-lg-9">
			{if $data['id'] != 1}
				<select name="parent_id" id="inputStructure" class="selectpicker show-tick" required data-header="Структура сайта" data-size="auto" data-live-search="true" data-width="50%">
				{foreach from=$tree item=p}
					<option value="{$p['id']}" data-subtext="{$p['alias']}" {if $p['id'] == $data['parent_id']}selected{/if}>{section name=level loop=$p['level']}&middot; {/section} {$p['title']}</option>
				{/foreach}
				</select>
			{else}
				<p class="text-primary form-control-static">Это корневая страница!</p>
				<input type="hidden" name="parent_id" value="{$data['parent_id']}" readonly>
			{/if}
			<input type="hidden" name="now_parent_id" value="{$data['parent_id']}" readonly>
		</div>
	</div>

	{if !empty($groups)}
	<div class="form-group">
		<label for="inputGroupAccess" class="col-lg-3 control-label">
			Доступ для групп:
			<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Укажите какие группы пользователей смогут просматривать эту страницу" data-placement="left"></span></small>
		</label>
		<div class="col-lg-9">
			<div class="btn-group" data-toggle="buttons" id="inputGroupAccess">
				{*<label class="btn btn-default {if isset($gids[0])}active{/if}">
					<input type="checkbox" name="gids[]" value="0" autocomplete="off"{if isset($gids[0])} checked{/if}><i class="fa fa-fw fa-users"></i> Все группы
				</label>*}
				{foreach from=$groups item=group}
					<label class="btn btn-default {if isset($gids[$group['gid']])}active{/if}">
						<input type="checkbox" name="gids[]" value="{$group['gid']}" autocomplete="off"{if isset($gids[$group['gid']])} checked{/if}><i class="fa fa-fw fa-user"></i> {$group['title']}
					</label>
				{/foreach}
			</div>
		</div>
	</div>
	{/if}
</div>
<div class="panel-footer">
	<div class="row">
		<div class="col-lg-9 col-md-offset-3">
			<input type="submit" name="update_unit" class="btn btn-success" value="Сохранить">
			<input type="submit" name="update_unit['ae']" class="btn btn-default" value="Сохранить и выйти">
		</div>
	</div>
</div>
</form>