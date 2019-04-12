{* Уведомления на электронную почту пользователя о регистрации на сайте *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Добро пожаловать</title>
	<meta http-equiv="Content-Type" 	content="text/html; charset=utf-8" />

</head>
<body bgcolor="#ffffff" marginwidth="0" marginheight="0"  leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0">

<h2> Здравствуйте, {$nickname} </h2>

<br />Поздравляем вас с успешной регистрацией на сайте <a href="{$site['domain']}" target="_blank">{$site['title']}</a>.
<br />
<br />Что бы завершить регистрацию, вам осталось подтвердить свой почтовый ящик. Ваш код подтверждения: <strong>{$activation['code']}</strong>. Или вы можете перейти по этой ссылке: {$activation['link']}
<br />
<h4>Данные Вашей учетной записи для авторизации:</h4>
Логин: {$login}
<br />Пароль: {$password}
<br />
<br /><span style="font-weight: bold;">*</span> Пароль известен только вам, никому его не сообщайте.
<br />Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a> никогда не попросит у вас предоставить пароль.
<br />
<br />Спасибо, что присоеденились к нам и Добро Пожаловать.
<br />
<br /><em>С наилучшими пожеланиями, Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a></em>

</body>
</html>