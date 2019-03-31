<style>
	{literal}
	body {overflow: hidden;background: transparent url('/skin/acp/img/bgcp_loginlow.jpg') !important;}
	#bglogin {position: absolute;z-index: 1;top: 0;left: 0;right: 0;bottom: 0;background: transparent url('/skin/acp/img/bgcp_loginhigh.jpg') no-repeat 50% 50%;background-size: cover;}
	.show {display: block;}

	{/literal}{if !isset($error_login)}{literal}
	@keyframes show {
		from {opacity: 0;}
		to {opacity: 1;}
	}
	#LoginForm {
		animation-name: show;
		animation-duration: 1.25s;
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
	#LoginForm {
		animation-name: error;
		animation-duration: .5s;
		animation-timing-function: linear;
	}
	{/literal}{/if}{literal}
	.bg_login {background: transparent url('/skin/acp/img/bg_login.png') repeat 50% 50%;}
	#LoginForm {border: 10px solid rgba(220,220,220,0.5);}
	{/literal}
</style>

<div id="bglogin">
	<form method="post" role="form">
		<div class="modal show" id="RooCMSACPLogin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content" id="LoginForm">
					<div class="modal-header bg_login">
						<img src="/skin/acp/img/logo.png" border="0" alt="Добро пожаловать в Панель Администратора RooCMS" title="Добро пожаловать в Панель Администратора RooCMS">
						<a href="/" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</a>

					</div>
					<div class="modal-body text-center bg_login">
						{if isset($error_login)}
							<div class="alert alert-danger text-left show in fade">
								<a href="#" class="close" data-dismiss="alert">&times;</a>
								<b>Внимание ошибка!</b>
								<br />{$error_login}
							</div>
						{/if}
						<div class="col-12">
							<div class="form-group row">
								<label class="col-md-2 form-control-plaintext d-none d-md-block" for="Login">Логин</label>
								<div class="input-group col-md-10">
									<span class="input-group-prepend">
										<span class="input-group-text" id="fLogin">
											<i class="fas fa-fw fa-user" rel="tooltip" title="Введите ваш логин в это поле" data-placement="right"></i>
										</span>
									</span>
									<input class="form-control non-bgreq" id="Login" type="text" name="login" placeholder="Логин" required autocomplete="off" aria-describedby="fLogin">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-md-2 form-control-plaintext d-none d-md-block" for="Password">Пароль</label>
								<div class="input-group col-md-10">
									<span class="input-group-prepend">
										<span class="input-group-text" id="fPassword">
											<span class="input-group-addon">
												<i class="fas fa-fw fa-key" rel="tooltip" title="Введите ваш пароль в это поле" data-placement="right"></i>
											</span>
										</span>
									</span>
									<input class="form-control non-bgreq" id="Password" type="password" name="password" placeholder="Пароль" required autocomplete="off" aria-describedby="fPassword">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer bg_login">
						<div class="row">
							<div class="col-12">
								<small>Панель управления сайтом <span class="text-nowrap"><a href="/">{$site['title']}</a></span></small>
								<button type="submit" class="btn btn-success" name="go" value="go">Войти <i class="fas fa-fw fa-sign-in-alt"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
