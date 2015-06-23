{* Основной шаблон управления пользователями *}

<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
		<li class="nav-header">Управление пользователями</li>
		<li{if !isset($smarty.get.part)} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users"><span class="fa fa-fw fa-users"></span> Список пользователей</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create_user"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users&part=create_user"><span class="fa fa-fw fa-user-plus"></span> Создать пользователя</a></li>


		<li class="nav-header">Управление группами</li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "group_list"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users&part=group_list"><span class="fa fa-fw fa-file-text"></span> Список групп</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create_group"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=users&part=create_group">
			<span class="fa fa-fw fa-plus"></span> Создать группу</a>
		</li>
	</ul>
</div>
<div class="col-md-10">
	<div class=" panel panel-default">
    		{$content}
	</div>
</div>