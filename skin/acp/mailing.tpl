{* Template Mailing *}

<div class="col-lg-2">
	<div class="card d-none d-lg-block submenu sticky-top">
		<div class="card-header">
			Управление пользователями
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}?act=users" class="list-group-item list-group-item-action text-decoration-none"><i class="fa fa-fw fa-users"></i> Пользователи</a>
			<a href="{$SCRIPT_NAME}?act=users&part=create_user" class="list-group-item list-group-item-action text-decoration-none"><i class="fas fa-fw fa-user-plus"></i> Новый пользователь</a>
		</div>

		<div class="card-header">
			Управление группами
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}?act=users&part=group_list" class="list-group-item list-group-item-action text-decoration-none"><i class="fas fa-fw fa-user-tag"></i> Группы</a>
			<a href="{$SCRIPT_NAME}?act=users&part=create_group" class="list-group-item list-group-item-action text-decoration-none"><i class="fas fa-fw fa-plus"></i> Создать группу</a>
		</div>

		<div class="card-header">
			Рассылка
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}?act=mailing&part=create_message" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create_message"} active{/if}"><i class="fas fa-fw fa-envelope"></i> Отправить письмо</a>
			<a href="{$SCRIPT_NAME}?act=mailing&part=archive_list" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "archive_list"} active{/if}"><i class="fas fa-fw fa-archive"></i> Архив</a>
		</div>
	</div>

	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<div class="btn-group btn-group-sm d-block d-lg-none">
				<a href="{$SCRIPT_NAME}?act=users" class="btn btn-outline-primary"><i class="fa fa-fw fa-users"></i></a>
				<a href="{$SCRIPT_NAME}?act=users&part=create_user" class="btn btn-outline-primary"><i class="fas fa-fw fa-user-plus"></i></a>
				<a href="{$SCRIPT_NAME}?act=users&part=group_list" class="btn btn-outline-primary"><i class="fas fa-fw fa-user-tag"></i></a>
				<a href="{$SCRIPT_NAME}?act=users&part=create_group" class="btn btn-outline-primary"><i class="fas fa-fw fa-plus"></i></a>
				<a href="{$SCRIPT_NAME}?act=mailing&part=create_message" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "create_message"} active{/if}"><i class="fas fa-fw fa-envelope"></i></a>
				<a href="{$SCRIPT_NAME}?act=mailing&part=archive_list" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "archive_list"} active{/if}"><i class="fas fa-fw fa-archive"></i></a>
			</div>
		</div>
	</div>
</div>
<div class="col-lg-10">
	<div class="card">
		{$content}
	</div>
</div>
