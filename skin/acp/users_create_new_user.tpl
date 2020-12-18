{* Шаблон создания нового пользователя *}
<div class="card-header">
	Новый пользователь
</div>
<form method="post" action="{$SCRIPT_NAME}?act=users&part=create_user" enctype="multipart/form-data" role="form">
	<div class="card-body">

		<h5 class="text-secondary">Персональные данные:</h5>

		<div class="form-group row">
			<label for="inputNickname" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Псевдоним пользователя:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Должен быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="nickname" id="inputNickname" class="form-control" spellcheck required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputLogin" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Логин пользователя: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Должен быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="login" id="inputLogin" class="form-control">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputPassword" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Пароль:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Минимум: 5 символов" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="password" id="inputPassword" class="form-control" pattern="^[\d\D]{literal}{5,}{/literal}">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputEmail" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Электронная почта:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="email" id="inputEmail" class="form-control" pattern="[A-Za-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputEmail" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Почтовая рассылка:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Подписан ли пользователь на почтовую рассылку" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
					<label class="btn btn-light active" for="flag_status_true_email" rel="tooltip" title="Получать рассылку" data-placement="auto" data-container="body">
						<input type="radio" name="mailing" value="1" id="flag_status_true_email" checked> <i class="far fa-fw fa-check-circle"></i> <i class="fa fa-fw fa-envelope-open text-success"></i> Получать уведомления
					</label>
					<label class="btn btn-light" for="flag_status_false_email" rel="tooltip" title="Не получать рассылку" data-placement="auto" data-container="body">
						<input type="radio" name="mailing" value="0" id="flag_status_false_email"> <i class="far fa-fw fa-circle"></i> <i class="fa fa-fw fa-envelope text-danger"></i> Не получать уведомления
					</label>
				</div>
			</div>
		</div>



		<hr />
		<h5 class="text-secondary">Анкетные данные:</h5>

		<div class="form-group row">
			<label for="inputUserName" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Имя:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="user_name" id="inputUserName" class="form-control">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserSurName" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Фамилия:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="user_surname" id="inputUserSurName" class="form-control">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserLastName" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Отчество:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="user_last_name" id="inputUserLastName" class="form-control">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserBirthdate" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Дата рождения:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="user_birthdate" id="inputUserBirthdate" class="form-control datepicker" data-date-end-date="-1y" data-date-start-view="2">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserSlogan" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Девиз:
			</label>
			<div class="col-md-7 col-lg-8">
				<textarea class="form-control" id="inputUserSlogan" name="user_slogan" rows="3" spellcheck></textarea>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserSex" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Пол:
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
					<label class="btn btn-light active">
						<input type="radio" name="user_sex" id="inputUserSex" autocomplete="off" value="n" checked><i class="far fa-fw fa-check-circle"></i><i class="fa fa-fw fa-user"></i> Не указан
					</label>
					<label class="btn btn-light">
						<input type="radio" name="user_sex" id="inputUserSexM" autocomplete="off" value="m"><i class="far fa-fw fa-circle"></i><i class="fa fa-fw fa-male"></i> Мужской
					</label>
					<label class="btn btn-light">
						<input type="radio" name="user_sex" id="inputUserSexF" autocomplete="off" value="f"><i class="far fa-fw fa-circle"></i><i class="fa fa-fw fa-female"></i> Женский
					</label>
				</div>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputAvatar" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Аватар:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="{$config->users_avatar_width}x{$config->users_avatar_height} пикселей" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="custom-file mb-3">
					<input type="file" name="avatar" class="custom-file-input" id="inputAvatar" multiple accept="{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}">
					<label class="custom-file-label" for="inputAvatar" data-browse="Выбрать">Выберите изображение</label>
				</div>
			</div>
		</div>

		<hr />
		<h5 class="text-secondary">Права и группа пользователя:</h5>

		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Титул:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Администраторы могут получить доступ к Панели Управления" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="row">
					<div class="col-12 col-lg-6">
						<select name="title" id="inputTitle" class="selectpicker" data-size="auto" data-width="100%">
							<option value="a">Администратор</option>
							<option value="u" selected>Пользователь</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		{if !empty($groups)}
			<div class="form-group row">
				<label for="inputGroups" class="col-md-5 col-lg-4 form-control-plaintext text-right">
					Основная группа пользователя:
				</label>
				<div class="col-md-7 col-lg-8">
					<div class="row">
						<div class="col-12 col-lg-6">
							<select name="gid" id="inputGroups" class="selectpicker" required data-header="Группы пользователей" data-size="auto" data-live-search="true" data-width="100%">
								<option value="0" selected>Не состоит в группе</option>
								{foreach from=$groups item=group}
									<option value="{$group['gid']}" data-subtext="В группе {$group['users']} пользователей">{$group['title']}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
			</div>
		{/if}
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-md-7 offset-md-5 col-lg-8 offset-lg-4">
				<input type="submit" name="create_user" class="btn btn-lg btn-success" value="Создать">
				<input type="submit" name="create_user['ae']" class="btn btn-lg  btn-outline-success" value="Создать и выйти">
			</div>
		</div>
	</div>
</form>