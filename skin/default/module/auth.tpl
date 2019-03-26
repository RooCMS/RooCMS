{* Module template: auth *}

{if $userdata['uid'] == 0}

	{literal}
	<style>
		#LoginForm .modal-content {border: 10px solid rgba(220,220,220,0.5);}
	</style>
	{/literal}
	<div class="text-right mt-n3">
		<a class="btn btn-sm btn-primary border-top-0 rounded-0" data-toggle="collapse" href="#LoginForm" role="button" aria-expanded="false" aria-controls="LoginForm">Войти <i class="fas fa-fw fa-sign-in-alt"></i></a>
		<a href="{$SCRIPT_NAME}?part=reg" class="btn btn-sm btn-secondary border-top-0 rounded-0">Регистрация <i class="fas fa-fw fa-user-plus"></i></i></a>
	</div>

	{*<div class="row auth">
		<div class="col-sm-12 text-right">
			<button class="btn btn-sm btn-link" data-toggle="modal" data-target="#LoginForm">Войти на сайт<i class="fa fa-fw fa-user-circle-o"></i></button>
			<a href="{$SCRIPT_NAME}?part=reg" class="btn btn-sm btn-link" style="">Регистрация<i class="fa fa-fw fa-user-plus"></i></a>
		</div>
	</div>*}

	<!-- Modal Auth -->
	<div class="modal fade" id="LoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=login">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><i class="fa fa-fw fa-user-circle-o"></i> Войти на сайт</h4>
					</div>
					<div class="modal-body">
						<div class="form-group form-group-sm text-left">
							<div class="inner-addon left-addon">
								<i class="fa fa-fw fa-user-secret"></i>
								<input type="text" name="login" class="form-control non-bgreq" id="inputLogin" aria-describedby="inputLoginStatus" required="">
							</div>
						</div>
						<div class="form-group form-group-sm text-left">
							<div class="inner-addon left-addon">
								<i class="fa fa-fw fa-lock"></i>
								<input type="password" name="password" class="form-control non-bgreq" id="inputPassword" aria-describedby="inputPasswordStatus" required="">
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<a href="{$SCRIPT_NAME}?part=repass" class="btn btn-sm btn-default">Забыли пароль<i class="fa fa-fw fa-question-circle-o"></i></a>
						<button type="submit" name="userlogin" id="inputAuth" class="btn btn-sm btn-primary" value="user">Войти<i class="fa fa-fw fa-sign-in"></i></button>
					</div>
				</form>
			</div>
		</div>
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
		<div class="col-xs-8 col-xs-offset-2 text-center visible-xs">
			<h5>
				{if $userdata['avatar'] != ""}<a href="{$SCRIPT_NAME}?part=ucp&act=ucp"><img src="/upload/images/{$userdata['avatar']}" height="70" class="img-circle mauth-avatar" alt="{$userdata['nickname']}"></a>{/if}
				<a href="{$SCRIPT_NAME}?part=ucp&act=ucp">{if $userdata['user_sex'] != "n"}<i class="fa fa-fw fa-{if $userdata['user_sex'] != "m"}fe{/if}male"></i>{/if}{$userdata['nickname']}</a>
				<a href="{$SCRIPT_NAME}?part=ucp&act=pm" class="btn btn-{if $pm == 0}default{else}success{/if} btn-sm"><i class="fa fa-fw fa-envelope-o"></i></a>
				<a href="{$SCRIPT_NAME}?part=ucp&act=logout" class="btn btn-default btn-sm"><i class="fa fa-fw fa-sign-out"></i></a>
			</h5>
		</div>
	</div>
{/if}
