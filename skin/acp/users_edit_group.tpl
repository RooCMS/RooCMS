{* Шаблон редактирования группы *}
<div class="panel-heading">
	Редактируем группу
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=users&part=update_group&gid={$group['gid']}" role="form" class="form-horizontal">

		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Название группы: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Название должно быть уникальным" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="title" id="inputTitle" class="form-control" value="{$group['title']}" required>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-9 col-md-offset-3">
				<input type="hidden" name="empty" value="1">
				<input type="submit" name="update_group" class="btn btn-success" value="Обновить">
				<input type="submit" name="update_group_ae" class="btn btn-default" value="Обновить и выйти">
			</div>
		</div>

	</form>
</div>