{* Шаблон редактирования HTML блока *}
<div class="panel-heading">
	<script type="text/javascript" src="plugin/ckeditor.php"></script>

	Редактируем HTML блок "{$data['title']}"
</div>
<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=update&block={$data['id']}" enctype="multipart/form-data" role="form" class="form-horizontal">
	<div class="panel-body">
		<div class="form-group">
			<label for="inputAlias" class="col-lg-3 control-label">
				Alias: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="alias" id="inputAlias" class="form-control" value="{$data['alias']}" required>
			</div>
		</div>
		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Заголовок:
			</label>
			<div class="col-lg-9">
				<input type="text" name="title" id="inputTitle" class="form-control" value="{$data['title']}" required>
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
				<input type="number" name="thumb_img_width" id="inputThumbWidth" class="form-control" pattern="^[ 0-9]+$" value="{$data['thumb_img_width']}">
				<small{if $data['thumb_img_width'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['width']}px</small>
			</div>
		</div>
		<div class="form-group">
			<label for="inputThumbHeight" class="col-lg-3 control-label">
				Высота миниатюр картинок у этой страницы:
				<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение в пикселях. Оставьте поле пустым или укажите 0 что бы применить глобальные настройки." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="number" name="thumb_img_height" id="inputThumbHeight" class="form-control" pattern="^[ 0-9]+$" value="{$data['thumb_img_height']}">
				<small{if $data['thumb_img_height'] == 0} class="text-primary"{/if}>По умолчанию: {$default_thumb_size['height']}px</small>
			</div>
		</div>


		<div class="form-group">
			<div class="col-lg-12">
				<label for="content" class="control-label">
					Код блока: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Код блока на языке HTML" data-placement="auto"></span></small>
				</label>
				<textarea id="content" class="form-control ckeditor" name="content" spellcheck required>{$data['content']}</textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				{$attachedimages}
			</div>
			<div class="col-md-6">
				{$attachedfiles}
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
	<div class="panel-footer">
		<div class="row">
			<div class="col-md-12">
				<input type="hidden" name="id" value="{$data['id']}" readonly>
				<input type="hidden" name="oldalias" value="{$data['alias']}" readonly>
				<input type="submit" name="update_block" class="btn btn-success" value="Обновить блок">
			</div>
		</div>
	</div>
</form>