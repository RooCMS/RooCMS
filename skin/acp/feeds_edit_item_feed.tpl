{* Шаблон редактирования элемента страницы *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="panel-heading">
	Редактируем "{$item['title']}"
</div>

<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_item&item={$item['id']}&page={$item['sid']}" enctype="multipart/form-data" role="form" class="form-horizontal">
	<div class="panel-body">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default{if $item['status'] == 1} active{/if} btn-sm" for="flag_status_true" rel="tooltip" title="Публиковать" data-placement="auto" data-container="body">
				<input type="radio" name="status" value="1" id="flag_status_true"{if $item['status'] == 1} checked{/if}> <span class="text-success"><span class="fa fa-fw fa-eye"></span></span>
			</label>
			<label class="btn btn-default{if $item['status'] == 0} active{/if} btn-sm" for="flag_status_false" rel="tooltip" title="Скрыть" data-placement="auto" data-container="body">
				<input type="radio" name="status" value="0" id="flag_status_false"{if $item['status'] == 0} checked{/if}> <span class="text-danger"><span class="fa fa-fw fa-eye-slash"></span></span>
			</label>
		</div>
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
				Мета ключевые слова:
			</label>
			<div class="col-lg-9">
				<input type="text" name="meta_keywords" id="inputMetaKeywords" class="form-control" value="{$item['meta_keywords']}">
			</div>
		</div>

		{if $feed['items_sorting'] == "manual_sorting"}
			{* Manual Sorting*}
			<div class="form-group">
				<label for="inputSort" class="col-lg-3 control-label">
					Порядок расположения в ленте:
				</label>
				<div class="col-lg-9">
					<input type="text" name="itemsort" id="inputSort" class="form-control" value="{$item['sort']}" pattern="^[ 0-9]+$">
				</div>
			</div>
		{/if}

		<div class="form-group">
			<label for="inputDateP" class="col-lg-3 control-label">
				Дата публикации: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Разрешается указать дату будущим числом. Посетители увидять публикацию только с наступлением указанной даты." data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<div class="input-group">
					<input type="text" name="date_publications" id="inputDateP" class="form-control datepicker form-date" value="{$item['date_publications']}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}" required>
					<input type="text" name="date_end_publications" id="inputDateEP" class="form-control datepicker form-date" value="{if $item['date_end_publications']}{$item['date_end_publications']}{/if}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}">
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="inputTags" class="col-lg-3 control-label">
				Метки:
			</label>
			<div class="col-lg-9">
				<input type="text" name="tags" id="inputTags" class="form-control, tagsinput" value="{$item['tags']}">
			</div>
		</div>

		<div class="form-group">
			<label for="author" class="col-lg-3 control-label">
				Автор:
			</label>
			<div class="col-lg-9">
				<select name="author_id" id="author_id" class="selectpicker show-tick" required data-size="auto" data-live-search="true" data-width="100%">
					<option value="0">Без автора</option>
					{foreach from=$userlist item=user}
						<option value="{$user['uid']}" data-subtext="{$user['uid']}" {if $user['uid'] == $item['author_id']} selected{/if}>{$user['nickname']}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<label for="brief_item" class="control-label">
					Аннотация:
				</label>
				<textarea id="brief_item" class="form-control ckeditor" name="brief_item" required>{$item['brief_item']}</textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<label for="brief_item" class="control-label">
					Полный текст: <small><span class="fa fa-warning fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
				</label>
				<textarea id="full_item" class="form-control ckeditor" name="full_item" required>{$item['full_item']}</textarea>
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

		<div class="row">
			<div class="col-lg-12 text-right">
				<input type="submit" name="update_item" class="btn btn-success" value="Сохранить элемент">
			</div>
		</div>
	</div>
</form>

