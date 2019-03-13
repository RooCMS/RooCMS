{* Шаблон личного сообщения пользователя *}
<div class="row">
	<div class="col-sm-12">
		<h1>{$message['title']}</h1>
	</div>
</div>
{*<div class="row">
	<div class="col-sm-12">
		<a href="{$SCRIPT_NAME}?act=pm&part=write" class="btn btn-md btn-default"><i class="fa fa-fw fa-envelope"></i> Написать сообщение</a>
	</div>
</div>*}

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default pm-message">
			<div class="panel-body">
				<div class="airmail">
					<small class="pull-right text-right">
						От: {$message['from_name']}
						<br />Отправлено: {$message['date_send']}
					</small>
					{$message['showmessage']}
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<br /><a href="{$SCRIPT_NAME}?part=ucp&act=pm" class="btn btn-md btn-default pull-right"><i class="fa fa-fw fa-long-arrow-left"></i> Вернуться к списку сообщений</a>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<h2>Ответить</h2>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=pm&move=send">

			<div class="form-group">
				<label for="inputTo" class="col-lg-4 control-label">Получатель:</label>
				<div class="col-lg-8">
					<select name="to_uid" id="inputTo" class="selectpicker show-tick" required data-size="auto" data-live-search="true" data-width="100%">
						{*<option value="0" disabled>Выберите получателя</option>*}
						{foreach from=$userlist item=user}
							{if $user['uid'] != $userdata['uid']}<option value="{$user['uid']}"{if $message['from_uid'] == $user['uid']} selected{/if}>{$user['nickname']}</option>{/if}
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="inputTitle" class="col-lg-4 control-label">Тема сообщения:</label>
				<div class="col-lg-8">
					<input type="text" name="title" id="inputTitle" class="form-control" value="RE: {$message['title']}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputText" class="col-lg-4 control-label">
					Сообщение:
				</label>
				<div class="col-lg-8">
					<textarea class="form-control" name="message" id="inputText" rows="7" required="">Цитата {$message['from_name']}:
-------------------------------------------
{$message['message']}
-------------------------------------------</textarea>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-lg-offset-4">
					<button type="submit" name="send" class="btn btn-success pull-right" value="send"><i class="fa fa-fw fa-mail-reply"></i> Ответить {$message['from_name']}</button>
				</div>
			</div>

		</form>
	</div>
</div>

