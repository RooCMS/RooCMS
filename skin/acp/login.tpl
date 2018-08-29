{literal}
<style>
	body {overflow: hidden;background: transparent url('{/literal}{$SKIN}{literal}/img/bgcp_loginlow.jpg') !important;}
	#bglogin {position: absolute;z-index: 1;top: 0;left: 0;right: 0;bottom: 0;background: transparent url('{/literal}{$SKIN}{literal}/img/bgcp_loginhigh.jpg') no-repeat 50% 50%;background-size: cover;}

	{/literal}{if !isset($error_login)}{literal}
	@keyframes show {
		from {opacity: 0;}
		to {opacity: 1;}
	}
	.login {
		animation-name: show;
		animation-duration: 1s;
		animation-timing-function: cubic-bezier(0, 0, 0.18, 0.99);
	}
	{/literal}{else}{literal}
	@keyframes error {
		from, to {left: 0;}
		20% {left: 25px; }
		40% {left: -25px;}
		60% {left: 25px;}
		80% {left: -25px;}
	}
	.login {
		animation-name: error;
		animation-duration: .5s;
		animation-timing-function: linear;
	}
	{/literal}{/if}{literal}
	.bg_login {background: transparent url('{/literal}{$SKIN}{literal}/img/bg_login.png') repeat 50% 50%;}
	#LoginForm {border: 10px solid rgba(220,220,220,0.5);}
</style>
{/literal}

<div id="bglogin">
	<form method="post" class="form-horizontal" role="form">
		<div class="modal show" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-login">
        		<div class="modal-content login" id="LoginForm">
					<div class="modal-header text-center bg_login">
						<a href="/" class="close" data-dismiss="modal" aria-hidden="true">×</a>
						<img src="{$SKIN}/img/logo.png" border="0" alt="Добро пожаловать в Панель Администратора RooCMS" title="Добро пожаловать в Панель Администратора RooCMS">
					</div>
					<div class="modal-body text-center bg_login">
            				{if isset($error_login)}
		    				<div class="alert alert-danger t12 text-left in fade">
		    					<a href="#" class="close" data-dismiss="alert">&times;</a>
							<b>Внимание ошибка!</b>
							<br />{$error_login}
		    				</div>
		    			{/if}
						<div class="col-xs-12">
							<div class="form-group">
								<label class="col-md-2 control-label hidden-xs hidden-sm" for="Login">Логин</label>
								<div class="input-group col-md-10">
									<span class="input-group-addon"><i class="fa fa-fw fa-user" rel="tooltip" title="Введите ваш логин в это поле" data-placement="right"></i></span>
									<input class="form-control non-bgreq" id="Login" type="text" name="login" placeholder="Логин" required autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label hidden-xs hidden-sm" for="Password">Пароль</label>
								<div class="input-group col-md-10">
									<span class="input-group-addon"><i class="fa fa-fw fa-key" rel="tooltip" title="Введите ваш пароль в это поле" data-placement="right"></i></span>
									<input class="form-control non-bgreq" id="Password" type="password" name="password" placeholder="Пароль" required autocomplete="off">
								</div>
							</div>
						</div>
						<span class="clearfix"></span>
					</div>
					<div class="modal-footer bg_login">
    					<p class="text-left">
						<small>Панель управления сайтом <nobr><a href="/">{$site['title']}</a></nobr></small>
						<button type="submit" class="btn btn-success pull-right" name="go" value="go">Войти <i class="fa fa-fw fa-sign-in"></i></button>
					</p>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>


