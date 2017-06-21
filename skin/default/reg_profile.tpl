{* Шаблон регистрации нового пользователя *}

<h1>Регистрация</h1>

<div class="row">
	<div class="col-sm-12">
		<form method="post" action="{$SCRIPT_NAME}?part=reg&act=join" role="form" class="form-horizontal">
			<hr />
			<h3>Персональные данные</h3>
			<div class="form-group">
				<label for="inputNickname" class="col-lg-4 control-label">
					Ваш псевдоним:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с псевдонимом другого пользователя" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="nickname" id="inputNickname" class="form-control" required>
				</div>
			</div>

			<div class="form-group">
				<label for="inputLogin" class="col-lg-4 control-label">
					Ваш логин: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с логином другого пользователя" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="login" id="inputLogin" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="inputEmail" class="col-lg-4 control-label">
					Электронная почта:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Нельзя заводить несколько аккаунтов на один почтовый ящик. После регистрации на почту будет отправлен код подтверждения." data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<div class="input-group">
						<input type="text" name="email" id="inputEmail" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
						<div class="input-group-btn" data-toggle="buttons">
							<label class="btn btn-default active" for="flag_status_true" rel="tooltip" title="Получать рассылку" data-placement="auto" data-container="body">
								<input type="radio" name="mailing" value="1" id="flag_status_true" checked> <span class="text-success"><i class="fa fa-fw fa-envelope-open"></i></span>
							</label>
							<label class="btn btn-default" for="flag_status_false" rel="tooltip" title="Не получать рассылку" data-placement="auto" data-container="body">
								<input type="radio" name="mailing" value="0" id="flag_status_false"> <span class="text-danger"><i class="fa fa-fw fa-envelope"></i></span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="inputPassword" class="col-lg-4 control-label">
					Пароль:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Оставьте поле пустым, если не хотите менять пароль." data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="password" id="inputPassword" class="form-control"  pattern="^[\d\D]{literal}{5,}{/literal}" required>
				</div>
			</div>

			<hr />
			<h3>Анкетные данные</h3>

			<div class="form-group">
				<label for="inputUserName" class="col-lg-4 control-label">
					Имя:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_name" id="inputUserName" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserSurName" class="col-lg-4 control-label">
					Фамилия:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_surname" id="inputUserSurName" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserLastName" class="col-lg-4 control-label">
					Отчество:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_last_name" id="inputUserLastName" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserBirthdate" class="col-lg-4 control-label">
					Дата рождения:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_birthdate" id="inputUserBirthdate" class="form-control datepicker">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserSex" class="col-lg-4 control-label">
					Пол:
				</label>
				<div class="col-lg-8">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default active">
							<input type="radio" name="user_sex" id="inputUserSex" autocomplete="off" value="n" checked><i class="fa fa-fw fa-user"></i> Не указан
						</label>
						<label class="btn btn-default">
							<input type="radio" name="user_sex" id="inputUserSexM" autocomplete="off" value="m"><i class="fa fa-fw fa-male"></i> Мужской
						</label>
						<label class="btn btn-default">
							<input type="radio" name="user_sex" id="inputUserSexF" autocomplete="off" value="f"><i class="fa fa-fw fa-female"></i> Женский
						</label>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="inputAvatar" class="col-lg-4 control-label">
					Аватар:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Изображение вашего профиля" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="file" name="avatar" id="inputAvatar" class="btn btn-default">
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-md-offset-4">
					<input type="submit" name="join" class="btn btn-success btn-sm" value="Зарегистрироваться">
				</div>
			</div>
		</form>
	</div>
</div>