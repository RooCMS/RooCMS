{* Email notice : create account *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Добро пожаловать</title>
	<meta http-equiv="Content-Type" 	content="text/html; charset=utf-8" />

</head>
<body bgcolor="#ffffff" marginwidth="0" marginheight="0"  leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0">

<h2> Здравствуйте, {$nickname} </h2>

<br />Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a> создала для вас приватную учетную запись, которой вы можете пользоваться для получения персонального доступа к сайту.
<br />
<h4>Данные Вашей учетной записи для авторизации:</h4>
Логин: {$login}
<br />Пароль: {$password}
<br />
<br /><span style="font-weight: bold;">*</span> Пароль был создан автоматически и известен только вам, никому его не сообщайте.
<br />Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a> никогда не попросит у вас предоставить пароль.
<br />
<br />Спасибо, что присоеденились к нам и Добро Пожаловать.
<br />
<br /><em>С наилучшими пожеланиями, Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a></em>

</body>
</html>