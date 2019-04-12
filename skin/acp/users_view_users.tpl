{* Userlist template *}
<div class="card-header">
	Пользователи
</div>

<table class="table table-hover table-condensed d-none d-sm-table mb-0">
	<thead class="bg-light">
	<tr class="active">
		<th width="2%">ID</th>
		<th width="3%">Аватар</th>
		<th width="53%">Имя пользователя <small>Логин и E-mail</small></th>
		<th width="11%">Титул</th>
		<th width="10%" class="d-none d-md-table-cell">Последний визит</th>
		<th width="20%" class="text-right">Опции</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$data item=user}
		<tr{if $user['status'] == 0 && $user['activation_code'] == ""} class="danger"{elseif $user['status'] == 0 && $user['activation_code'] != ""} class="warning"{/if} title="{$user['user_slogan']}">
			<td class="align-middle text-muted">{$user['uid']}</td>
			<td class="align-middle">
				{if $user['avatar'] != ""}<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}"><img src="/upload/images/{$user['avatar']}" height="40" class="border border-{if $user['ban'] == 1 || $user['status'] == 0}danger{else}secondary{/if} rounded-circle" alt="{$user['nickname']}"></a>{/if}
			</td>
			<td class="align-middle">
				<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}">{$user['nickname']}</a>
				{if $user['user_sex'] == "m"}<i class="fas fa-fw fa-mars text-info"></i>{elseif $user['user_sex'] == "f"}<i class="fas fa-fw fa-venus text-danger"></i>{/if}
				{if $user['gtitle'] != ""}<span class="badge badge-light float-right d-none d-md-block">{$user['gtitle']}</span>{/if}
				<br /><small>{$user['login']} ({$user['email']}) <i class="fas fa-fw {if $user['mailing'] == 0}fa-envelope text-muted{else}fa-envelope-open text-success{/if}"></i></small>
			</td>
			<td class="align-middle text-left">
				{if $user['title'] == "a" && $user['uid'] == 1}
					<span class="badge badge-primary">Супер Администратор</span>
				{elseif $user['title'] == "a" && $user['uid'] != 1}
					<span class="badge badge-info">Администратор</span>
				{else}
					<span class="badge badge-light">Пользователь</span>
				{/if}
				{if $user['ban'] == 1}<span class="badge badge-danger d-none d-md-block">Заблокирован</span>{/if}
				{if $user['status'] == 0 && $user['activation_code'] != ""}<span class="badge badge-warning d-none d-sm-block">Не активирован</span>{/if}
				{if $user['status'] == 0 && $user['activation_code'] == ""}<span class="badge badge-gray d-none d-sm-block">Отключен</span>{/if}
			</td>
			<td class="align-middle small d-none d-md-table-cell" title="Зарегестрирован: {$user['date_create']}">
				{$user['last_visit']}
			</td>
			<td class="align-middle text-right">
				<div class="btn-group btn-group-sm">
					<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}" class="btn btn-outline-primary"><i class="fas fa-user-edit fa-fw"></i><span class="d-none d-lg-block">Редактировать</span></a>
					{if $user['uid'] != 1}<a href="{$SCRIPT_NAME}?act=users&part=delete_user&uid={$user['uid']}" class="btn btn-danger"><i class="fas fa-user-times fa-fw"></i><span class="d-none d-lg-block">Удалить</span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>


<table class="table table-hover d-block-table d-sm-none mb-0">
	<tbody>
	{foreach from=$data item=user}
		<tr{if $user['status'] == 0 && $user['activation_code'] == ""} class="table-danger"{elseif $user['status'] == 0 && $user['activation_code'] != ""} class="table-warning"{/if}>
			<td class="align-middle">
				{if $user['avatar'] != ""}<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}"><img src="/upload/images/{$user['avatar']}" height="40" class="border border-{if $user['ban'] == 1 || $user['status'] == 0}danger{else}secondary{/if} rounded-circle" alt="{$user['nickname']}"></a>{/if}
			</td>
			<td class="align-middle w-75">
				<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}"><!-- #{$user['uid']} --> {$user['nickname']}</a>
				<br />
				{if $user['title'] == "a" && $user['uid'] == 1}
					<span class="badge badge-primary">Супер Администратор</span>
				{elseif $user['title'] == "a" && $user['uid'] != 1}
					<span class="badge badge-info">Администратор</span>
				{else}
					<span class="badge badge-light">Пользователь</span>
				{/if}
			</td>
			<td class="w-25 align-middle text-right">
				<div class="btn-group btn-group-sm">
					<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}" class="btn btn-outline-primary"><span class="fas fa-user-edit fa-fw"></span></a>
					{if $user['uid'] != 1}<a href="{$SCRIPT_NAME}?act=users&part=delete_user&uid={$user['uid']}" class="btn btn-danger"><span class="fas fa-user-times fa-fw"></span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>