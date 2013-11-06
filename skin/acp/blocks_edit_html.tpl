{* Шаблон редактирования HTML блока *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<h3>Редактируем HTML блок "{$data['title']}"</h3>

<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=update&block={$data['id']}" enctype="multipart/form-data" role="form" class="form-horizontal">
	<div class="form-group">
	    <label for="inputAlias" class="col-lg-3 control-label">
    		Alias: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="auto"></span></small>
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
	<div class="row">
		<div class="col-lg-12">
		    <label for="content" class="control-label">
    			Код блока: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Код блока на языке HTML" data-placement="auto"></span></small>
		    </label>
			<textarea id="content" class="form-control ckeditor" name="content" required>{$data['content']}</textarea>
		</div>
	</div>

	<div class="row">
    	<div class="col-lg-12">
        	{$attachedimages}
    	</div>
	</div>
	<div class="row images_attach">
    	<div class="col-lg-12">
        	{$imagesupload}
    	</div>
	</div>
	<div class="row">
    	<div class="col-lg-12 text-right">
    		<input type="hidden" name="id" value="{$data['id']}" readonly>
			<input type="hidden" name="oldalias" value="{$data['alias']}" readonly>
        	<input type="submit" name="update_block" class="btn btn-success" value="Обновить блок">
    	</div>
	</div>
</form>