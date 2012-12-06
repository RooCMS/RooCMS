<?php

class tpl_items_acp_login {

//#####################################################
//#		Основной шаблон
//#####################################################

function tpl_page() {
$HTML = <<<HTML

<div id="cont">
  <div class="box lock"> </div>
  <div id="loginform" class="box form">
    <h3>Панель управления RooCMS <a href="" class="closef">Close it</a></h2>
    <div class="formcont">
      <fieldset id="signin_menu">
      <span class="message">Введите свой логин и пароль</span>
      <form method="post">
        <label for="username">Логин</label>
        <input id="username" name="login" value=""  class="required" tabindex="4" type="text">
        </p>
        <p>
          <label for="password">Пароль</label>
          <input id="password" name="passw" value=""  class="required" tabindex="5" type="password">
        </p>
        <p class="clear"></p>
        {html:error_login}
        <p class="remember">
          <input id="signin_submit" value="Войти" name="go" tabindex="6" type="submit">
          <input id="cancel_submit" value="Сброс" tabindex="7" type="button">
        </p>
      </form>
      </fieldset>
    </div>
    <div class="formfooter"></div>
  </div>
</div>

<div id="bg">
  <div>
    <table cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="img/acp/bg_login.jpg" alt=""/> </td>
      </tr>
    </table>
  </div>
</div>


HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS
* { margin: 0px; padding: 0px; }
a.closef { background-image: url(img/acp/close.png); background-position: center center; background-repeat: no-repeat; height: 25px; outline: none; position: absolute; right: 10px; text-indent: -99999px; top: 10px; width: 25px; }
body { font-family: Geneva, Arial, Helvetica, sans-serif; line-height: 15px; }
h3 { background-image: url(img/acp/header.png); background-position: center top; background-repeat: repeat; color: #333333; font-size: 15px; font-weight: bold; height: 43px; line-height: 43px; margin: 0px; padding-bottom: 0px; padding-left: 20px; padding-right: 0px; padding-top: 0px; position: relative; text-shadow: 0px 1px 1px #FFF; }
html, #bg, #bg table, #bg td, #cont { height: 100%; overflow: hidden; width: 100%; }
.box { left: 50%; position: absolute; top: 50%; }
.clear { clear: both; }
.form { display: none; left: 50%; margin-bottom: 0; /* Half the width of the DIV tag which is 388 pixels */ margin-left: -194px; margin-right: auto; /* Half the height of the DIV tag which is also 216 pixels */ margin-top: -108px; padding: 0px; position: absolute; top: 50%; width: 387px; }
.formcont { margin: 0px; overflow: hidden; position: relative; }
.formfooter { background-image: url(img/acp/footer.png); background-position: center top; background-repeat: no-repeat; height: 13px; margin: 0px; padding: 0px; }
.lock { background-image: url(img/acp/lock.png); background-position: center center; background-repeat: no-repeat; height: 198px; left: 50%; margin-left: -99px; /* Half the width of the DIV tag which is 50 pixels */ margin-top: -99px; padding: 0px; position: absolute; top: 50%; width: 197px; }
.message { display: block; font-size: 12px; margin-bottom: 10px; margin-top: 15px; }
.remember input { *filter: Shadow(Color=#FFFFFF, 	
			Direction=180, 
			Strength=1); background: #39d url('img/acp/bg-btn.png') repeat-x scroll 0 0; border: 1px solid #B5B5B5; color: #222; font-size: 11px; font-weight: bold; margin: 0 0px 0 3px; -moz-border-radius: 4px; padding: 4px 10px 5px; text-shadow: 0px 1px 1px #FFF; -webkit-border-radius: 4px; }
.show { display: inline; }
#bg div { height: 200%; left: -50%; position: absolute; top: -50%; width: 200%; }
#bg img { margin: 0 auto; min-height: 50%; min-width: 50%; }
#bg td { text-align: center; vertical-align: middle; }
#cont { left: 0; overflow: auto; position: absolute; top: 0; z-index: 70; }
#focus-stealer { left: -9999px; position: absolute; }
#signin_menu { background-image: url(img/acp/form-bg.png); background-position: center bottom; background-repeat: repeat-y; border: none; color: #333; font-size: 12px; height: 160px; margin: 0px; overflow: hidden; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px; text-align: left; }
#signin_menu a { color: #6AC; }
#signin_menu a.forgot { background-image: url(img/acp/reset.png); background-position: left center; background-repeat: no-repeat; color: #666666; display: inline; float: left; font-weight: bold; line-height: 24px; margin-top: 10px; padding-left: 18px; text-decoration: none; }
#signin_menu input[type=text], #signin_menu input[type=password] { *margin: 0px; border: 1px solid #C6C6C6; float: right; font-size: 13px; margin: 5px 0px 5px; -moz-border-radius: 4px; padding: 5px; -webkit-border-radius: 4px; width: 180px; }
#signin_menu label { float: left; font-weight: normal; margin: 10px 0px 0px; }
#signin_menu p { *margin: 5px 0 5px; clear: both; margin: 10px 0 10px; }
#signin_menu p.remember { clear: none; float: right; padding: 0; }
#signin_submit:hover, #signin_submit:focus { background-position: 0 -5px; cursor: pointer; }
#signin_submit::-moz-focus-inner { border: 0; padding: 0; }
CSS;
return $CSS;
}

//*****************************************************
// JS
function tpl_js() {
$JS = <<<JS
<script type="text/javascript">
	$(document).ready(function() {
	
		$(document).mouseup(function() {
			$("#loginform").mouseup(function() {
				return false
			});
			
			$("a.close").click(function(e){
				e.preventDefault();
				$("#loginform").hide();
				$(".lock").fadeIn();
			});
			
			if ($("#loginform").is(":hidden"))
			{
				$(".lock").fadeOut();
			} else {
				$(".lock").fadeIn();
			}				
			$("#loginform").toggle();
		});
		
		$("form#signin").submit(function() {
		  return false;
		});
		
		$("input#cancel_submit").click(function(e) {
				$("#loginform").hide();
				$(".lock").fadeIn();
		});			
		
	});
</script>
JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################

//***************************
//	Отображение ошибок авторизации
//	{html:error_login}
function error_login($msg="",$style="text") {
$HTML = <<<HTML

		<font class="{$style}">{$msg}</font>

HTML;
return $HTML;
}

// end class
}
?>