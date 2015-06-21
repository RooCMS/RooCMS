{* Шаблон отображения списка пользователей *}
<div class="panel-heading">
	Пользователи
</div>


<table class="table table-hover table-condensed hidden-xs">
	<thead>
		<tr class="active">
			<th width="3%">ID</th>
			<th width="46%">Имя пользователя <small>Логин и E-mail</small></th>
			<th width="11%">Титул</th>
			<th width="10%">Последний визит</th>
			<th width="30%">Опции</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$data item=user}
		<tr{if $user['status'] == 0} class="danger"{/if}>
			<td class="text-muted">{$user['uid']}</td>
			<td>
				<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}">{$user['nickname']}</a>
				<br /><small>{$user['login']} ({$user['email']})</small>
			</td>
			<td class="text-left">
				{if $user['title'] == "a" && $user['uid'] == 1}
					<span class="label label-primary">Супер Администратор</span>
				{elseif $user['title'] == "a" && $user['uid'] != 1}
					<span class="label label-info">Администратор</span>
				{else}
					<span class="label label-default">Пользователь</span>
				{/if}
			</td>
			<td class="small">{$user['last_visit']}</td>
			<td>
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
					{if $user['uid'] != 1}<a href="{$SCRIPT_NAME}?act=users&part=delete_user&uid={$user['uid']}" class="btn btn-xs btn-danger"><span class="fa fa-user-times fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>


{foreach from=$data item=user}

	<div class="panel-heading visible-xs">
		{if $user['status'] == 0}<span style="text-decoration: line-through;">{/if}<a href="{$SCRIPT_NAME}?act=users&part=edit_user&user={$user['uid']}">#{$user['uid']} {$user['nickname']}</a>{if $user['status'] == 0}</span>{/if}
		{if $user['title'] == "a" && $user['uid'] == 1}
			<span class="label label-primary">Супер Администратор</span>
		{elseif $user['title'] == "a" && $user['uid'] != 1}
			<span class="label label-info">Администратор</span>
		{else}
			<span class="label label-default">Пользователь</span>
		{/if}
	</div>
	<div class="panel-body visible-xs">
		Логин: {$user['login']}
		<br />Эл.почта: {$user['email']}
	</div>
	<div class="panel-body text-right visible-xs">
		<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
		{if $user['uid'] != 1}<a href="{$SCRIPT_NAME}?act=users&part=delete_user&uid={$user['uid']}" class="btn btn-xs btn-danger"><span class="fa fa-user-times fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
	</div>

{/foreach}