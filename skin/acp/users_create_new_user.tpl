{* Шаблон создания нового пользователя *}
<div class="panel-heading">
	Новый пользователь
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=users&part=create" role="form" class="form-horizontal">

		<div class="form-group">
			<label for="inputLogin" class="col-lg-3 control-label">
				Логин пользователя: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Должен быть уникальным" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="login" id="inputLogin" class="form-control" required>
			</div>
		</div>

		<div class="form-group">
			<label for="inputNickname" class="col-lg-3 control-label">
				Имя пользователя:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Должно быть уникальным" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="nickname" id="inputNickname" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="inputEmail" class="col-lg-3 control-label">
				Электронная почта:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Должна быть уникальной. Нельзя заводить несколько аккаунтов на один почтовый ящик" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="email" id="inputEmail" class="form-control" pattern="^\s*\w+\-*\.*\w*@\w+\.[\w+\s*]{literal}{2,}{/literal}" required>
			</div>
		</div>

		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Титул:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Администраторы могут получить доступ к Панели Управления" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<select name="title"  id="inputTitle" class="selectpicker show-tick" data-size="auto" data-width="50%">
					<option value="a">Администратор</option>
					<option value="u" selected>Пользователь</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label for="inputPassword" class="col-lg-3 control-label">
				Пароль:  <small><span class="fa fa-info fa-fw" rel="tooltip" title="Оставьте поле пустым, и RooCMS сама создаст пароль. Мин: 5 символов" data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="password" id="inputPassword" class="form-control" pattern="^[\d\D]{literal}{5,}{/literal}">
			</div>
		</div>


		<div class="row">
			<div class="col-lg-9 col-md-offset-3">
				<input type="hidden" name="empty" value="1">
				<input type="submit" name="create_user" class="btn btn-success" value="Создать">
				<input type="submit" name="create_user_ae" class="btn btn-default" value="Создать и выйти">
			</div>
		</div>

	</form>
</div>