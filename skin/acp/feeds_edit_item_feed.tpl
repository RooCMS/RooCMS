{* Шаблон редактирования элемента страницы *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<h3>Редактируем "{$item['title']}"</h3>


<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_item&item={$item['id']}&page={$item['sid']}" enctype="multipart/form-data" role="form" class="form-horizontal">
	<div class="form-group">
	    <label for="inputTitle" class="col-lg-3 control-label">
    		Заголовок:
	    </label>
	    <div class="col-lg-9">
			<input type="text" name="title" id="inputTitle" class="form-control" value="{$item['title']}" required>
		</div>
	</div>

	<div class="form-group">
	    <label for="inputMetaDescription" class="col-lg-3 control-label">
    		Мета описание:
	    </label>
	    <div class="col-lg-9">
			<input type="text" name="meta_description" id="inputMetaDescription" class="form-control" value="{$item['meta_description']}">
		</div>
	</div>
	<div class="form-group">
	    <label for="inputMetaKeywords" class="col-lg-3 control-label">
    		Мета описание:
	    </label>
	    <div class="col-lg-9">
			<input type="text" name="meta_keywords" id="inputMetaKeywords" class="form-control" value="{$item['meta_keywords']}">
		</div>
	</div>

	<div class="form-group">
	    <label for="inputDateP" class="col-lg-3 control-label">
    		Дата публикации: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Разрешается указать дату будущим числом. Посетители увидять публикацию только с наступлением указанной даты." data-placement="right"></span></small>
	    </label>
	    <div class="col-lg-9">
	    	<div class="input-group">
				<input type="text" name="date_publications" id="inputDateP" class="form-control datepicker form-date" value="{$item['date_publications']}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}" required>
				<input type="text" name="date_end_publications" id="inputDateEP" class="form-control datepicker form-date" value="{if $item['date_end_publications']}{$item['date_end_publications']}{/if}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
		    <label for="brief_item" class="control-label">
    			Аннотация: <small><span class="icon-info icon-fixed-width text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
		    </label>
			<textarea id="brief_item" class="form-control" name="brief_item" required>{$item['brief_item']}</textarea>
			{literal}<script>CKEDITOR.replace( 'brief_item' );</script>{/literal}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
		    <label for="brief_item" class="control-label">
    			Полный текст:
		    </label>
			<textarea id="full_item" class="form-control" name="full_item" required>{$item['full_item']}</textarea>
			{literal}<script>CKEDITOR.replace( 'full_item' );</script>{/literal}
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
        	<input type="submit" name="update_item" class="btn btn-success" value="Сохранить элемент">
    	</div>
	</div>
</form>
