{* Шаблон редактирования структуры страницы *}

<h3>Редактириуем параметры страницы</h3>

<form method="post" action="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['id']}" role="form" class="form-horizontal">

<div class="form-group">
    <label for="inputTitle" class="col-lg-3 control-label">
    	Название страницы: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Будет использовано в мета теге title." data-placement="right"></span></small>
    </label>
    <div class="col-lg-9">
		<input type="text" name="title" id="inputTitle" class="form-control" required value="{$data['title']}">
	</div>
</div>

<div class="form-group">
    <label for="inputAlias" class="col-lg-3 control-label">
    	Alias страницы:  <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Должен быть обязательно уникальным." data-placement="right"></span></small>
    </label>
    <div class="col-lg-9">
        {if $data['id'] == 1}
        	<p class="text-danger form-control-static">Нельзя изменять алиас главной страницы!</p>
			<input type="hidden" name="alias" class="f_input" value="{$data['alias']}" required readonly>
		{else}
			<input type="text" name="alias" id="inputAlias" class="form-control" required value="{$data['alias']}">
		{/if}
		<input type="hidden" name="old_alias" value="{$data['alias']}" readonly>
	</div>
</div>

<div class="form-group">
    <label for="inputMetaDesc" class="col-lg-3 control-label">
    	Мета описание страницы:
    </label>
    <div class="col-lg-9">
		<input type="text" name="meta_description" id="inputMetaDesc" class="form-control" value="{$data['meta_description']}">
	</div>
</div>

<div class="form-group">
    <label for="inputMetaKeys" class="col-lg-3 control-label">
    	Ключевые слова страницы:
    </label>
    <div class="col-lg-9">
		<input type="text" name="meta_keywords" id="inputMetaKeys" class="form-control" value="{$data['meta_keywords']}">
	</div>
</div>

<div class="form-group">
    <label for="inputNoindex" class="col-lg-3 control-label">
    	NOINDEX: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Запрещает индексировать страницу поисковыми роботами." data-placement="right"></span></small>
    </label>
    <div class="col-lg-9">
		<div class="btn-group" data-toggle="buttons">
		  <label class="btn btn-default{if $data['noindex'] == 0} active{/if}">
		    <input type="radio" name="noindex" value="0" id="flag_noindex_false"{if $data['noindex'] == 0} checked{/if}> Разрешить индексацию
		  </label>
		  <label class="btn btn-default{if $data['noindex'] == 1} active{/if}">
		    <input type="radio" name="noindex" value="1" id="flag_noindex_true"{if $data['noindex'] == 1} checked{/if}> Запретить индексацию
		  </label>
		</div>
	</div>
</div>

<div class="form-group">
    <label for="inputSort" class="col-lg-3 control-label">
    	Порядок расположения страницы в структуре:
    </label>
    <div class="col-lg-9">
		<input type="text" name="sort" id="inputSort" class="form-control" value="{$data['sort']}">
	</div>
</div>

<div class="form-group">
    <label for="inputType" class="col-lg-3 control-label">
    	Тип страницы:
    </label>
    <div class="col-lg-9">
		<p class="form-control-static text-muted"><span class="label label-default">{$page_types[$data['type']]}</span> Невозможно изменить после создания.</p>
	</div>
</div>

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

<div class="form-group">
    <div class="col-lg-9 col-md-offset-3">
    	<input type="hidden" name="empty" value="1" readonly>
		<input type="submit" name="update_unit" class="btn btn-success" value="Сохранить страницу">
	</div>
</div>

</form>