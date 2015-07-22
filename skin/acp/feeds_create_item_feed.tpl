{* Шаблон создания элемента страницы *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="panel-heading">
	Новый элемент ленты
</div>

<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" enctype="multipart/form-data" role="form" class="form-horizontal">
	<div class="panel-body">
		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
				Заголовок:
			</label>
			<div class="col-lg-9">
				<input type="text" name="title" id="inputTitle" class="form-control" required>
			</div>
		</div>

		<div class="form-group">
			<label for="inputMetaDescription" class="col-lg-3 control-label">
				Мета описание:
			</label>
			<div class="col-lg-9">
				<input type="text" name="meta_description" id="inputMetaDescription" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="inputMetaKeywords" class="col-lg-3 control-label">
				Мета ключевые слова:
			</label>
			<div class="col-lg-9">
				<input type="text" name="meta_keywords" id="inputMetaKeywords" class="form-control">
			</div>
		</div>
		{if $feed['items_sorting'] == "manual_sorting"}
			{* Manual Sorting*}
			<div class="form-group">
				<label for="inputSort" class="col-lg-3 control-label">
					Порядок расположения в ленте:
				</label>
				<div class="col-lg-9">
					<input type="text" name="itemsort" id="inputSort" class="form-control" value="0" pattern="^[ 0-9]+$">
				</div>
			</div>
		{/if}

		<div class="form-group">
			<label for="inputDateP" class="col-lg-3 control-label">
				Дата публикации: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Разрешается указать дату будущим числом. Посетители увидять публикацию только с наступлением указанной даты." data-placement="right"></span></small>
			</label>
			<div class="col-lg-9">
				<div class="input-group">
					<input type="text" name="date_publications" id="inputDateP" class="form-control datepicker form-date" value="{$date}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}" required>
					<input type="text" name="date_end_publications" id="inputDateEP" class="form-control datepicker form-date" value="" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<label for="brief_item" class="control-label">
					Аннотация:
				</label>
				<textarea id="brief_item" class="form-control ckeditor" name="brief_item" required></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<label for="brief_item" class="control-label">
					Полный текст: <small><span class="fa fa-info fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
				</label>
				<textarea id="full_item" class="form-control ckeditor" name="full_item" required></textarea>
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
				<input type="submit" name="create_item" class="btn btn-success" value="Создать элемент">
			</div>
		</div>
	</div>
</form>