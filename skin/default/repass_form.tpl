{* Шаблон для восстановления пароля *}
<div class="row">
	<div class="col-sm-12">
		<h1>Восстановление пароля</h1>
	</div>
	<div class="col-sm-12">
		<form method="post" action="{$SCRIPT_NAME}?part=repass&act=reminder" role="form" class="form-horizontal" enctype="multipart/form-data">
			<hr />
			<div class="form-group">
				<label for="inputEmail" class="col-lg-4 control-label">
					Электронная почта:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Адрес электронной почты, указанный Вами при регистрации на сайте" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="email" id="inputEmail" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-md-offset-4">
					<input type="submit" name="reminder" class="btn btn-success btn-sm" value="Восстановить">
				</div>
			</div>
		</form>
	</div>
</div>