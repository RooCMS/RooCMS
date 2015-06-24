{* Шаблон личного кабинета пользователя *}

<h1>Личный кабинет</h1>
<hr>
<div class="row">
	<div class="col-sm-8">
		<dl class="dl-horizontal">
			<dt class="text-primary">Ваш логин</dt>
			<dd>
				{$userdata['login']}
				<br /><small>(используется для входа на сайт)</small>
			</dd>

			<dt class="text-primary">Ваш псевдоним</dt>
			<dd>
				{$userdata['nickname']}
				<br /><small>(под этим именем вас знают на сайте)</small>
			</dd>

			<dt class="text-primary">Электронная почта</dt>
			<dd>
				{$userdata['email']}
				<br /><small>(на этот адрес вы получаете уведомления с сайта)</small>
			</dd>

			<dt></dt>
			<dd>
				<br /><a href="{$SCRIPT_NAME}?act=ucp&part=edit_info" class="btn btn-default btn-sm"><i class="fa fa-fw fa-pencil"></i>Редактировать</a>
			</dd>
		</dl>
	</div>
</div>

<div class="row">
	<div class="col-sm-8">
		<div class="alert alert-warning" role="alert">
			Не передавайте свои персональные данные третьим лицам!
			<br />Администрация сайта никогда не будет просить ваш пароль.
		</div>
	</div>
</div>
