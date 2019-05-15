{* Edit HTML Page *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="card-header align-middle">
	<q>{$data['title']}</q>
	<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['sid']}" class="btn btn-outline-primary btn-sm float-right"><span class="fas fa-edit fa-fw"></span> Редактировать информацию</a>
</div>
<form method="post" action="{$SCRIPT_NAME}?act=pages&part=update&page={$data['sid']}" enctype="multipart/form-data" role="form">
	<div class="card-body">

		<dl class="row">
			<dt class="col-sm-4">ID #:</dt>
			<dd class="col-sm-8">{$data['sid']}</dd>

			<dt class="col-sm-4">Название страницы:</dt>
			<dd class="col-sm-8"><a href="index.php?page={$data['alias']}" target="_blank"><i class="fas fa-external-link-square-alt fa-fw"></i>{$data['title']}</a></dd>

			<dt class="col-sm-4">Алиас страницы:</dt>
			<dd class="col-sm-8">{$data['alias']}</dd>

			{if $data['meta_description'] != ""}
				<dt class="col-sm-4">Мета описание:</dt>
				<dd class="col-sm-8">{$data['meta_description']}</dd>
			{/if}

			{if $data['meta_keywords'] != ""}
				<dt class="col-sm-4">Мета ключевые слова:</dt>
				<dd class="col-sm-8">{$data['meta_keywords']}</dd>
			{/if}

			<dt class="col-sm-4">Последнее обновление:</dt>
			<dd class="col-sm-8">{$data['lm']}</dd>
		</dl>

		<div class="form-group">
			<textarea id="content_field" class="form-control ckeditor" name="content" spellcheck>{$data['content']}</textarea>
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
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<input type="submit" name="update_page" class="btn btn-success btn-lg" value="Обновить страницу">
			</div>
		</div>
	</div>
</form>