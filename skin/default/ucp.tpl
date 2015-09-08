{* Шаблон личного кабинета пользователя *}

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
		<dl class="dl-horizontal">
			<dt class="text-info">Ваш логин</dt>
			<dd>
				{$userdata['login']}
				<br /><small>(используется для входа на сайт)</small>
			</dd>

			<dt class="text-info">Ваш псевдоним</dt>
			<dd>
				{$userdata['nickname']}
				<br /><small>(под этим именем вас знают на сайте)</small>
			</dd>

			<dt class="text-info">Электронная почта</dt>
			<dd>
				{$userdata['email']}
				<br /><small>(на этот адрес вы получаете уведомления с сайта)</small>
			</dd>

			<dt></dt>
			<dd>
				<br /><a href="{$SCRIPT_NAME}?act=ucp&part=edit_info" class="btn btn-default btn-sm"><i class="fa fa-fw fa-pencil"></i> Редактировать</a>
			</dd>
		</dl>

	</div>
</div>
<div class="row">
	<div class="col-sm-8">
		<div class="alert alert-warning" role="alert">
			<i class="fa fa-fw fa-info"></i> Не передавайте свои персональные данные третьим лицам!
			<br /><i class="fa fa-fw fa-info"></i> Администрация сайта никогда не будет просить ваш пароль.
		</div>
	</div>
</div>
