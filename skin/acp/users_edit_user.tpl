{* Template User Edit *}
<div class="card-header">
	Редактируем пользователя #{$user['uid']} {$user['nickname']}
</div>
<form method="post" action="{$SCRIPT_NAME}?act=users&part=update_user&uid={$user['uid']}" enctype="multipart/form-data" role="form">
	<div class="card-body">
		{if $user['uid'] != 1}
		<div class="form-group row">
			<label for="inputNickname" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Вкл/Выкл учетной записи:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Принудительное включение/отключение учетной записи пользователя администрацией сайта" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
					<label class="btn btn-light{if $user['status'] == 1} active{/if}" for="flag_status_true" rel="tooltip" title="Учетная запись активна" data-placement="auto" data-container="body">
						<input type="radio" name="status" value="1" id="flag_status_true"{if $user['status'] == 1} checked{/if}> <i class="far fa-fw fa{if $user['status'] == 1}-check{/if}-circle"></i> <i class="fa fa-fw fa-eye text-success"></i>
					</label>
					<label class="btn btn-light{if $user['status'] == 0} active{/if}" for="flag_status_false" rel="tooltip" title="Учетная запись отключена" data-placement="auto" data-container="body">
						<input type="radio" name="status" value="0" id="flag_status_false"{if $user['status'] == 0} checked{/if}> <i class="far fa-fw fa{if $user['status'] == 0}-check{/if}-circle"></i> <i class="fa fa-fw fa-eye-slash text-danger"></i>
					</label>
				</div>
			</div>
		</div>
		{/if}

		{if $i_am_groot}
		<blockquote class="quote quote-warning">
			Внимание! Вы редактируете собственные данные.
			<br />По завершению редактирования RooCMS может попросить вас заново указать ваш логин и пароль для авторизации.
			<br />В некоторых случаях, вы можете увидеть предупрждение системы безопастности RooCMS о попытке подмены данных. В этом случае вам не стоит волноваться, потому что это просто срабатывание защиты Панели Управления от несанкционированного доступа.
		</blockquote>
		{/if}

		<h5 class="text-secondary">Персональные данные:</h5>

		<div class="form-group row">
			<label for="inputNickname" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Псевдоним пользователя:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Должен быть уникальным" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="nickname" id="inputNickname" class="form-control" value="{$user['nickname']}" spellcheck required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputLogin" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Логин пользователя: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Должен быть уникальным" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="login" id="inputLogin" class="form-control" value="{$user['login']}" required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputPassword" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Пароль:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Оставьте поле пустым, что бы не менять пароль." data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="password" id="inputPassword" class="form-control"  pattern="^[\d\D]{literal}{5,}{/literal}">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputEmail" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Электронная почта:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="email" id="inputEmail" class="form-control"  value="{$user['email']}" pattern="[A-Za-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>

			</div>
		</div>

		<div class="form-group row">
			<label for="inputEmailL" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Почтовая рассылка:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Подписан ли пользователь на почтовую рассылку" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
					<label class="btn btn-light{if $user['mailing'] == 1} active{/if}" for="flag_status_true_email" rel="tooltip" title="Получать рассылку" data-placement="auto" data-container="body">
						<input type="radio" name="mailing" value="1" id="flag_status_true_email"{if $user['mailing'] == 1} checked{/if}> <i class="far fa-fw fa-{if $user['mailing'] == 1}check-{/if}circle"></i> <i class="fa fa-fw fa-envelope-open text-success"></i> Получать уведомления
					</label>
					<label class="btn btn-light{if $user['mailing'] == 0} active{/if}" for="flag_status_false_email" rel="tooltip" title="Не получать рассылку" data-placement="auto" data-container="body">
						<input type="radio" name="mailing" value="0" id="flag_status_false_email"{if $user['mailing'] == 0} checked{/if}> <i class="far fa-fw fa-{if $user['mailing'] == 0}check-{/if}circle"></i> <i class="fa fa-fw fa-envelope text-danger"></i> Не получать уведомления
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
				<input type="text" name="user_name" id="inputUserName" class="form-control" value="{$user['user_name']}">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserSurName" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Фамилия:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="user_surname" id="inputUserSurName" class="form-control" value="{$user['user_surname']}">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserLastName" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Отчество:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="user_last_name" id="inputUserLastName" class="form-control" value="{$user['user_last_name']}">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserBirthdate" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Дата рождения:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="user_birthdate" id="inputUserBirthdate" value="{if $user['user_birthdate'] != 0}{$user['user_birthdate']}{/if}" class="form-control datepicker" data-date-end-date="-1y" data-date-start-view="2">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserSlogan" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Девиз:
			</label>
			<div class="col-md-7 col-lg-8">
				<textarea class="form-control" id="inputUserSlogan" name="user_slogan" rows="3" spellcheck>{$user['user_slogan']}</textarea>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputUserSex" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Пол:
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
					<label class="btn btn-light{if $user['user_sex'] == "n"} active{/if}">
						<input type="radio" name="user_sex" id="inputUserSex" autocomplete="off" value="n"{if $user['user_sex'] == "n"} checked{/if}><i class="far fa-fw fa{if $user['user_sex'] == "n"}-check{/if}-circle"></i><i class="fa fa-fw fa-user"></i> Не указан
					</label>
					<label class="btn btn-light{if $user['user_sex'] == "m"} active{/if}">
						<input type="radio" name="user_sex" id="inputUserSexM" autocomplete="off" value="m"{if $user['user_sex'] == "m"} checked{/if}><i class="far fa-fw fa{if $user['user_sex'] == "m"}-check{/if}-circle"></i><i class="fa fa-fw fa-male"></i> Мужской
					</label>
					<label class="btn btn-light{if $user['user_sex'] == "f"} active{/if}">
						<input type="radio" name="user_sex" id="inputUserSexF" autocomplete="off" value="f"{if $user['user_sex'] == "f"} checked{/if}><i class="far fa-fw fa{if $user['user_sex'] == "f"}-check{/if}-circle"></i><i class="fa fa-fw fa-female"></i> Женский
					</label>
				</div>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputAvatar" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Аватар:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="{$config->users_avatar_width}x{$config->users_avatar_height} пикселей" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				{if $user['avatar'] != ""}
					<span class="d-inline position-relative" id="ua-{$user['uid']}">
						<img src="/upload/images/{$user['avatar']}" height="50" class="rounded-circle border" alt="{$user['nickname']}">
						<i id="dua-{$user['uid']}" class="fas fa-fw fa-times-circle fa-icon-action del" rel="tooltip" title="Удалить аватар пользователя" data-placement="top"></i>
					</span>
				{/if}
				<div class="custom-file w-75 ml-2 mb-3">
					<input type="file" name="avatar" class="custom-file-input" id="inputAvatar" multiple accept="{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}">
					<label class="custom-file-label" for="inputAvatar" data-browse="Выбрать">Выберите изображение</label>
				</div>
			</div>
		</div>

		<hr />
		<h5 class="text-secondary">Права и группа пользователя:</h5>

		{if $user['uid'] != 1}
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Титул:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Администраторы могут получить доступ к Панели Управления" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="row">
					<div class="col-12 col-lg-6">
						<select name="title"  id="inputTitle" class="selectpicker" data-size="auto" data-width="100%">
							<option value="a" {if $user['title'] == "a"}selected{/if}>Администратор</option>
							<option value="u" {if $user['title'] == "u"}selected{/if}>Пользователь</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		{/if}

		{if !empty($groups)}
		<div class="form-group row">
			<label for="inputGroups" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Основная группа пользователя:
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="row">
					<div class="col-12 col-lg-6">
						<select name="gid" id="inputGroups" class="selectpicker" required data-header="Группы пользователей" data-size="auto" data-live-search="true" data-width="100%">
							<option value="0" {if $user['gid'] == 0}selected{/if}>Не состоит в группе</option>
							{foreach from=$groups item=group}
								<option value="{$group['gid']}" data-subtext="В группе {$group['users']} пользователей" {if $group['gid'] == $user['gid']}selected{/if}>{$group['title']}</option>
							{/foreach}
						</select>
						<input type="hidden" name="now_gid" value="{$user['gid']}" readonly>
					</div>
				</div>
			</div>
		</div>
		{/if}
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-md-7 offset-md-5 col-lg-8 offset-lg-4">
				<input type="submit" name="update_user" class="btn btn-lg btn-success" value="Обновить">
				<input type="submit" name="update_user['ae']" class="btn btn-lg btn-outline-success" value="Обновить и выйти">
			</div>
		</div>
	</div>
</form>


<script>
	{literal}
	(function($) {
		"use strict";
		$(window).on('load', function() {
			$('[id^=dua]').on('click', function() {
				let attrdata = $(this).attr('id');
				let arrdata = attrdata.split('-');
				let uid = arrdata[1];

				$("#ua-"+uid).load('/acp.php?act=ajax&part=delete_user_avatar&uid='+uid, function() {
					$("#ua-"+uid).animate({'opacity':'0.2'}, 750, function() {
						$("#ua-"+uid).hide(600).delay(900).remove();
					});
				});

			});
		});
	})(jQuery);
	{/literal}
</script>
