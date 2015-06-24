{* Шаблон редактирования личных данных пользователя *}

<h1>Личный кабинет</h1>
<hr>

<div class="row">
	<div class="col-sm-8">
		<form method="post" action="{$SCRIPT_NAME}?act=ucp&part=update_info" role="form" class="form-horizontal">
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
					<input type="text" name="email" id="inputEmail" class="form-control"  value="{$userdata['email']}" pattern="^\s*\w+\-*\.*\w*@\w+\.[\w+\s*]{literal}{2,}{/literal}" required>
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
			После смены данных, система попросит перезайти вас на сайт, что бы удостоверится в вашей безопастноти.
		</div>
	</div>
</div>
