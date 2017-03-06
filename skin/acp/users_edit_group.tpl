{* Шаблон редактирования группы *}
<div class="panel-heading">
	Редактируем группу
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=users&part=update_group&gid={$group['gid']}" role="form" class="form-horizontal">

		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Название группы: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Название должно быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="title" id="inputTitle" class="form-control" value="{$group['title']}" required>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<hr />
				<h5 class="text-info">Основные участники группы:</h5>
				{foreach from=$users item=user}<a rel="popover" role="button" data-placement="top" data-toggle="popover" title="{$user['uid']} : {$user['nickname']}" data-html="true" data-content="<a href='{$SCRIPT_NAME}?act=users&part=edit_user&uid={$user['uid']}' class='btn btn-info btn-xs btn-block'><i class='fa fa-fw fa-user-circle-o'></i> Профиль UID:{$user['uid']}</a> <a href='{$SCRIPT_NAME}?act=users&part=exclude_user_group&uid={$user['uid']}&gid={$group['gid']}' class='btn btn-warning btn-xs btn-block'><i class='fa fa-fw fa-user-times'></i> Исключить из группы</a>"><img src="/upload/images/{$user['avatar']}" class="img-circle avatar{if $user['ban'] == 1 || $user['status'] == 0} ban{/if}"></a>{/foreach}
			</div>
		</div>

		<div class="row">
			<div class="col-lg-9 col-md-offset-3">
				<br />
				<input type="submit" name="update_group" class="btn btn-success" value="Обновить">
				<input type="submit" name="update_group_ae" class="btn btn-default" value="Обновить и выйти">
			</div>
		</div>

	</form>
</div>