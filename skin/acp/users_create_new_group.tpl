{* Шаблон создания новой группы *}
<div class="panel-heading">
	Новая группа
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=users&part=create_group" role="form" class="form-horizontal">

		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Название группы: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Название должно быть уникальным" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="title" id="inputTitle" class="form-control" required>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-9 col-md-offset-3">
				<input type="hidden" name="empty" value="1">
				<input type="submit" name="create_group" class="btn btn-success" value="Создать">
				<input type="submit" name="create_group_ae" class="btn btn-default" value="Создать и выйти">
			</div>
		</div>

	</form>
</div>