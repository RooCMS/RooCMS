{* Шаблон редактирования элемента страницы *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="card-header">
	Составить рассылку
</div>

<form method="post" action="{$SCRIPT_NAME}?act=mailing&part=send" enctype="multipart/form-data" role="form">
	<div class="card-body">
		{if !empty($groups)}
			<div class="form-group row">
				<label for="inputUserGroups" class="col-md-5 col-lg-4 form-control-plaintext text-right">
					Группа получателей:
				</label>
				<div class="col-md-7 col-lg-8">
					<div class="btn-group-vertical btn-group-toggle roocms-crui" data-toggle="buttons" id="inputGroupAccess">
						{foreach from=$groups item=group}
							<label class="btn btn-light">
								<input type="checkbox" name="gids[]" value="{$group['gid']}" autocomplete="off"><i class="far fa-fw fa-square"></i> {$group['title']}
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		{/if}

		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Получатели:
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="btn-group-vertical btn-group-toggle roocms-crui" data-toggle="buttons">
					<label class="btn btn-light active" for="flag_status_false">
						<input type="radio" name="force" value="0" id="flag_status_false" checked> <i class="far fa-fw fa-check-circle text-success"></i> Только подписавшиеся на рассылку
					</label>
					<label class="btn btn-light" for="flag_status_true">
						<input type="radio" name="force" value="1" id="flag_status_true"> <i class="far fa-fw fa-circle text-danger" aria-describedby="forecAllSupport"></i> Экстренная рассылка для всех
					</label>
					<small id="forecAllSupport" class="form-text text-gray">
						Используйте экстренную рассылку только в крайнем случае
					</small>
				</div>
			</div>
		</div>

		<hr />

		{if $config->global_email == ""}
			<div class="alert alert-danger">
				<i class="fa fa-fw fa-exclamation-triangle"></i> Пожалуйста, укажите <a href="{$SCRIPT_NAME}?act=config&part=global" class="alert-link">в настройках</a> E-Mail для администрации сайта
			</div>
		{/if}

		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" value="" required{if $config->global_email == ""} disabled{/if}>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-12">
				<label for="brief_item" class="control-label">
					Сообщение: <small><span class="fas fa-exclamation-triangle fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
				</label>
				<textarea id="message" class="form-control ckeditor-mail" name="message" required{if $config->global_email == ""} disabled{/if}></textarea>
			</div>
		</div>

		<div class="alert alert-warning mb-0">
			<i class="fa fa-fw fa-exclamation-triangle"></i>
			<b>Внимание!</b> Пожалуйста, тщательно проверьте свое сообщение перед отправкой получателям. У вас не будет возможности внести исправления после того, как Вы его отправите.
		</div>

	</div>
	{if $config->global_email != ""}
	<div class="card-footer">
		<div class="row">
			<div class="col-12">
				<input type="submit" name="update_item" class="btn btn-lg btn-success" value="Отправить сообщение">
			</div>
		</div>
	</div>
	{/if}
</form>