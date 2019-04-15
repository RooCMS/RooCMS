{* Template PM *}
<div class="container mb-4">
	<div class="row">
		<div class="col-12">
			<h1>Личные cообщения</h1>
		</div>
	</div>
	{*<div class="row">
		<div class="col-sm-12">
			<a href="{$SCRIPT_NAME}?act=pm&part=write" class="btn btn-primary"><i class="fas fa-fw fa-envelope"></i> Написать сообщение</a>
		</div>
	</div>*}
	<div class="row">
		<div class="col-12">
			<div class="list-group mb-3">
				{if empty($pm)}
					<a href="#" class="list-group-item list-group-item-action disabled">Личных сообщений нет</a>
				{else}
					{foreach from=$pm item=m}
						<a href="{$SCRIPT_NAME}?part=ucp&act=pm&move=read&id={$m['id']}" class="list-group-item list-group-item-action{if $m['see'] != 0} list-group-item-light{/if}">
							<div class="d-inline-flex w-100 justify-content-between">
								<h5 class="mb-1"><i class="fas fa-envelope{if $m['see'] != 0}-open-text{else} text-secondary{/if} mr-3"></i>{$m['title']}</h5>
								<small class="text-right text-gray">{$m['date_send']}<br />{$m['from_name']}</small>
							</div>
						</a>
					{/foreach}
				{/if}
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
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-sm-12">
		<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=pm&move=send">

			<div class="row">
				<div class="col-lg-8 col-lg-offset-4">

				</div>
			</div>

		</form>
	</div>
</div>
