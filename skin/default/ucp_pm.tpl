{* Шаблон личных сообщений пользователя *}

<h1>Личные cообщения</h1>
{if empty($pm)}<hr />{/if}

{*<div class="row">
	<div class="col-sm-12">
		<a href="index.php?act=pm&part=write" class="btn btn-md btn-default"><i class="fa fa-fw fa-envelope"></i> Написать сообщение</a>
	</div>
</div>*}

<div class="row">
{if empty($pm)}
	<div class="col-md-12 text-center">
		<br />У вас нет сообщений
	</div>
{else}
	<div class="col-md-12">
		<div class="list-group">
			{foreach from=$pm item=m}
			<a href="index.php?part=ucp&act=pm&move=read&id={$m['id']}" class="list-group-item{if $m['see'] == 0} list-group-item-info{/if}">
				<i class="fa fa-envelope{if $m['see'] != 0}-o{/if} fa-2x pull-left"></i><h4 class="list-group-item-heading">{$m['title']}</h4>
				<span class="small list-group-item-text">Отправлено: {$m['date_send']}</span>
				<span class="pull-right list-group-item-text">От: {$m['from_name']}</span>
			</a>
			{/foreach}
		</div>
	</div>
{/if}
</div>

{if empty($pm)}<hr />{/if}
<h2>Новое сообщение</h2>

<div class="row">
	<div class="col-sm-12">
		<form method="post" action="index.php?part=ucp&act=pm&move=send" class="form-horizontal">

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
					<input type="hidden" name="empty" value="1">
					<button type="submit" name="send" class="btn btn-success pull-right" value="send"><i class="fa fa-fw fa-envelope"></i> Отправить сообщение</button>
				</div>
			</div>

		</form>
	</div>
</div>
