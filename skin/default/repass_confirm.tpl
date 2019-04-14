{* Template RePass Confirm *}
<div class="container mb-4">
	<div class="row">
		<div class="col-12">
			<h1>Подтверждения запроса на смену пароля</h1>
		</div>
	</div>
	<form method="post" action="{$SCRIPT_NAME}?part=repass&act=verification" role="form" class="needs-validation" novalidate>
		<div class="row">
			<div class="col-12">
				<div class="card card-body">
					<div class="form-group position-relative">
						<label for="inputEmail">
							Электронная почта:
						</label>

						<input type="text" name="email" id="inputEmail" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" aria-describedby="emailHelp" required value="{$email}">
						<div class="invalid-tooltip">
							Укажите корректный e-mail который вы использовали для регистрации на сайте.
						</div>
						<small id="emailHelp" class="form-text text-gray">Адрес электронной почты, указанный Вами при регистрации на сайте.</small>
					</div>

					<div class="form-group position-relative">
						<label for="inputConfirmationCode">
							Код подтверждения:
						</label>

						<input type="text" name="code" id="inputConfirmationCode" class="form-control" pattern="^[\d\D]{literal}{5,}{/literal}" aria-describedby="CCHelp" required value="{$code}">
						<div class="invalid-tooltip">
							Вы должные ввести проверочный код, который был выслан на Вашу почту.
						</div>
						<small id="CCHelp" class="form-text text-gray">Укажите проверочный код, полученный Вами по электронной почте.</small>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="card mt-4">
					<div class="card-body text-center">
						<input type="submit" name="confirm" class="btn btn-lg btn-success btn-block" value="Сгенерировать новый пароль">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

