{* Основной шаблон управления пользователями *}

<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
		<li class="nav-header">Управление пользователями</li>
		<li{if !isset($smarty.get.part)} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users"><i class="fa fa-fw fa-users"></i> Список пользователей</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create_user"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users&part=create_user"><i class="fa fa-fw fa-user-plus"></i> Создать пользователя</a></li>

		<li class="nav-header">Управление группами</li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "group_list"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users&part=group_list"><i class="fa fa-fw fa-file-text"></i> Список групп</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create_group"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users&part=create_group"><i class="fa fa-fw fa-plus"></i> Создать группу</a></li>

		<li class="nav-header">Рассылка</li>
		<li><a href="{$SCRIPT_NAME}?act=mailing&part=message"><i class="fa fa-fw fa-envelope"></i> Отправить письмо</a></li>
	</ul>
</div>
<div class="col-md-10">
	<div class=" panel panel-default">
    		{$content}
	</div>
</div>