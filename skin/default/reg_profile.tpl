{* Template register new user *}
<script type="text/javascript" src="/plugin/jquery.roocms.crui.min.js"></script>
<script type="text/javascript" src="/plugin/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<div class="container mb-4">
	<div class="row">
		<div class="col-12">
			<h1>
				Регистрация
			</h1>
		</div>
	</div>
	<form method="post" action="{$SCRIPT_NAME}?part=reg&act=join" role="form" enctype="multipart/form-data">
		<div class="row">
			<div class="col-lg-6">
				<div class="card card-body h-100">
					<h4 class="card-title pb-2 border-bottom">Аккаунт</h4>

					<div class="form-group">
						<label for="inputLogin">
							Ваш логин: <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с логином другого пользователя" data-placement="top"></i></small>
						</label>

						<input type="text" name="login" id="inputLogin" class="form-control" required>
					</div>

					<div class="form-group">
						<label for="inputNickname">
							Ваш псевдоним:  <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с псевдонимом другого пользователя" data-placement="top"></i></small>
						</label>

						<input type="text" name="nickname" id="inputNickname" class="form-control">
					</div>

					<div class="form-group">
						<label for="inputEmail">
							Электронная почта:  <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="top"></i></small>
						</label>

						<input type="text" name="email" id="inputEmail" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
					</div>

					<div class="form-group">
						<label for="inputEmailL">
							Почтовая рассылка:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Желаете получать рассылку от администрации сайта?" data-placement="top"></i></small>
						</label>
						<div class="btn-group btn-block btn-group-toggle roocms-crui" data-toggle="buttons">
							<label class="btn btn-light active" for="flag_status_true">
								<input type="radio" name="mailing" value="1" id="flag_status_true" checked> <i class="far fa-fw fa-check-circle"></i> <i class="fas fa-fw fa-envelope-open text-success"></i> Получать уведомления
							</label>
							<label class="btn btn-light" for="flag_status_false">
								<input type="radio" name="mailing" value="0" id="flag_status_false"> <i class="far fa-fw fa-circle"></i> <i class="fas fa-fw fa-envelope text-danger"></i> Не получать уведомления
							</label>
						</div>
					</div>

					<div class="form-group">
						<label for="inputPassword">
							Пароль:  <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Если вы оставите поле пустым, пароль для Вас будет сгенерирован автоматически." data-placement="top"></i></small>
						</label>

						<input type="text" name="password" id="inputPassword" class="form-control" minlength="5" pattern="^[\d\D]{literal}{5,}{/literal}">
					</div>

					<div class="alert alert-warning border-warning border-2 text-center d-flex align-items-center h-100 mb-0" role="alert">
						<span class="my-auto">
							<i class="fas fa-fw fa-lg mb-2 fa-exclamation-triangle"></i>
							<br />После регистрации Вам на почту придет письмо с просьбой подтвердить Ваши данные. Для заверешения регистрации, Вам будет необходимо перейти по ссылке из полученного письма. Подробная инструкция будет прилогаться.
						</span>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="card card-body h-100">
					<h4 class="card-title pb-2 border-bottom">Анкета</h4>

					<div class="form-group">
						<label for="inputUserName">
							Имя:
						</label>

						<input type="text" name="user_name" id="inputUserName" class="form-control">
					</div>

					<div class="form-group">
						<label for="inputUserSurName">
							Фамилия:
						</label>

						<input type="text" name="user_surname" id="inputUserSurName" class="form-control">
					</div>

					<div class="form-group">
						<label for="inputUserLastName">
							Отчество:
						</label>

						<input type="text" name="user_last_name" id="inputUserLastName" class="form-control">
					</div>

					<div class="form-group">
						<label for="inputUserBirthdate">
							Дата рождения:
						</label>

						<input type="text" name="user_birthdate" id="inputUserBirthdate" class="form-control datepicker">
					</div>

					<div class="form-group">
						<label for="inputUserSex">
							Пол:
						</label>

						<div class="btn-group btn-block btn-group-toggle roocms-crui" data-toggle="buttons">
							<label class="btn btn-light active">
								<input type="radio" name="user_sex" id="inputUserSex" autocomplete="off" value="n" checked><i class="far fa-fw fa-check-circle"></i><i class="fas fa-fw fa-user"></i> Не указан
							</label>
							<label class="btn btn-light">
								<input type="radio" name="user_sex" id="inputUserSexM" autocomplete="off" value="m"><i class="far fa-fw fa-circle"></i><i class="fas fa-fw fa-male"></i> Мужской
							</label>
							<label class="btn btn-light">
								<input type="radio" name="user_sex" id="inputUserSexF" autocomplete="off" value="f"><i class="far fa-fw fa-circle"></i><i class="fas fa-fw fa-female"></i> Женский
							</label>
						</div>
					</div>

					<div class="form-group">
						<label for="inputAvatar">
							Аватар:  <small><span class="fas fa-question-circle fa-fw" rel="tooltip" title="Изображение вашего профиля" data-placement="top"></span></small>
						</label>

						<div class="custom-file" id="upload-new-avatar">
							<input type="file" name="avatar" class="custom-file-input" id="inputAvatar" accept="image/*">
							<label class="custom-file-label" for="inputAvatar" data-browse="Выбрать аватар">Выберите изображение</label>
						</div>
					</div>

					<div class="form-group mb-0">
						<label for="inputUserSlogan">
							Девиз:
						</label>

						<textarea class="form-control" id="inputUserSlogan" name="user_slogan" rows="3"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 offset-lg-3">
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
						{if $config->uagreement_use}
							<p class="text-center small">Регистрируясь на сайте Вы соглашаетесь <span class="text-nowrap">с <a href="{$SCRIPT_NAME}?part=uagreement&ajax=true" data-fancybox data-animation-duration="300" data-type="ajax"><b>условиями передачи информации</b></a></span></p>
						{/if}
						<input type="submit" name="join" class="btn btn-lg btn-success btn-block" value="Зарегистрироваться">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>