{* Template Group Edit *}
<div class="card-header">
	Редактируем группу
</div>
<form method="post" action="{$SCRIPT_NAME}?act=usergroups&part=update_group&gid={$group['gid']}" role="form">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Название группы: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Название должно быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" value="{$group['title']}" spellcheck required>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<hr />
				<h5 class="text-secondary">Основные участники группы:</h5>
				{foreach from=$users item=user}<a rel="popover" class="mr-1" role="button" data-placement="top" data-toggle="popover" title="{$user['uid']} : {$user['nickname']}" data-html="true" data-content="<a href='{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}' class='btn btn-outline-secondary btn-sm btn-block'><i class='far fa-fw fa-user-circle'></i> Профиль UID:{$user['uid']}</a> <a href='{$SCRIPT_NAME}?act=usergroups&part=exclude_user_group&uid={$user['uid']}&gid={$group['gid']}' class='btn btn-outline-warning btn-sm btn-block'><i class='fas fa-fw fa-user-times'></i> Исключить из группы</a>"><img src="/upload/images/{$user['avatar']}" class="rounded-circle border hover-cursor{if $user['ban'] == 1 || $user['status'] == 0} border-danger{/if}" alt="{$user['nickname']}" style="height: 3rem;"></a>{/foreach}
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-md-7 offset-md-5 col-lg-8 offset-lg-4">
				<input type="submit" name="update_group" class="btn btn-lg btn-success" value="Обновить">
				<input type="submit" name="update_group['ae']" class="btn btn-lg btn-outline-success" value="Обновить и выйти">
			</div>
		</div>
	</div>
</form>