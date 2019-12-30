{* Template: edit personal data *}
<script type="text/javascript" src="/plugin/jquery.roocms.crui.min.js"></script>
<script type="text/javascript" src="/plugin/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<div class="container mb-4">
	<div class="row">
		<div class="col-12">
			<h1>Личный кабинет</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card mb-4">
				<div class="card-body text-center text-md-left">
					{if $userdata['avatar'] != ""}
						<img src="/upload/images/{$userdata['avatar']}" class="rounded-circle border" alt="{$userdata['nickname']}">
					{/if}
					<span class="display-4 p-3 align-middle text-nowrap">{$userdata['nickname']} {*{if $userdata['user_sex'] == "m"}<i class="fas fa-fw fa-mars text-info"></i>{elseif $userdata['user_sex'] == "f"}<i class="fas fa-fw fa-venus text-danger"></i>{/if}*}</span>
					{if $userdata['user_slogan'] != ""}
						<div class="float-md-right text-md-right text-gray ubuntu">
							<span class="badge badge-extra mb-1">{$userdata['gtitle']}</span>
							<br />{$userdata['user_slogan']}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
	<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=ucp&move=update_info" role="form" enctype="multipart/form-data">
		<div class="row">
			<div class="col-lg-6">
				<div class="card card-body h-100">
					<h4 class="card-title pb-2 border-bottom">Аккаунт</h4>

					<div class="form-group">
						<label for="inputLogin">
							Ваш логин: <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с логином другого пользователя" data-placement="top"></i></small>
						</label>

						<input type="text" name="login" id="inputLogin" class="form-control" value="{$userdata['login']}" required>
					</div>

					<div class="form-group">
						<label for="inputNickname">
							Ваш псевдоним:  <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Не должен совпадать с псевдонимом другого пользователя" data-placement="top"></i></small>
						</label>

						<input type="text" name="nickname" id="inputNickname" class="form-control" value="{$userdata['nickname']}">
					</div>

					<div class="form-group">
						<label for="inputEmail">
							Электронная почта:  <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="top"></i></small>
						</label>

						<input type="text" name="email" id="inputEmail" class="form-control"  value="{$userdata['email']}" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
					</div>

					<div class="form-group">
						<label for="inputEmailL">
							Почтовая рассылка:  <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Желаете получать рассылку от администрации сайта?" data-placement="top"></i></small>
						</label>
						<div class="btn-group btn-block btn-group-toggle roocms-crui" data-toggle="buttons">
							<label class="btn btn-light{if $userdata['mailing'] == 1} active{/if}" for="flag_status_true">
								<input type="radio" name="mailing" value="1" id="flag_status_true"{if $userdata['mailing'] == 1} checked{/if}> <i class="far fa-fw fa-{if $userdata['mailing'] == 1}check-{/if}circle"></i> <i class="fas fa-fw fa-envelope-open text-success"></i> Получать уведомления
							</label>
							<label class="btn btn-light{if $userdata['mailing'] == 0} active{/if}" for="flag_status_false">
								<input type="radio" name="mailing" value="0" id="flag_status_false"{if $userdata['mailing'] == 0} checked{/if}> <i class="far fa-fw fa-{if $userdata['mailing'] == 0}check-{/if}circle"></i> <i class="fas fa-fw fa-envelope text-danger"></i> Не получать уведомления
							</label>
						</div>
					</div>

					<div class="form-group">
						<label for="inputPassword">
							Пароль:  <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Оставьте поле пустым, если не хотите менять пароль." data-placement="top"></i></small>
						</label>

						<input type="text" name="password" id="inputPassword" class="form-control" minlength="5" pattern="^[\d\D]{literal}{5,}{/literal}">
					</div>

					<div class="alert alert-warning border-warning border-2 text-center d-flex align-items-center h-100 mb-0" role="alert">
						<span class="my-auto">
							<i class="fas fa-fw fa-lg mb-2 fa-exclamation-triangle"></i>
							<br />После смены данных аккаунта, сайт попросить Вас повторно авторизоваться.
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

						<input type="text" name="user_name" id="inputUserName" class="form-control" value="{$userdata['user_name']}">
					</div>

					<div class="form-group">
						<label for="inputUserSurName">
							Фамилия:
						</label>

						<input type="text" name="user_surname" id="inputUserSurName" class="form-control" value="{$userdata['user_surname']}">
					</div>

					<div class="form-group">
						<label for="inputUserLastName">
							Отчество:
						</label>

						<input type="text" name="user_last_name" id="inputUserLastName" class="form-control" value="{$userdata['user_last_name']}">
					</div>

					<div class="form-group">
						<label for="inputUserBirthdate">
							Дата рождения:
						</label>

						<input type="text" name="user_birthdate" id="inputUserBirthdate" value="{$userdata['user_birthdaten']}" class="form-control datepicker" data-date-end-date="-1y">
					</div>

					<div class="form-group">
						<label for="inputUserSex">
							Пол:
						</label>

						<div class="btn-group btn-block btn-group-toggle roocms-crui" data-toggle="buttons">
							<label class="btn btn-light{if $userdata['user_sex'] == "n"} active{/if}">
								<input type="radio" name="user_sex" id="inputUserSex" autocomplete="off" value="n"{if $userdata['user_sex'] == "n"} checked{/if}><i class="far fa-fw fa{if $userdata['user_sex'] == "n"}-check{/if}-circle"></i><i class="fas fa-fw fa-user"></i> Не указан
							</label>
							<label class="btn btn-light{if $userdata['user_sex'] == "m"} active{/if}">
								<input type="radio" name="user_sex" id="inputUserSexM" autocomplete="off" value="m"{if $userdata['user_sex'] == "m"} checked{/if}><i class="far fa-fw fa{if $userdata['user_sex'] == "m"}-check{/if}-circle"></i><i class="fas fa-fw fa-male"></i> Мужской
							</label>
							<label class="btn btn-light{if $userdata['user_sex'] == "f"} active{/if}">
								<input type="radio" name="user_sex" id="inputUserSexF" autocomplete="off" value="f"{if $userdata['user_sex'] == "f"} checked{/if}><i class="far fa-fw fa{if $userdata['user_sex'] == "f"}-check{/if}-circle"></i><i class="fas fa-fw fa-female"></i> Женский
							</label>
						</div>
					</div>

					<div class="form-group">
						<label for="inputAvatar">
							Аватар:  <small><span class="fas fa-question-circle fa-fw" rel="tooltip" title="Изображение вашего профиля" data-placement="top"></span></small>
						</label>

						{if $userdata['avatar'] != ""}
							<br /><span class="text-success">У вас есть <a href="/upload/images/{$userdata['avatar']}" class="text-success font-weight-bold" data-fancybox="avatar" data-animation-duration="300" data-caption="Ваш аватар">аватар</a>!</span>
							Желаете удалить?
							<div class="d-md-inline ml-1 custom-control custom-switch">
								<input type="checkbox" name="delete_avatar" value="1" class="custom-control-input" id="DeleteAvatar" autocomplete="on">
								<label class="custom-control-label text-danger" for="DeleteAvatar">Да. Удалить!</label>
							</div>
						{/if}

						<div class="custom-file {if $userdata['avatar'] != ""}mt-2{/if}" id="upload-avatar">
							<input type="file" name="avatar" class="custom-file-input" id="inputAvatar" accept="image/*" aria-describedby="inputAvatarHelp">
							<label class="custom-file-label" for="inputAvatar" data-browse="Выбрать{if $userdata['avatar'] != ""} новый {/if} аватар">Выберите изображение</label>
							{if $userdata['avatar'] != ""}
								<small id="inputAvatarHelp" class="form-text text-gray">
									Если вы загрузите новый аватар, Ваш предыдущий будет автоматически заменен!
								</small>
							{/if}
						</div>
					</div>

					<div class="form-group mb-0">
						<label for="inputUserSlogan">
							Девиз:
						</label>

						<textarea class="form-control" id="inputUserSlogan" name="user_slogan" rows="3">{$userdata['user_slogan_edit']}</textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<div class="card mt-4">
					<div class="card-body text-center">
						<input type="submit" name="update_info" class="btn btn-lg btn-success btn-block" value="Обновить данные">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>


<script>
	{literal}
		$(document).ready(function(){
			$("#DeleteAvatar").on('click', function () {
				if($(this).is(":checked")) {
					$("#upload-avatar").hide();
				}
				else {
					$("#upload-avatar").show();
				}
			});

			/* BS Custom file input */
			bsCustomFileInput.init();
		});
	{/literal}
</script>
