{literal}
<style>
body {overflow: hidden;background: transparent url('{/literal}{$SKIN}{literal}/img/bgcp_loginlow.jpg') !important;}
#bglogin {position: absolute;z-index: 1;top: 0px;left: 0px;right: 0px;bottom: 0px;background: transparent url('{/literal}{$SKIN}{literal}/img/bgcp_loginhigh.jpg') no-repeat 50% 50%;background-size: cover;}
#LoginForm {border: 10px solid rgba(220,220,220,0.5);}
</style>
{/literal}

<div id="bglogin">
	<form method="post" class="form-horizontal" role="form">
		<div class="modal show" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
        		<div class="modal-content" id="LoginForm">
					<div class="modal-header text-center ">
						<a href="/" class="close" data-dismiss="modal" aria-hidden="true">×</a>
						<img src="{$SKIN}/img/logo_login.png" border="0" alt="Добро пожаловать в Панель Администратора RooCMS" title="Добро пожаловать в Панель Администратора RooCMS">
					</div>
					<div class="modal-body text-center">
            				{if !empty($error_login)}
		    				<div class="alert alert-danger t12 text-left in fade">
		    					<a href="#" class="close" data-dismiss="alert">&times;</a>
							<b>Внимание ошибка!</b>
							<br />{$error_login}
		    				</div>
		    			{/if}
						<div class="col-xs-12">
							<div class="form-group">
								{*<label class="col-lg-2 control-label" for="Login">Логин</label>*}
								<div class="input-group col-lg-12">
									<span class="input-group-addon"><span class="fa fa-fw fa-user" rel="tooltip" title="Введите ваш логин в это поле" data-placement="right"></span></span>
									<input class="form-control" id="Login" type="text" name="login" placeholder="Логин" required autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								{*<label class="col-lg-2 control-label" for="Password">Пароль</label>*}
								<div class="input-group col-lg-12">
									<span class="input-group-addon"><span class="fa fa-fw fa-key" rel="tooltip" title="Введите ваш пароль в это поле" data-placement="right"></span></span>
									<input class="form-control" id="Password" type="password" name="password" placeholder="Пароль" required autocomplete="off">
								</div>
							</div>
						</div>
						<span class="clearfix"></span>
					</div>
					<div class="modal-footer">
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


