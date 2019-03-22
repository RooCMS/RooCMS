{* Create HTML block template *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>
<div class="card-header">
	Новый HTML блок
</div>
<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=create&type=html" enctype="multipart/form-data" role="form">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputAlias" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Alias: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="alias" id="inputAlias" class="form-control" required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" required>
			</div>
		</div>

		<hr>

		{* Миниаютры *}
		<div class="form-group row">
			<label for="inputThumbWidth" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Ширина миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="number" name="thumb_img_width" id="inputThumbWidth" class="form-control" pattern="^[ 0-9]+$">
				<small>По умолчанию: {$default_thumb_size['width']}px</small>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputThumbHeight" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Высота миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="number" name="thumb_img_height" id="inputThumbHeight" class="form-control" pattern="^[ 0-9]+$">
				<small>По умолчанию: {$default_thumb_size['height']}px</small>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-12">
				<label for="content" class="control-label">
					Код блока: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Код блока на языке HTML" data-placement="auto"></span></small>
				</label>
				<textarea id="content" class="form-control ckeditor" name="content" spellcheck required></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				{$imagesupload}
			</div>
			<div class="col-md-6">
				{$filesupload}
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-12">
				<input type="submit" name="create_block" class="btn btn-lg btn-success" value="Создать блок">
			</div>
		</div>
	</div>
</form>