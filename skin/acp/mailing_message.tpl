{* Шаблон редактирования элемента страницы *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="card-header">
	Составить рассылку
</div>

<form method="post" action="{$SCRIPT_NAME}?act=mailing&part=send" enctype="multipart/form-data" role="form">
	<div class="card-body">

		{if !empty($groups)}
			<div class="form-group row">
				<label for="inputUserGroups" class="col-md-4 form-control-plaintext text-right">
					Группа получателей:
				</label>
				<div class="col-md-8">
					<div class="btn-group btn-group-toggle roocms-boolui" data-toggle="buttons" id="inputGroupAccess">
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
			<label for="inputTitle" class="col-md-4 form-control-plaintext text-right">
				Условия рассылки:
			</label>
			<div class="col-md-8">
				<div class="btn-group btn-group-toggle roocms-boolui" data-toggle="buttons">
					<label class="btn btn-light" for="flag_status_false">
						<input type="radio" name="force" value="0" id="flag_status_false"> <i class="far fa-fw fa-square text-success"></i> Отправить только подписавшимся
					</label>
					<label class="btn btn-light active" for="flag_status_true">
						<input type="radio" name="force" value="1" id="flag_status_true" checked> <i class="far fa-fw fa-check-square text-danger"></i> Отправить всем
					</label>
				</div>
			</div>
		</div>

		<hr />

		<div class="form-group row">
			<label for="inputTitle" class="col-md-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-md-8">
				<input type="text" name="title" id="inputTitle" class="form-control" value="" required>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-md-12">
				<label for="brief_item" class="control-label">
					Сообщение: <small><span class="fas fa-exclamation-triangle fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
				</label>
				<textarea id="message" class="form-control ckeditor-mail" name="message" required></textarea>
			</div>
		</div>

	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<input type="submit" name="update_item" class="btn btn-lg btn-success" value="Отправить сообщение">
			</div>
		</div>
	</div>
</form>