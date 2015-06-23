{* Шаблон для модуля: auth *}

<div class="row">
	<div class="col-sm-12 text-right">
		<form method="post" class="form-inline">
			<div class="form-group form-group-sm text-left" style="margin-top: 10px;">
				<label class="control-label text-primary" for="inputLogin" rel="tooltip" title="Логин" data-placement="right" data-container="body"><i class="fa fa-fw fa-user-secret"></i></label>
				<br />
				<input type="text" class="form-control" id="inputLogin" aria-describedby="inputLoginStatus" required="">

			</div>
			<div class="form-group form-group-sm text-left" style="margin-top: 10px;">
				<label class="control-label text-primary" for="inputPassword"  rel="tooltip" title="Пароль" data-placement="right" data-container="body"><i class="fa fa-fw fa-lock"></i></label>
				<br />
				<input type="password" class="form-control" id="inputPassword" aria-describedby="inputPasswordStatus" required="">
			</div>
			<div class="form-group form-group-sm text-left" style="margin-top: 10px;">
				<label class="control-label text-primary" for="inputAuth">&nbsp;</label>
				<br />
				<button type="submit" name="auth" id="inputAuth" class="btn btn-default btn-sm"  rel="tooltip" title="Войти на сайт" data-placement="top" data-container="body"><i class="text-primary fa fa-fw fa-sign-in"></i></button>
			</div>

		</form>
	</div>
</div>
