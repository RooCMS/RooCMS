{literal}
<script>
$(document).ready(function() {
	$('#or').css('opacity','0.2');
	$('#loginform').mouseover(function(){
		$('#or').stop().animate({opacity: '1'});
	}).mouseout(function(){
		$('#or').stop().animate({opacity: '0.2'});
	});
	
	$('#loginform').css('right', '-310px');
	$('#loginform').animate({right: '40px'});
	
	$('#errmsg').css('left', '-500px');
	$('#errmsg').animate({left: '50px'});
	
	$('#er').css('opacity', '0');
	$('#er').animate({opacity: '1'});
});
</script>
<style>
body {overflow: hidden;}
#central {position: absolute;top: 50%;left: 0%;z-index: 2;width: 100%;}
#or {width: 100%;height: 200px;position:relative;z-index: 4;background-color: #fffff9;left: 0px;top: -100px;vertical-align: top;text-align: left;border: 0px;background: url('{/literal}{$SKIN}{literal}/img/bg_loginform_normal.png') repeat;}
#er {width: 100%;height: 200px;position:relative;z-index: 4;background-color: #fffff9;left: 0px;top: -100px;vertical-align: top;text-align: left;border: 0px;background: url('{/literal}{$SKIN}{literal}/img/bg_loginform_error.png') repeat;}
#loginform {width: 300px;height: 154px;position:absolute;z-index: 5;background-color: #fffff9;right: 34px;top: -77px;vertical-align: top;text-align: left;border: 1px solid #DA7A2E;}
#formtitle {background-color: #DA7A2E;font-size: 14px;color: #fffffb;height: 20px;padding: 4px;margin: 4px;}
#errmsg {color: red;background-color: #fffffb;padding: 2px 4px 4px 4px;position: absolute;left: 50px; top: 84px;font-size: 32px;font-family: Ubuntu;}
</style>
{/literal}
<form method="post">
	<div id="central">
		<div id="{if empty($error_login)}or{else}er{/if}" class="corner">
		{if !empty($error_login)}<center><font id="errmsg" class="corner">{$error_login}</font></center>{/if}
		</div>
		<div id="loginform" class="shadow corner">
			<div id="formtitle" class="corner">
				Вход
			</div>
			<table width="100%" border="0" cellpadding="3" cellspacing="3">
				<tr>
					<td width="20%" align="right" valign="middle">
						Логин
					</td>
					<td width="80%" align="left" valign="middle">
						<input type="text" value="" name="login" class="f_input_m" required placeholder="Login">
					</td>
				</tr>
				<tr>
					<td width="22%" align="right" valign="middle">
						Пароль
					</td>
					<td width="80%" align="left" valign="middle">
						<input type="password" value="" name="passw" class="f_input_m" required placeholder="Password">
					</td>
				</tr>
			</table>
			<input type="submit" value="Войти в панель управления" name="go" class="f_submit" style="width: 291px;margin-left: 4px;">
		</div>
	</div>
</form>
        
