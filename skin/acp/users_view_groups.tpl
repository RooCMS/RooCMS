{* Шаблон отображения списка пользователей *}
<div class="panel-heading">
	Группы пользователей
</div>

{if !empty($data)}
	<table class="table table-hover table-condensed hidden-xs">
		<thead>
			<tr class="active">
				<th width="3%">ID</th>
				<th width="56%">Название группы</th>
				<th width="11%">Кол-во участников</th>
				<th width="30%">Опции</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$data item=group}
			<tr>
				<td class="text-muted">{$group['gid']}</td>
				<td>
					<a href="{$SCRIPT_NAME}?act=users&part=edit_group&gid={$group['gid']}">{$group['title']}</a>
				</td>
				<td class="text-left">

				</td>
				<td>
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=users&part=edit_group&uid={$group['gid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
						<a href="{$SCRIPT_NAME}?act=users&part=delete_group&uid={$group['gid']}" class="btn btn-xs btn-danger"><span class="fa fa-trash fa-fw"></span><span class="hidden-sm">Удалить</span></a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>


	{foreach from=$data item=user}

		<div class="panel-heading visible-xs">

		</div>
		<div class="panel-body visible-xs">
			Пользователей в группе:
		</div>
		<div class="panel-body text-right visible-xs">
			<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
			{if $user['uid'] != 1}<a href="{$SCRIPT_NAME}?act=users&part=delete_user&uid={$user['uid']}" class="btn btn-xs btn-danger"><span class="fa fa-user-times fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
		</div>

	{/foreach}
{else}
	<div class="panel-body">
		На данный момент групп не создано. Вы можете создать первую группу воспользовавшись слева пунктом меню "Создать группу".
	</div>
{/if}