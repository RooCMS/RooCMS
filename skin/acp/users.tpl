{* Основной шаблон управления контентом страниц *}

<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Управление пользователями</li>
		<li><a href="{$SCRIPT_NAME}?act=users&part=create"><span class="fa fa-fw fa-user-plus"></span> Создать пользователя</a></li>
	</ul>
</div>
<div class="col-md-10">
	<div class=" panel panel-default">
    		{$content}
	</div>
</div>