{* Уведомления на электронную почту о запросе на смену пароля *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Запрос на восстановления пароля</title>
	<meta http-equiv="Content-Type" 	content="text/html; charset=utf-8" />

</head>
<body bgcolor="#ffffff" marginwidth="0" marginheight="0"  leftmargin="0" topmargin="0" bottommargin="0" rightmargin="0">

<h2> Здравствуйте, {$userdata['nickname']} </h2>

<br />Нам поступил запрос на восстановления пароля, для Вашего аккаунта на сайте <a href="{$site['domain']}" target="_blank">{$site['title']}</a>.
<br />Для смены пароля Вам потребуется код подтверждения: <strong>{$userdata['secret_key']}</strong>. Или вы можете перейти по этой ссылке: {$confirmlink}
<br />После подтверждения, для Вашей учетной записи будет сгенерирован новый пароль и выслан на эту почту.
<br />Изменить сгенерированный пароль, Вы сможете в настройках своего профиля, после авторизации на сайте.
<br />
<br />Если Вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.
<br />
<br /><em>С наилучшими пожеланиями, Администрация сайта <a href="{$site['domain']}" target="_blank">{$site['title']}</a></em>

</body>
</html>