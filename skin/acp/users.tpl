{* Основной шаблон управления пользователями *}

<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">
			<div class="panel-heading visible-lg">
				Управление пользователями
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=users" class="list-group-item{if !isset($smarty.get.part)} active{/if}"><i class="fa fa-fw fa-users"></i> Пользователи</a>
				<a href="{$SCRIPT_NAME}?act=users&part=create_user" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create_user"} active{/if}"><i class="fa fa-fw fa-user-plus"></i> Новый пользователь</a>
			</div>

			<div class="panel-heading visible-lg">
				Управление группами
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=users&part=group_list" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "group_list"} active{/if}"><i class="fa fa-fw fa-file-text"></i> Группы</a>
				<a href="{$SCRIPT_NAME}?act=users&part=create_group" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create_group"} active{/if}"><i class="fa fa-fw fa-plus"></i> Создать группу</a>
			</div>

			<div class="panel-heading visible-lg">
				Рассылка
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=mailing&part=message" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "message"} active{/if}"><i class="fa fa-fw fa-envelope"></i> Отправить письмо</a>
			</div>
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		<a href="{$SCRIPT_NAME}?act=users" class="btn btn-outline-primary{if !isset($smarty.get.part)} active{/if}"><i class="fa fa-fw fa-users"></i></a>
		<a href="{$SCRIPT_NAME}?act=users&part=create_user" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "create_user"} active{/if}"><i class="fa fa-fw fa-user-plus"></i></a>
		<a href="{$SCRIPT_NAME}?act=users&part=group_list" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "group_list"} active{/if}"><i class="fa fa-fw fa-file-text"></i></a>
		<a href="{$SCRIPT_NAME}?act=users&part=create_group" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "create_group"} active{/if}"><i class="fa fa-fw fa-plus"></i></a>
		<a href="{$SCRIPT_NAME}?act=mailing&part=message" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "message"} active{/if}"><i class="fa fa-fw fa-envelope"></i></a>
	</div>
</div>
<div class="col-sm-9 col-md-10">
	<div class=" panel panel-default">
		{$content}
	</div>
</div>
