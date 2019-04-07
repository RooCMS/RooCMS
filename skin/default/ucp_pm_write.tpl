{* Template PM Write *}
<div class="container">
	<div class="row">
		<div class="col-12">
			<h1>Новое сообщение</h1>
		</div>
	</div>
	<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=pm&move=send">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title pb-2 border-bottom">Новое сообщение</h4>
				<div class="form-group">
					<label for="inputTo">Получатель:</label>
					<select name="to_uid" id="inputTo" class="selectpicker" required data-size="auto" data-live-search="true" data-width="100%">
						<option value="0" disabled>Выберите получателя</option>
						{foreach from=$userlist item=user}
							{if $user['uid'] != $userdata['uid']}<option value="{$user['uid']}">{$user['nickname']}</option>{/if}
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label for="inputTitle">Тема сообщения:</label>
					<input type="text" name="title" id="inputTitle" class="form-control">
				</div>
				<div class="form-group">
					<label for="inputText">Сообщение:</label>
					<textarea class="form-control" name="message" id="inputText" rows="7" required></textarea>
				</div>
				<button type="submit" name="send" class="btn btn-success" value="send"><i class="fas fa-fw fa-envelope"></i> Отправить сообщение</button>

				<div class="alert alert-warning mt-3" role="alert">
					Не передавайте свои персональные данные третьим лицам!
					<br />Администрация сайта никогда не будет просить ваш пароль.
				</div>
			</div>
		</div>
	</form>
</div>