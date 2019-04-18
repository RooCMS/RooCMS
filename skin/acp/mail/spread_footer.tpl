{* Email notice: Spread footer *}
<hr />
<small>
	Веб версия сообщения: <a href="{$site['protocol']}://{$site['domain']}/?part=mailing&act=letter&id={$message_id}&secret={$secret_key}" target="_blank">Читать на сайте "{$site['title']}"</a>
	<br />Если Вы не хотите получать от нас в дальнейшем сообщения на электронную почту, перейдите по ссылке ниже:
	<br />[<a href="{$site['protocol']}://{$site['domain']}/?part=unsubscribe&uid={$userdata['uid']}&code={$userdata['secret_key']}" target="_blank">Не желаю больше получать новости от "{$site['title']}"</a>]
</small>
</body>
</html>