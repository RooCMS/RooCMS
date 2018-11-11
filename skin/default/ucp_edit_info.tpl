{* Шаблон редактирования личных данных пользователя *}
<div class="row">
	<div class="col-sm-12">
		<h1>Личный кабинет</h1>
		<hr>
	</div>
</div>

<div class="row">

	{if $userdata['avatar'] != ""}
		<div class="col-xs-3 col-sm-2 col-md-2 text-center"><img src="/upload/images/{$userdata['avatar']}"  class="img-thumbnail"></div>
	{/if}


	<div class="{if $userdata['avatar'] != ""}col-xs-9 col-sm-10 col-md-10{else}col-xs-12{/if}">

		<h2 class="ucp-user-nickname">
			{$userdata['nickname']} {if $userdata['user_sex'] == "m"}<i class="fa fa-fw fa-mars text-info"></i>{elseif $userdata['user_sex'] == "f"}<i class="fa fa-fw fa-venus text-danger"></i>{/if}
		</h2>
		<div class="ucp-user-slogan">{$userdata['user_slogan']}</div>
		<small class="label label-primary">{$userdata['gtitle']}</small>
	</div>
	<div class="col-xs-12">
		<hr />
	</div>
</div>

<div class="row">
	<div class="col-sm-8">
		<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=ucp&move=update_info" role="form" class="form-horizontal" enctype="multipart/form-data">
			<h3>Персональные данные</h3>
			<div class="form-group">
				<label for="inputLogin" class="col-lg-4 control-label">
					Ваш логин: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с логином другого пользователя" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="login" id="inputLogin" class="form-control" value="{$userdata['login']}" required>
				</div>
			</div>

			<div class="form-group">
				<label for="inputNickname" class="col-lg-4 control-label">
					Ваш псевдоним:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с псевдонимом другого пользователя" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="nickname" id="inputNickname" class="form-control" value="{$userdata['nickname']}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputEmail" class="col-lg-4 control-label">
					Электронная почта:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<div class="input-group">
						<input type="text" name="email" id="inputEmail" class="form-control"  value="{$userdata['email']}" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
						<div class="input-group-btn" data-toggle="buttons">
							<label class="btn btn-default{if $userdata['mailing'] == 1} active{/if}" for="flag_status_true" rel="tooltip" title="Получать рассылку" data-placement="auto" data-container="body">
								<input type="radio" name="mailing" value="1" id="flag_status_true"{if $userdata['mailing'] == 1} checked{/if}> <span class="text-success"><i class="fa fa-fw fa-envelope-open"></i></span>
							</label>
							<label class="btn btn-default{if $userdata['mailing'] == 0} active{/if}" for="flag_status_false" rel="tooltip" title="Не получать рассылку" data-placement="auto" data-container="body">
								<input type="radio" name="mailing" value="0" id="flag_status_false"{if $userdata['mailing'] == 0} checked{/if}> <span class="text-danger"><i class="fa fa-fw fa-envelope"></i></span>
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
					<input type="text" name="password" id="inputPassword" class="form-control"  pattern="^[\d\D]{literal}{5,}{/literal}">
				</div>
			</div>

			<hr />
			<h3>Анкетные данные</h3>

			<div class="form-group">
				<label for="inputUserName" class="col-lg-4 control-label">
					Имя:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_name" id="inputUserName" class="form-control"  value="{$userdata['user_name']}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserSurName" class="col-lg-4 control-label">
					Фамилия:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_surname" id="inputUserSurName" class="form-control"  value="{$userdata['user_surname']}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserLastName" class="col-lg-4 control-label">
					Отчество:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_last_name" id="inputUserLastName" class="form-control"  value="{$userdata['user_last_name']}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserBirthdate" class="col-lg-4 control-label">
					Дата рождения:
				</label>
				<div class="col-lg-8">
					<input type="text" name="user_birthdate" id="inputUserBirthdate" value="{$userdata['user_birthdaten']}" class="form-control datepicker">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserSex" class="col-lg-4 control-label">
					Пол:
				</label>
				<div class="col-lg-8">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default{if $userdata['user_sex'] == "n"} active{/if}">
							<input type="radio" name="user_sex" id="inputUserSex" autocomplete="off" value="n"{if $userdata['user_sex'] == "n"} checked{/if}><i class="fa fa-fw fa-user"></i> Не указан
						</label>
						<label class="btn btn-default{if $userdata['user_sex'] == "m"} active{/if}">
							<input type="radio" name="user_sex" id="inputUserSexM" autocomplete="off" value="m"{if $userdata['user_sex'] == "m"} checked{/if}><i class="fa fa-fw fa-male"></i> Мужской
						</label>
						<label class="btn btn-default{if $userdata['user_sex'] == "f"} active{/if}">
							<input type="radio" name="user_sex" id="inputUserSexF" autocomplete="off" value="f"{if $userdata['user_sex'] == "f"} checked{/if}><i class="fa fa-fw fa-female"></i> Женский
						</label>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="inputAvatar" class="col-lg-4 control-label">
					Аватар:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Изображение вашего профиля" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					{if $userdata['avatar'] != ""}<div class="btn-group pull-right" data-toggle="buttons"><label class="btn btn-default btn-xs bda"><input type="checkbox" name="delete_avatar" class="ida" value="1" autocomplete="on"><i class="iconda fa fa-fw fa-trash"></i> Удалить аватар?</label></div>{/if} <input type="file" name="avatar" id="inputAvatar" class="btn btn-default">
				</div>
			</div>

			<div class="form-group">
				<label for="inputUserSlogan" class="col-lg-4 control-label">
					Девиз:
				</label>
				<div class="col-lg-8">
					<textarea class="form-control" id="inputUserSlogan" name="user_slogan" rows="3">{$userdata['user_slogan_edit']}</textarea>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-md-offset-4">
					<input type="submit" name="update_info" class="btn btn-success btn-sm" value="Обновить данные">
				</div>
			</div>
		</form>
		<hr />
	</div>
</div>

<div class="row">
	<div class="col-sm-8">
		<div class="alert alert-warning" role="alert">
			<i class="fa fa-fw fa-warning"></i> После смены данных, система попросит перезайти вас на сайт, что бы удостоверится в вашей безопастности.
		</div>
	</div>
</div>

{literal}
	<script>
		$(document).ready(function(){
			$('.bda').toggle(function() {
				$(this).removeClass('btn-default').addClass('btn-danger');
				$('.iconda').removeClass('fa-trash').addClass('fa-check');
				$('.ida').attr('checked','true');
			}, function() {
				$(this).removeClass('btn-danger').addClass('btn-default');
				$('.iconda').removeClass('fa-check').addClass('fa-trash');
				$('.ida').removeAttr('checked');
			});
		});
	</script>
{/literal}
