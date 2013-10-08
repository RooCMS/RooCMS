{* Шаблон создания новой страницы в структуре сайта *}

<h3>Новая страница сайта</h3>

<form method="post" action="{$SCRIPT_NAME}?act=structure&part=create" role="form" class="form-horizontal">

<div class="form-group">
    <label for="inputTitle" class="col-lg-3 control-label">
    	Название страницы: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Будет использовано в мета теге title." data-placement="right"></span></small>
    </label>
    <div class="col-lg-9">
		<input type="text" name="title" id="inputTitle" class="form-control" required>
	</div>
</div>

<div class="form-group">
    <label for="inputAlias" class="col-lg-3 control-label">
    	Alias страницы:  <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Должен быть обязательно уникальным." data-placement="right"></span></small>
    </label>
    <div class="col-lg-9">
		<input type="text" name="alias" id="inputAlias" class="form-control" required>
	</div>
</div>

<div class="form-group">
    <label for="inputMetaDesc" class="col-lg-3 control-label">
    	Мета описание страницы:
    </label>
    <div class="col-lg-9">
		<input type="text" name="meta_description" id="inputMetaDesc" class="form-control">
	</div>
</div>

<div class="form-group">
    <label for="inputMetaKeys" class="col-lg-3 control-label">
    	Ключевые слова страницы:
    </label>
    <div class="col-lg-9">
		<input type="text" name="meta_keywords" id="inputMetaKeys" class="form-control">
	</div>
</div>

<div class="form-group">
    <label for="inputNoindex" class="col-lg-3 control-label">
    	NOINDEX: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Запрещает индексировать страницу поисковыми роботами." data-placement="right"></span></small>
    </label>
    <div class="col-lg-9">
		<div class="btn-group" data-toggle="buttons">
		  <label class="btn btn-default active">
		    <input type="radio" name="noindex" value="0" id="flag_noindex_false" checked> Разрешить индексацию
		  </label>
		  <label class="btn btn-default">
		    <input type="radio" name="noindex" value="1" id="flag_noindex_true"> Запретить индексацию
		  </label>
		</div>
	</div>
</div>

<div class="form-group">
    <label for="inputSort" class="col-lg-3 control-label">
    	Порядок расположения страницы в структуре:
    </label>
    <div class="col-lg-9">
		<input type="text" name="sort" id="inputSort" class="form-control">
	</div>
</div>

<div class="form-group">
    <label for="inputType" class="col-lg-3 control-label">
    	Тип страницы: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Вы не сможете в последствии изменить тип страницы." data-placement="right"></span></small>
    </label>
    <div class="col-lg-9">
		<select name="type" id="inputType" class="selectpicker show-tick" required>
			{foreach from=$page_types key=ptype item=ptitle}
				<option value="{$ptype}"{if isset($smarty.get.type) && $smarty.get.type == $ptype} selected{/if}>{$ptitle}</option>
			{/foreach}
		</select>
	</div>
</div>

<div class="form-group">
    <label for="inputStructure" class="col-lg-3 control-label">
    	Расположение страницы в структуре:
    </label>
    <div class="col-lg-9">
		<select name="parent_id" id="inputStructure" class="selectpicker show-tick" required data-header="Структура сайта" data-size="auto" data-live-search="true" data-width="50%">
			{foreach from=$tree item=p}
				<option value="{$p['id']}" data-subtext="{$p['alias']}">{section name=level loop=$p['level']}&middot; {/section} {$p['title']}</option>
			{/foreach}
		</select>
	</div>
</div>

<div class="form-group">
    <div class="col-lg-9 col-md-offset-3">
    	<input type="hidden" name="empty" value="1">
		<input type="submit" name="create_unit" class="btn btn-success" value="Создать страницу">
	</div>
</div>

</form>