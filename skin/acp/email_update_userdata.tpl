{* Уведомления на электронную почту пользователя о созданние ему учетной записи *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Ваши данные обновились</title>
	<meta http-equiv="Content-Type" 	content="text/html; charset=utf-8" />

</head>
<body bgcolor="#fff" marginwidth="0" marginheight="0"  leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0">

<h2> Здравствуйте, {$nickname} </h2>

Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a> обновила данные вашей учетной записи.

<h4>Вот ваши новые данные:</h4>
Логин: {$login}
Никнейм: {$nickname}
E-Mail: {$email}
{if isset($password) && trim($password) != ""}
Пароль: {$password}

<strong>*</strong> Пароль был создан автоматически и известен только вам, никому его не сообщайте.
Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a> никогда не попросит у вас предоставить пароль.
{else}
Пароль: <em>не изменялся</em>
{/if}

<em>С наилучшими пожеланиями, Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a></em>

</body>
</html>