{* Основной шаблон управления пользователями *}

<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Управление пользователями</li>
		<li><a href="{$SCRIPT_NAME}?act=users&part=create"><span class="fa fa-fw fa-user-plus"></span> Создать пользователя</a></li>

		{if isset($smarty.get.part)}
			<li class="nav-header">Опции</li>
			<li><a href="{$SCRIPT_NAME}?act=users"><span class="fa fa-fw fa-arrow-left"></span>  Вернуться к списку</a></li>
		{/if}
	</ul>
</div>
<div class="col-md-10">
	<div class=" panel panel-default">
    		{$content}
	</div>
</div>