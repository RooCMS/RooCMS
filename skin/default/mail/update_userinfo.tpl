{* Уведомления на электронную почту пользователя о созданние ему учетной записи *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Ваши данные обновились</title>
	<meta http-equiv="Content-Type" 	content="text/html; charset=utf-8" />

</head>
<body bgcolor="#ffffff" marginwidth="0" marginheight="0"  leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0">

<h2> Здравствуйте, {$nickname} </h2>

<br />Ваши данные на сайте <a href="{$site['domain']}" target="_blank">{$site['title']}</a> были обновлены.
<br />
<h4>Вот ваши новые данные:</h4>
Логин: {$login}
<br />Никнейм: {$nickname}
<br />E-Mail: {$email}
{if isset($password) && trim($password) != ""}
	<br />Пароль: {$password}
	<br />
	<br /><strong>*</strong> Пароль был создан автоматически и известен только вам, никому его не сообщайте.
	<br /><br />Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a> никогда не попросит у вас предоставить пароль.
{else}
	<br />Пароль: <em>не изменялся</em>
{/if}
<br />
<br /><em>С наилучшими пожеланиями, Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a></em>

</body>
</html>