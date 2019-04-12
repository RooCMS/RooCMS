{* Уведомления на электронную почту о новом пароле для сайта *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>По Вашей заявке создан новый пароль</title>
	<meta http-equiv="Content-Type" 	content="text/html; charset=utf-8" />

</head>
<body bgcolor="#ffffff" marginwidth="0" marginheight="0"  leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0">

<h2> Здравствуйте, {$udata['nickname']} </h2>

<br />По Вашей заявке был создан новый пароль для доступа на сайт <a href="{$site['domain']}" target="_blank">{$site['title']}</a>.
<br />Вы можете изменить его в настройках Вашего профиля, после авторизации на сайте.
<br />
<h4>Данные Вашей учетной записи для авторизации:</h4>
Логин: {$udata['login']}
<br />Пароль: {$pass}
<br />
<br /><span style="font-weight: bold;">*</span> Пароль сгенерирован автоматически и известен только вам, никому его не сообщайте.
<br />Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a> никогда не попросит у вас предоставить пароль.
<br />
<br /><em>С наилучшими пожеланиями, Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a></em>

</body>
</html>