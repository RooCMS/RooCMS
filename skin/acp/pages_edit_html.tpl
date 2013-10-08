{* Шаблон редактирования HTML страницы *}

<script type="text/javascript" src="plugin/ckeditor.php"></script>

<h3>Редактор HTML страницы</h3>
<form method="post" action="{$SCRIPT_NAME}?act=pages&part=update&page={$data['sid']}" enctype="multipart/form-data" role="form" class="form-horizontal cked">
    <div class="row">
    	<div class="col-lg-12">
			<dl class="dl-horizontal">
				<dt>ID #:</dt>
				<dd>{$data['sid']}</dd>

				<dt>Название страницы:</dt>
				<dd><a href="index.php/page-{$data['alias']}" target="_blank">{$data['title']}</a></dd>

				<dt>Алиас страницы:</dt>
				<dd>{$data['alias']}</dd>

				<dt>Мета описание:</dt>
				<dd>{$data['meta_description']}</dd>

				<dt>Мета ключевые слова:</dt>
				<dd>{$data['meta_keywords']}</dd>

				<dt>Последнее обновление:</dt>
				<dd>{$data['lm']}</dd>
			</dl>
    	</div>
    </div>
    <div class="row">
    	<div class="col-lg-12 text-right">
        	<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['sid']}" class="btn btn-link"><span class="icon-edit icon-fixed-width"></span> Редактировать теги</a>
    	</div>
    </div>
	<div class="row">
		<div class="col-lg-12">
			<textarea id="content_field" class="form-control" name="content">{$data['content']}</textarea>
		</div>
	</div>
	<div class="row">
    	<div class="col-lg-12">
        	{$attachedimages}
    	</div>
	</div>
	<div class="row">
    	<div class="col-lg-12">
        	{$imagesupload}
    	</div>
	</div>
	<div class="row">
    	<div class="col-lg-12 text-right">
        	<input type="submit" name="update_page" class="btn btn-success" value="Обновить страницу">
    	</div>
	</div>
</form>

{literal}
<script>
	CKEDITOR.replace( 'content_field' );
</script>
{/literal}