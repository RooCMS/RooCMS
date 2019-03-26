{* Module template: auth *}

{if $userdata['uid'] == 0}
	<div class="text-right mt-n3">
		<a class="btn btn-sm btn-primary border-top-0 rounded-0" data-toggle="collapse" href="#LoginForm" role="button" aria-expanded="false" aria-controls="LoginForm">Войти <i class="fas fa-fw fa-sign-in-alt"></i></a>
		<a href="{$SCRIPT_NAME}?part=reg" class="btn btn-sm btn-secondary border-top-0 rounded-0">Регистрация <i class="fas fa-fw fa-user-plus"></i></i></a>
	</div>
{else}
	<div class="row auth">
		<div class="col-sm-12 text-right hidden-xs">
			{if $userdata['avatar'] != ""}<a href="{$SCRIPT_NAME}?part=ucp&act=ucp"><img src="/upload/images/{$userdata['avatar']}" height="70" class="img-circle mauth-avatar" alt="{$userdata['nickname']}"></a>{/if}
			<div class="pull-right">
				<h4>Здравствуйте, <a href="{$SCRIPT_NAME}?part=ucp&act=ucp">{if $userdata['user_sex'] != "n"}<i class="fa fa-fw fa-{if $userdata['user_sex'] != "m"}fe{/if}male"></i>{/if}{$userdata['nickname']}</a></h4>

				<a href="{$SCRIPT_NAME}?part=ucp&act=pm" class="btn btn-{if $pm == 0}default{else}success{/if} btn-sm"><i class="fa fa-fw fa-envelope-o"></i> У вас {if $pm == 0}нет{else}{$pm}{/if} новых сообщений</a>
				<a href="{$SCRIPT_NAME}?part=ucp&act=logout" class="btn btn-default btn-sm">Выйти <i class="fa fa-fw fa-sign-out"></i></a>
			</div>
		</div>
	</div>
{/if}
