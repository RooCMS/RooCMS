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

						<input type="text" name="code" id="inputConfirmationCode" class="form-control" pattern="^[\d\D]{literal}{6,16}{/literal}" aria-describedby="CCHelp" required value="{$code}">
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
						{if $config->captcha_power}
							<div class="row mb-3">
								<div class="col-sm-6 text-center d-flex flex-row justify-content-center align-items-center">
									<img src="/captcha.php" alt="Код для защиты от СПАМа" class="CaptchaCode">
									<div class="d-flex flex-column">
										<a href="#" class="badge badge-light ml-1 refresh-CaptchaCode" tabindex="-1" title="Обновить изображение"><i class="fas fa-fw fa-redo-alt"></i></a>
										<a href="#" class="badge badge-light ml-1 mt-1 recycle-CaptchaCode" tabindex="-1" title="Сменить код"><i class="fas fa-fw fa-recycle"></i></a>
										<a href="/captcha.php" class="badge badge-light ml-1 mt-1 zoom-CaptchaCode" tabindex="-1" data-fancybox="gallery_captcha" data-width="360" data-height="170" title="Увеличить изображение"><i class="fas fa-fw fa-search-plus"></i></a>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="inputCaptcha">
											Защитный код: <i class="fas fa-question-circle fa-fw" rel="tooltip" title="Из-за множества программ для СПАМа и другого вредоносного софта, мы просим Вас пройти простую проверку, доказывающую, что за компьютером сидит человек..." data-placement="top"></i></small>
										</label>
										<input type="text" name="captcha" id="inputCaptcha" class="form-control" aria-describedby="captchaHelp" placeholder="" required>
										<small id="captchaHelp" class="form-text text-muted">Введите код с картинки (буквы и цифры), что бы помочь нам защититься от СПАМа</small>
									</div>
								</div>
							</div>
						{/if}
						<input type="submit" name="confirm" class="btn btn-lg btn-success btn-block" value="Сгенерировать новый пароль">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

