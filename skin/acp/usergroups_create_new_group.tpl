{* Template Create Group Users *}
<div class="card-header">
	Новая группа
</div>
<form method="post" action="{$SCRIPT_NAME}?act=usergroups&part=create_group" role="form">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Название группы: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Название должно быть уникальным" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-md-7 offset-md-5 col-lg-8 offset-lg-4">
				<input type="submit" name="create_group" class="btn btn-lg btn-success" value="Создать">
				<input type="submit" name="create_group['ae']" class="btn btn-lg btn-outline-success" value="Создать и выйти">
			</div>
		</div>
	</div>
</form>