{* Основной шаблон управления контентом страниц *}

<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Управление страницами</li>
		<li><a href="{$SCRIPT_NAME}?act=structure&part=create"><span class="fa fa-fw fa-plus-circle"></span> Создать новую страницу</a></li>
	</ul>
</div>
<div class="col-md-10 thumbnail">
	<div class="caption">
    	{$content}
	</div>
</div>