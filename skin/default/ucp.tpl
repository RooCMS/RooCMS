{* Template: user control panel *}
<div class="container mb-4">
	<div class="row">
		<div class="col-12">
			<h1>Личный кабинет</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card mb-3">
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
	<div class="row">
		<div class="col-xxl-6 col-xl-7 col-lg-8">
			<div class="card card-body">
				<h4 class="card-title pb-2 border-bottom">Персональные данные</h4>
				<dl class="row">
					<dt class="col-md-5">Ваш логин</dt>
					<dd class="col-md-7">
						{$userdata['login']}
						<br /><small>используется для входа на сайт</small>
					</dd>

					<dt class="col-md-5">Ваш псевдоним</dt>
					<dd class="col-md-7">
						{$userdata['nickname']}
						<br /><small>под этим именем вас знают на сайте</small>
					</dd>

					<dt class="col-md-5">Электронная почта</dt>
					<dd class="col-md-7">
						{$userdata['email']} <span class="badge {if $userdata['mailing'] == 0}badge-warning">Рыссылка отключена{else}badge-success">Рассылка подключена{/if}</span>
						<br /><small>на этот адрес вы получаете уведомления с сайта</small>
					</dd>
				</dl>
				<div class="alert alert-warning small mb0" role="alert">
					<i class="fas fa-fw fa-exclamation-triangle mr-3 text-danger"></i> Не передавайте свои персональные данные третьим лицам!
					<br /><i class="fas fa-fw fa-exclamation-triangle mr-3 text-danger"></i> Администрация сайта никогда не будет просить ваш пароль.
				</div>
				<h4 class="card-title pb-2 border-bottom">Анкетные данные</h4>
				<dl class="row">
					<dt class="col-md-5">Имя</dt>
					<dd class="col-md-7">
						{if $userdata['user_name'] != ""}
							{$userdata['user_name']}
						{else}
							<span class="text-muted">-</span>
						{/if}
					</dd>

					<dt class="col-md-5">Фамилия</dt>
					<dd class="col-md-7">
						{if $userdata['user_surname'] != ""}
							{$userdata['user_surname']}
						{else}
							<span class="text-muted">-</span>
						{/if}
					</dd>

					<dt class="col-md-5">Отчество</dt>
					<dd class="col-md-7">
						{if $userdata['user_last_name'] != ""}
							{$userdata['user_last_name']}
						{else}
							<span class="text-muted">-</span>
						{/if}
					</dd>

					<dt class="col-md-5">Дата Рождения</dt>
					<dd class="col-md-7">
						{if $userdata['user_birthdate'] != ""}
							{$userdata['user_birthdate']}
						{else}
							<span class="text-muted">-</span>
						{/if}
					</dd>

					<dt class="col-md-5">Пол</dt>
					<dd class="col-md-7">
						{if $userdata['user_sex'] == "m"}
							<i class="fas fa-fw fa-mars text-info"></i> Мужчина
						{elseif $userdata['user_sex'] == "f"}
							<i class="fas fa-fw fa-venus text-danger"></i> Женщина
						{elseif $userdata['user_sex'] == "n"}
							<i class="fas fa-fw fa-genderless"></i> Не указан
						{/if}
					</dd>

					<dt class="col-md-5">Девиз</dt>
					<dd class="col-md-7">
						{if $userdata['user_slogan'] != ""}
							{$userdata['user_slogan']}
						{else}
							<span class="text-muted">-</span>
						{/if}
					</dd>
				</dl>
				<a href="{$SCRIPT_NAME}?part=ucp&act=ucp&move=edit_info" class="btn btn-light"><i class="fas fa-fw fa-edit"></i> Редактировать</a>
			</div>
		</div>
	</div>
</div>