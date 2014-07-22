{* Шаблон создания HTML блока *}
<div class="panel-heading">
	<script type="text/javascript" src="plugin/ckeditor.php"></script>

	Новый HTML блок
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=create&type=html" enctype="multipart/form-data" role="form" class="form-horizontal">
		<div class="form-group">
			<label for="inputAlias" class="col-md-3 control-label">
				Alias: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="auto"></span></small>
			</label>
			<div class="col-md-9">
				<input type="text" name="alias" id="inputAlias" class="form-control" required>
			</div>
		</div>
		<div class="form-group">
			<label for="inputTitle" class="col-md-3 control-label">
				Заголовок:
			</label>
			<div class="col-md-9">
				<input type="text" name="title" id="inputTitle" class="form-control" required>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
			    <label for="content" class="control-label">
				Код блока: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Код блока на языке HTML" data-placement="auto"></span></small>
			    </label>
				<textarea id="content" class="form-control ckeditor" name="content" required></textarea>
			</div>
		</div>

		<div class="row images_attach">
			<div class="col-md-12">
				{$imagesupload}
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-right">
				<input type="submit" name="create_block" class="btn btn-success" value="Создать блок">
			</div>
		</div>
	</form>
</div>