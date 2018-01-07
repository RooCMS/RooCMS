{* Шаблон создания HTML блока *}
<div class="panel-heading">
	<script type="text/javascript" src="plugin/ckeditor.php"></script>

	Новый HTML блок
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=create&type=html" enctype="multipart/form-data" role="form" class="form-horizontal">
		<div class="form-group">
			<label for="inputAlias" class="col-md-3 control-label">
				Alias: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></span></small>
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

		<hr>

		{* Миниаютры *}
		<div class="form-group">
			<label for="inputThumbWidth" class="col-lg-3 control-label">
				Ширина миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="thumb_img_width" id="inputThumbWidth" class="form-control" pattern="^[ 0-9]+$">
				<small>По умолчанию: {$default_thumb_size['width']}px</small>
			</div>
		</div>
		<div class="form-group">
			<label for="inputThumbHeight" class="col-lg-3 control-label">
				Высота миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="thumb_img_height" id="inputThumbHeight" class="form-control" pattern="^[ 0-9]+$">
				<small>По умолчанию: {$default_thumb_size['height']}px</small>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12">
			    <label for="content" class="control-label">
				Код блока: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Код блока на языке HTML" data-placement="auto"></span></small>
			    </label>
				<textarea id="content" class="form-control ckeditor" name="content" spellcheck required></textarea>
			</div>
		</div>

		<div class="row field_attach">
			<div class="col-md-6">
				{$imagesupload}
			</div>
			<div class="col-md-6">
				{$filesupload}
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 text-right">
				<input type="submit" name="create_block" class="btn btn-success" value="Создать блок">
			</div>
		</div>
	</form>
</div>