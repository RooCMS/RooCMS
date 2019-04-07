{* Temaplte Read PM *}
<div class="container">
	<div class="row">
		<div class="col-12">
			<h1>{$message['title']}</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card airmail">
				<div class="card-body">
					<small class="float-right text-right">
						От: {$message['from_name']}
						<br />Отправлено: {$message['date_send']}
					</small>
					{$message['showmessage']}
				</div>
			</div>
		</div>
	</div>
	<div class="row my-3">
		<div class="col-12 text-center">
			<a href="{$SCRIPT_NAME}?part=ucp&act=pm" class="btn btn-outline-primary"><i class="fas fa-fw fa-long-arrow-alt-left"></i> Вернуться к списку сообщений</a>
		</div>
	</div>
	<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=pm&move=send">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title pb-2 border-bottom">Ответить</h4>
				<div class="form-group">
					<label for="inputTo">Получатель:</label>
					<select name="to_uid" id="inputTo" class="selectpicker" required data-size="auto" data-live-search="true" data-width="100%">
						{*<option value="0" disabled>Выберите получателя</option>*}
						{foreach from=$userlist item=user}
							{if $user['uid'] != $userdata['uid']}<option value="{$user['uid']}"{if $message['from_uid'] == $user['uid']} selected{/if}>{$user['nickname']}</option>{/if}
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label for="inputTitle">Тема сообщения:</label>
					<input type="text" name="title" id="inputTitle" class="form-control">
				</div>
				<div class="form-group">
					<label for="inputText">Сообщение:</label>
					<textarea class="form-control" name="message" id="inputText" rows="7" required>
Цитата {$message['from_name']}:
-------------------------------
{$message['message']}
-------------------------------
					</textarea>
				</div>
				<button type="submit" name="send" class="btn btn-success" value="send"><i class="fas fa-fw fa-envelope"></i> Отправить сообщение</button>
			</div>
		</div>
	</form>
</div>