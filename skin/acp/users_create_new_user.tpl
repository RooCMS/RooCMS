{* Шаблон создания нового пользователя *}
<div class="panel-heading">
	Новый пользователь
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=users&part=create_user" enctype="multipart/form-data" role="form" class="form-horizontal">

		<h5 class="text-info">Персональные данные:</h5>

		<div class="form-group">
			<label for="inputNickname" class="col-lg-3 control-label">
				Псевдоним пользователя:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Должен быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="nickname" id="inputNickname" class="form-control" spellcheck required>
			</div>
		</div>

		<div class="form-group">
			<label for="inputLogin" class="col-lg-3 control-label">
				Логин пользователя: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Должен быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="login" id="inputLogin" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="inputPassword" class="col-lg-3 control-label">
				Пароль:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Минимум: 5 символов" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="password" id="inputPassword" class="form-control" pattern="^[\d\D]{literal}{5,}{/literal}">
			</div>
		</div>

		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">
				Электронная почта:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<div class="input-group">
					<input type="text" name="email" id="inputEmail" class="form-control" pattern="[A-Za-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
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

		<hr />
		<h5 class="text-info">Анкетные данные:</h5>

		<div class="form-group">
			<label for="inputUserName" class="col-lg-3 control-label">
				Имя:
			</label>
			<div class="col-lg-9">
				<input type="text" name="user_name" id="inputUserName" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="inputUserSurName" class="col-lg-3 control-label">
				Фамилия:
			</label>
			<div class="col-lg-9">
				<input type="text" name="user_surname" id="inputUserSurName" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="inputUserLastName" class="col-lg-3 control-label">
				Отчество:
			</label>
			<div class="col-lg-9">
				<input type="text" name="user_last_name" id="inputUserLastName" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="inputUserBirthdate" class="col-lg-3 control-label">
				Дата рождения:
			</label>
			<div class="col-lg-9">
				<input type="text" name="user_birthdate" id="inputUserBirthdate" class="form-control datepicker form-date">
			</div>
		</div>

		<div class="form-group">
			<label for="inputUserSlogan" class="col-lg-3 control-label">
				Девиз:
			</label>
			<div class="col-lg-9">
				<textarea class="form-control" id="inputUserSlogan" name="user_slogan" rows="3" spellcheck></textarea>
			</div>
		</div>

		<div class="form-group">
			<label for="inputUserSex" class="col-lg-3 control-label">
				Пол:
			</label>
			<div class="col-lg-9">
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
			<label for="inputAvatar" class="col-lg-3 control-label">
				Аватар:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="{$config->users_avatar_width}x{$config->users_avatar_height} пикселей" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="file" name="avatar" id="inputAvatar" class="btn btn-default">
			</div>
		</div>

		<hr />
		<h5 class="text-info">Права и группа пользователя:</h5>

		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Титул:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Администраторы могут получить доступ к Панели Управления" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<select name="title"  id="inputTitle" class="selectpicker show-tick" data-size="auto" data-width="50%">
					<option value="a">Администратор</option>
					<option value="u" selected>Пользователь</option>
				</select>
			</div>
		</div>

		{if !empty($groups)}
			<div class="form-group">
				<label for="inputGroups" class="col-lg-3 control-label">
					Основная группа пользователя:
				</label>
				<div class="col-lg-9">
					<select name="gid" id="inputGroups" class="selectpicker show-tick" required data-header="Группы пользователей" data-size="auto" data-live-search="true" data-width="50%">
						<option value="0" selected>Не состоит в группе</option>
						{foreach from=$groups item=group}
							<option value="{$group['gid']}" data-subtext="В группе {$group['users']} пользователей">{$group['title']}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}

		<div class="row">
			<div class="col-lg-9 col-md-offset-3">
				<input type="submit" name="create_user" class="btn btn-success" value="Создать">
				<input type="submit" name="create_user_ae" class="btn btn-default" value="Создать и выйти">
			</div>
		</div>

	</form>
</div>