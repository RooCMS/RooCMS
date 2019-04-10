{* Шаблон отображения списка пользователей *}
<div class="card-header">
	Группы пользователей
</div>

{if !empty($data)}
	<table class="table table-hover table-condensed d-none d-sm-table mb-0">
		<thead class="bg-light">
			<tr class="active">
				<th width="3%">ID</th>
				<th width="56%">Название группы</th>
				<th width="11%">Кол-во участников</th>
				<th width="30%" class="text-right">Опции</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$data item=group}
			<tr>
				<td class="align-middle text-muted">{$group['gid']}</td>
				<td class="align-middle">
					<a href="{$SCRIPT_NAME}?act=usergroups&part=edit_group&gid={$group['gid']}">{$group['title']}</a>
				</td>
				<td class="align-middle text-left">
					{$group['users']}
				</td>
				<td class="align-middle text-right">
					<div class="btn-group btn-group-sm">
						<a href="{$SCRIPT_NAME}?act=usergroups&part=edit_group&gid={$group['gid']}" class="btn btn-outline-primary"><i class="fas fa-users-cog fa-fw"></i><span class="d-none d-md-inline-block">Редактировать</span></a>
						<a href="{$SCRIPT_NAME}?act=usergroups&part=delete_group&gid={$group['gid']}" class="btn btn-danger"><i class="far fa-trash-alt fa-fw"></i><span class="d-none d-md-inline-block">Удалить</span></a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<table class="table table-hover d-block-table d-sm-none mb-0">
		<tbody>
		{foreach from=$data item=group}
			<tr>
				<td class="align-middle">
					<a href="{$SCRIPT_NAME}?act=usergroups&part=edit_group&gid={$group['gid']}">{$group['title']}</a>
				</td>
				<td class="w-25 align-middle text-right">
					<div class="btn-group btn-group-sm">
						<a href="{$SCRIPT_NAME}?act=usergroups&part=edit_group&gid={$group['gid']}" class="btn btn-outline-primary"><i class="fas fa-users-cog fa-fw"></i></a>
						<a href="{$SCRIPT_NAME}?act=usergroups&part=delete_group&gid={$group['gid']}" class="btn btn-danger"><i class="far fa-trash-alt fa-fw"></i></a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

{else}
	<div class="card-body">
		На данный момент групп не создано. Вы можете создать первую группу воспользовавшись слева пунктом меню "Создать группу".
	</div>
{/if}