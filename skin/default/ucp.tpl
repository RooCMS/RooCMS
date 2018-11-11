{* Шаблон личного кабинета пользователя *}
<div class="row">
	<div class="col-sm-12">
		<h1>Личный кабинет</h1>
		<hr>
	</div>
</div>
<div class="row">
	{if $userdata['avatar'] != ""}<div class="col-xs-3 col-sm-2 col-md-2 text-center"><img src="/upload/images/{$userdata['avatar']}"  class="img-thumbnail"></div>{/if}
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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Персональные данные</h3>
			</div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt class="text-info">Ваш логин</dt>
					<dd>
						{$userdata['login']}
						<br /><small>используется для входа на сайт</small>
					</dd>

					<dt class="text-info">Ваш псевдоним</dt>
					<dd>
						{$userdata['nickname']}
						<br /><small>под этим именем вас знают на сайте</small>
					</dd>

					<dt class="text-info">Электронная почта</dt>
					<dd>
						{$userdata['email']} <span class="label {if $userdata['mailing'] == 0}label-default">Рыссылка отключена{else}label-success">Рассылка подключена{/if}</span>
						<br /><small>на этот адрес вы получаете уведомления с сайта</small>
					</dd>
				</dl>
				<div class="alert alert-warning small mb0" role="alert">
					<i class="fa fa-fw fa-warning"></i> Не передавайте свои персональные данные третьим лицам!
					<br /><i class="fa fa-fw fa-warning"></i> Администрация сайта никогда не будет просить ваш пароль.
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Анкетные данные</h3>
			</div>
			<div class="panel-body">
				<dl class="dl-horizontal mb0">
					<dt class="text-info">Имя</dt>
					<dd>{$userdata['user_name']}</dd>

					<dt class="text-info">Фамилия</dt>
					<dd>{$userdata['user_surname']}</dd>

					<dt class="text-info">Отчество</dt>
					<dd>{$userdata['user_last_name']}</dd>

					<dt class="text-info">Дата Рождения</dt>
					<dd>{$userdata['user_birthdate']}</dd>

					<dt class="text-info">Пол</dt>
					<dd>
						{if $userdata['user_sex'] == "m"}
							<i class="fa fa-fw fa-mars text-info"></i> Мужчина
						{elseif $userdata['user_sex'] == "f"}
							<i class="fa fa-fw fa-venus text-danger"></i> Женщина
						{elseif $userdata['user_sex'] == "n"}
							<i class="fa fa-fw fa-genderless"></i> Не указан
						{/if}
					</dd>

					<dt class="text-info">Девиз</dt>
					<dd>{$userdata['user_slogan']}</dd>
				</dl>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-footer text-center">
				<a href="{$SCRIPT_NAME}?part=ucp&act=ucp&move=edit_info" class="btn btn-default"><i class="fa fa-fw fa-pencil"></i> Редактировать</a>
			</div>
		</div>


	</div>
</div>