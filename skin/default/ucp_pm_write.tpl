{* Шаблон создания личного сообщения пользователю *}

<h1>Новое сообщение</h1>
<hr />


<div class="row">
	<div class="col-sm-12">
		<form method="post" action="index.php?act=pm&part=send" class="form-horizontal">

			<div class="form-group">
				<label for="inputTo" class="col-lg-4 control-label">Получатель:</label>
				<div class="col-lg-8">
					<select name="to_uid" id="inputTo" class="selectpicker show-tick" required data-size="auto" data-live-search="true" data-width="100%">
						<option value="0" disabled>Выберите получателя</option>
						{foreach from=$userlist item=user}
							{if $user['uid'] != $userdata['uid']}<option value="{$user['uid']}">{$user['nickname']}</option>{/if}
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="inputTitle" class="col-lg-4 control-label">Тема сообщения:</label>
				<div class="col-lg-8">
					<input type="text" name="title" id="inputTitle" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="inputText" class="col-lg-4 control-label">
					Сообщение:
				</label>
				<div class="col-lg-8">
					<textarea class="form-control" name="message" id="inputText" rows="7" required=""></textarea>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-lg-offset-4">
					<a href="index.php?act=pm" class="btn btn-md btn-default"><i class="fa fa-fw fa-long-arrow-left"></i> Вернуться к списку сообщений</a>

					<input type="hidden" name="empty" value="1">
					<button type="submit" name="send" class="btn btn-success pull-right" value="send"><i class="fa fa-fw fa-envelope"></i> Отправить сообщение</button>
				</div>
			</div>

		</form>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="alert alert-warning" role="alert" style="margin-top: 15px;">
			Не передавайте свои персональные данные третьим лицам!
			<br />Администрация сайта никогда не будет просить ваш пароль.
		</div>
	</div>
</div>
