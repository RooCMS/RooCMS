{* Template: user control panel *}
<div class="container mb-4">
	<div class="row">
		<div class="col-12">
			<h1>Личный кабинет</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card">
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
						{$userdata['email']} <span class="badge {if $userdata['mailing'] == 0}badge-light">Рыссылка отключена{else}label-success">Рассылка подключена{/if}</span>
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