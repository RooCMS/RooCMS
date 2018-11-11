{* Шаблон для подтверждения запроса на смену пароля *}
<div class="row">
	<div class="col-sm-12">
		<h1>Подтверждения запроса на смену пароля</h1>
	</div>
	<div class="col-sm-12">
		<form method="post" action="{$SCRIPT_NAME}?part=repass&act=verification" role="form" class="form-horizontal" enctype="multipart/form-data">
			<hr />
			<div class="form-group">
				<label for="inputEmail" class="col-lg-4 control-label">
					Электронная почта: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Адрес Вашей электронной почты" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="email" id="inputEmail" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required value="{$email}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputConfirmationCode" class="col-lg-4 control-label">
					Код подтверждения:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Секретный код, отправленный Вам на почту." data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="code" id="inputConfirmationCode" class="form-control"  pattern="^[\d\D]{literal}{5,}{/literal}" required value="{$code}">
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-md-offset-4">
					<input type="submit" name="confirm" class="btn btn-success btn-sm" value="Сгенерировать новый пароль">
				</div>
			</div>
		</form>
	</div>
</div>