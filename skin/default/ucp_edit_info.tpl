{* Шаблон редактирования личных данных пользователя *}

<h1>Личный кабинет</h1>
<hr>

<div class="row">

	{if $userdata['avatar'] != ""}
	<div class="col-xs-3 col-sm-2 col-md-2"><img src="/upload/images/{$userdata['avatar']}"  class="img-thumbnail"></div>
	<div class="col-xs-9 col-sm-10 col-md-10">
		{else}
		<div class="col-xs-12">
			{/if}
			<h1>
				{$userdata['nickname']}
			</h1>
			<small class="label label-primary">{$userdata['gtitle']}</small>
		</div>


</div>
<hr>

<div class="row">
	<div class="col-sm-8">
		<form method="post" action="{$SCRIPT_NAME}?act=ucp&part=update_info" role="form" class="form-horizontal" enctype="multipart/form-data">
			<div class="form-group">
				<label for="inputLogin" class="col-lg-4 control-label">
					Ваш логин: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Не должен совпадать с логином другого пользователя" data-placement="right"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="login" id="inputLogin" class="form-control" value="{$userdata['login']}" required>
				</div>
			</div>

			<div class="form-group">
				<label for="inputNickname" class="col-lg-4 control-label">
					Ваш псевдоним:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Не должен совпадать с псевдонимом другого пользователя" data-placement="right"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="nickname" id="inputNickname" class="form-control" value="{$userdata['nickname']}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputEmail" class="col-lg-4 control-label">
					Электронная почта:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="right"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="email" id="inputEmail" class="form-control"  value="{$userdata['email']}" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
				</div>
			</div>

			<div class="form-group">
				<label for="inputPassword" class="col-lg-4 control-label">
					Пароль:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Оставьте поле пустым, если не хотите менять пароль." data-placement="right"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="password" id="inputPassword" class="form-control"  pattern="^[\d\D]{literal}{5,}{/literal}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputAvatar" class="col-lg-4 control-label">
					Аватар:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Изображение вашего профиля" data-placement="right"></span></small>
				</label>
				<div class="col-lg-8">
					{if $userdata['avatar'] != ""}<div class="btn-group pull-right" data-toggle="buttons"><label class="btn btn-default btn-xs bda"><input type="checkbox" name="delete_avatar" class="ida" value="1" autocomplete="on"><i class="iconda fa fa-fw fa-trash"></i> Удалить аватар?</label></div>{/if} <input type="file" name="avatar" id="inputAvatar" class="btn btn-default">
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-md-offset-4">
					<input type="hidden" name="empty" value="1">
					<input type="submit" name="update_info" class="btn btn-default btn-sm" value="Обновить данные">
				</div>
			</div>
		</form>
	</div>
</div>

<br />

<div class="row">
	<div class="col-sm-8">
		<div class="alert alert-warning" role="alert">
			<i class="fa fa-fw fa-info"></i> После смены данных, система попросит перезайти вас на сайт, что бы удостоверится в вашей безопастности.
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
