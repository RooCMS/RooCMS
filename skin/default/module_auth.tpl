{* Шаблон для модуля: auth *}

{if $userdata['uid'] == 0}
	<div class="row">
		<div class="col-sm-12 text-right">
			<form method="post" action="?part=ucp&act=login" class="form-inline">
				<div class="form-group form-group-sm text-left" style="margin-top: 10px;">
					<label class="control-label text-primary" for="inputLogin" rel="tooltip" title="Логин" data-placement="right" data-container="body"><i class="fa fa-fw fa-user-secret"></i></label>
					<br />
					<input type="text" name="login" class="form-control" id="inputLogin" aria-describedby="inputLoginStatus" required="">

				</div>
				<div class="form-group form-group-sm text-left" style="margin-top: 10px;">
					<label class="control-label text-primary" for="inputPassword"  rel="tooltip" title="Пароль" data-placement="right" data-container="body"><i class="fa fa-fw fa-lock"></i></label>
					<br />
					<input type="password" name="password" class="form-control" id="inputPassword" aria-describedby="inputPasswordStatus" required="">
				</div>
				<div class="form-group form-group-sm text-left" style="margin-top: 10px;">
					<label class="control-label text-primary" for="inputAuth">&nbsp;</label>
					<br />
					<button type="submit" name="userlogin" id="inputAuth" class="btn btn-default btn-sm"  rel="tooltip" title="Войти на сайт" data-placement="top" data-container="body" value="user"><i class="text-primary fa fa-fw fa-sign-in"></i></button>
				</div>
			</form>
		</div>
	</div>

	{*
	<div class="row">
		<form method="post" action="?act=login" class="form-inline">
			<div class="col-md-8 input-group input-group-sm text-left" style="margin-top: 10px;">
				<input type="text" name="login" class="form-control mod_auth_form" id="inputLogin" aria-describedby="inputLoginStatus" required="">
				<input type="password" name="password" class="form-control mod_auth_form" id="inputPassword" aria-describedby="inputPasswordStatus" required="">
			</div>
			<div class="col-md-4">
				<button type="submit" name="userlogin" id="inputAuth" class="btn btn-default btn-sm"  rel="tooltip" title="Войти на сайт" data-placement="top" data-container="body" value="user"><i class="text-primary fa fa-fw fa-sign-in"></i></button>
			</div>
		</form>
	</div>
	*}
{else}
	<div class="row">
		<div class="col-sm-12 text-right">
			{if $userdata['avatar'] != ""}<a href="/?part=ucp&act=ucp"><img src="/upload/images/{$userdata['avatar']}" height="70" class="img-circle mauth-avatar"></a>{/if}
			<div class="pull-right">
				<h4>Здравствуйте, <a href="/?part=ucp&act=ucp"><i class="fa fa-fw fa-{if $userdata['user_sex'] != "m"}fe{/if}male"></i>{$userdata['nickname']}</a></h4>

				<a href="/index.php?part=ucp&act=pm" class="btn btn-{if $pm == 0}default{else}success{/if} btn-xs"><i class="fa fa-fw fa-envelope-o"></i> У вас {if $pm == 0}нет{else}{$pm}{/if} новых сообщений</a>
				<a href="/index.php?part=ucp&act=logout" class="btn btn-default btn-xs">Выйти <i class="fa fa-fw fa-sign-out"></i></a>
			</div>
		</div>
	</div>
{/if}
