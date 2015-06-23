{* Шаблон для модуля: auth *}

{if $userdata['uid'] == 0}
	<div class="row">
		<div class="col-sm-12 text-right">
			<form method="post" action="?act=login" class="form-inline">
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
{else}

{/if}
