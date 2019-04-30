{* Module template: auth *}
<div class="text-center text-sm-right mt-3 mt-sm-n3">
	{if $userdata['uid'] == 0}
		<a class="btn btn-sm btn-primary rounded-0" data-toggle="collapse" href="#LoginForm" role="button" aria-expanded="false" aria-controls="LoginForm">Войти <i class="fas fa-fw fa-sign-in-alt"></i></a>
		<a href="{$SCRIPT_NAME}?part=reg" class="btn btn-sm btn-secondary rounded-0">Регистрация <i class="fas fa-fw fa-user-plus"></i></i></a>
	{else}
		Здравствуйте, <a href="{$SCRIPT_NAME}?part=ucp&act=ucp" class="mr-2 dropdown-toggle" id="UserMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{if $userdata['avatar'] != ""}<img src="/upload/images/{$userdata['avatar']}" height="22" class="border border-primary rounded-circle mr-1" alt="{$userdata['nickname']}">{else}{if $userdata['user_sex'] != "n"}<i class="fa fa-fw fa-{if $userdata['user_sex'] != "m"}fe{/if}male"></i>{/if}{/if}{$userdata['nickname']}{if $pm != 0}<i class="fas fa-fw fa-envelope blink"></i>{/if}</a>

		<div class="dropdown-menu dropdown-menu-right" aria-labelledby="UserMenu">
			<h6 class="dropdown-header">Личные сообщения</h6>
			<a class="dropdown-item" href="{$SCRIPT_NAME}?part=ucp&act=ucp"><i class="far fa-fw fa-user"></i> Личный кабинет</a>
			<a class="dropdown-item" href="{$SCRIPT_NAME}?part=ucp&act=pm"><i class="far fa-fw fa-envelope{if $pm == 0}-open{/if}"></i> Новых: {$pm}</a>
			<div class="dropdown-divider"></div>
			{if $userdata['title'] == "a"}
				<a class="dropdown-item" href="{$config->cp_script}"><i class="fas fa-fw fa-skull-crossbones"></i> Admin CP</a>
				<div class="dropdown-divider"></div>
			{/if}
			<a class="dropdown-item" href="{$SCRIPT_NAME}?part=ucp&act=logout"><i class="fas fa-fw fa-sign-out-alt"></i> Выйти</a>
		</div>
	{/if}
</div>
