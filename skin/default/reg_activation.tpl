{* User activation template *}
<div class="container">
	<div class="row">
		<div class="col-12">
			<h1>Активация аккаунта</h1>
		</div>
	</div>
	<form method="post" action="{$SCRIPT_NAME}?part=reg&act=verification" role="form" class="needs-validation" novalidate>
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
						<label for="inputActivationCode">
							Код активации:
						</label>

						<input type="text" name="code" id="inputActivationCode" class="form-control" pattern="^[\d\D]{literal}{6,10}{/literal}" aria-describedby="ACHelp" required value="{$code}">
						<div class="invalid-tooltip">
							Вы должные ввести код активации, который был выслан на Вашу почту.
						</div>
						<small id="ACHelp" class="form-text text-gray">Укажите код активации, полученный Вами по электронной почте.</small>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="card mt-4">
					<div class="card-body text-center">
						<input type="submit" name="activate" class="btn btn-lg btn-success btn-block" value="Активировать аккаунт">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
