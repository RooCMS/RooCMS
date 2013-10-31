{* Шаблон редактирования элемента страницы *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_item&item={$item['id']}&page={$item['sid']}" enctype="multipart/form-data" role="form" class="form-horizontal">
	<h3>
		Редактируем "{$item['title']}"
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default{if $item['status'] == 1} active{/if}" for="flag_status_true" rel="tooltip" title="Публиковать" data-placement="auto" data-container="body">
				<input type="radio" name="status" value="1" id="flag_status_true"{if $item['status'] == 1} checked{/if}> <span class="text-success"><span class="fa fa-fw fa-eye"></span></span>
			</label>
			<label class="btn btn-default{if $item['status'] == 0} active{/if}" for="flag_status_false" rel="tooltip" title="Скрыть" data-placement="auto" data-container="body">
				<input type="radio" name="status" value="0" id="flag_status_false"{if $item['status'] == 0} checked{/if}> <span class="text-danger"><span class="fa fa-fw fa-eye-slash"></span></span>
			</label>
		</div>
	</h3>
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
			Дата публикации: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Разрешается указать дату будущим числом. Посетители увидять публикацию только с наступлением указанной даты." data-placement="left"></span></small>
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
				Аннотация: <small><span class="fa fa-info fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
			</label>
			<textarea id="brief_item" class="form-control ckeditor" name="brief_item" required>{$item['brief_item']}</textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<label for="brief_item" class="control-label">
				Полный текст:
			</label>
			<textarea id="full_item" class="form-control ckeditor" name="full_item" required>{$item['full_item']}</textarea>
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
