{* Шаблон редактирования элемента страницы *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="panel-heading">
	Составить рассылку
</div>

<form method="post" action="{$SCRIPT_NAME}?act=mailing&part=send" enctype="multipart/form-data" role="form" class="form-horizontal">
	<div class="panel-body">
		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Заголовок:
			</label>
			<div class="col-lg-9">
				<input type="text" name="title" id="inputTitle" class="form-control" value="" required>
			</div>
		</div>

		<div class="form-group">
			<div class="col-lg-12">
				<label for="brief_item" class="control-label">
					Сообщение: <small><span class="fa fa-warning fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
				</label>
				<textarea id="message" class="form-control ckeditor-mail" name="message" required></textarea>
			</div>
		</div>

		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Принудительная рассылка:
			</label>
			<div class="col-lg-9">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default active btn-sm" for="flag_status_false">
						<input type="radio" name="force" value="0" id="flag_status_false" checked> <span class="text-success"><i class="fa fa-fw fa-envelope"></i> Отправить подписчикам</span>
					</label>
					<label class="btn btn-default btn-sm" for="flag_status_true">
						<input type="radio" name="force" value="1" id="flag_status_true"> <span class="text-danger"><i class="fa fa-fw fa-envelope-o"></i> Отправить всем</span>
					</label>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 text-right">
				<input type="submit" name="update_item" class="btn btn-success" value="Отправить сообщение">
			</div>
		</div>
	</div>
</form>