{* Шаблон отображения страниц сайта *}
<div class="panel-heading">
	Пользователи
</div>


<table class="table table-hover table-condensed hidden-xs">
	<thead>
		<tr class="active">
			<th width="3%">ID</th>
			<th width="46%">Имя пользователя <small>Логин</small></th>
			<th width="11%">Эл.почта</th>
			<th width="10%">Последний визит</th>
			<th width="30%">Опции</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$data item=user}
		<tr>
			<td class="text-muted">{$user['id']}</td>
			<td>
				<a href="{$SCRIPT_NAME}?act=users&part=edit&user={$user['id']}">{$user['nickname']}</a>
				<br /><small>{$user['login']}</small>
			</td>
			<td class="text-left"><span class="label label-default">{$user['email']}</span></td>
			<td class="small">{$user['last_visit']}</td>
			<td>
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=users&part=edit&user={$user['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
					{if $user['id'] != 1}<a href="{$SCRIPT_NAME}?act=users&part=delete&user={$user['id']}" class="btn btn-xs btn-danger"><span class="fa fa-user-times fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>


{foreach from=$data item=user}

	<div class="panel-heading visible-xs">
		<a href="{$SCRIPT_NAME}?act=users&part=edit&user={$user['id']}">#{$user['id']} {$user['nickname']}</a>
	</div>
	<div class="panel-body visible-xs">
		Логин: {$user['login']}
		<br />Эл.почта: {$user['email']}
	</div>
	<div class="panel-body text-right visible-xs">
		<a href="{$SCRIPT_NAME}?act=users&part=edit&user={$user['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
		{if $user['id'] != 1}<a href="{$SCRIPT_NAME}?act=users&part=delete&user={$user['id']}" class="btn btn-xs btn-danger"><span class="fa fa-user-times fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
	</div>

{/foreach}