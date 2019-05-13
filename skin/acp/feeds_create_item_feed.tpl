{* Template Create Feed Unit *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="card-header">
	Новый запись в ленте
</div>

<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" enctype="multipart/form-data" role="form">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputMetaTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Мета Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="meta_title" id="inputMetaTitle" class="form-control" spellcheck>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputMetaDescription" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Мета описание:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="meta_description" id="inputMetaDescription" class="form-control" spellcheck>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputMetaKeywords" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Мета ключевые слова:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="meta_keywords" id="inputMetaKeywords" class="form-control" spellcheck>
			</div>
		</div>
		{if $feed['items_sorting'] == "manual_sorting"}
			{* Manual Sorting*}
			<div class="form-group row">
				<label for="inputSort" class="col-md-5 col-lg-4 form-control-plaintext text-right">
					Порядок расположения в ленте:
				</label>
				<div class="col-md-7 col-lg-8">
					<input type="number" name="itemsort" id="inputSort" class="form-control" value="0" pattern="^[ 0-9]+$">
				</div>
			</div>
		{/if}

		<div class="form-group row">
			<label for="inputDateP" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Дата публикации: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Разрешается указать дату будущим числом. Посетители увидять публикацию только с наступлением указанной даты." data-placement="right"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="input-group">
					<input type="text" name="date_publications" id="inputDateP" class="form-control datepicker w-50" value="{$date}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}" required>
					<input type="text" name="date_end_publications" id="inputDateEP" class="form-control datepicker w-50" value="" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}">
				</div>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputTags" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Метки:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="tags" id="inputTags" class="form-control tagsinput">
				{if !empty($poptags)}
					<div class="mt-1">
						{foreach from=$poptags item=tag}
							<a href=#" name="assdag" class="addtag btn btn-sm btn-outline-primary" data-value="{$tag['title']}">{$tag['title']}</a>
						{/foreach}
					</div>
				{/if}
			</div>
		</div>

		<div class="form-group row">
			<label for="author" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Автор:
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="row">
					<div class="col-12 col-lg-6">
						<select name="author_id" id="author" class="selectpicker" required data-size="auto" data-live-search="true" data-width="100%">
							<option value="0">Без автора</option>
							{foreach from=$userlist item=user}
								<option value="{$user['uid']}" {if $user['uid'] == $userdata['uid']} selected{/if}>{$user['nickname']}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Рассылка:
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="btn-group btn-group-toggle roocms roocms-crui" data-toggle="buttons">
					<label class="btn btn-light active" for="flag_status_ignore">
						<input type="radio" name="mailing" value="0" id="flag_status_ignore" checked> <i class="far fa-fw fa-check-circle"></i> Не осуществлять рассылку
					</label>
					<label class="btn btn-light" for="flag_status_false">
						<input type="radio" name="mailing" value="1" id="flag_status_false"> <i class="far fa-fw fa-circle"></i> Отправить подписчикам
					</label>
				</div>
			</div>
		</div>

		{if !empty($groups)}
			<div class="form-group row">
				<label for="inputGroupAccess" class="col-md-5 col-lg-4 form-control-plaintext text-right">
					Доступ для групп:
					<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Укажите какие группы пользователей смогут просматривать эту публикацию" data-placement="left"></span></small>
				</label>
				<div class="col-md-7 col-lg-8">
					<div class="btn-group-vertical btn-group-toggle roocms-crui" data-toggle="buttons" id="inputGroupAccess">
						{foreach from=$groups item=group}
							<label class="btn btn-light">
								<input type="checkbox" name="gids[]" value="{$group['gid']}" autocomplete="off"><i class="far fa-fw fa-square"></i> {$group['title']}
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		{/if}

		<div class="row mb-2">
			<div class="col-12">
				<label for="brief_item" class="control-label">
					Аннотация:
				</label>
				<textarea id="brief_item" class="form-control ckeditor" name="brief_item" spellcheck required></textarea>
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-12">
				<label for="brief_item" class="control-label">
					Полный текст: <small><span class="fas fa-exclamation-triangle fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
				</label>
				<textarea id="full_item" class="form-control ckeditor" name="full_item" spellcheck required></textarea>
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
				<input type="hidden" name="status" value="1" readonly>
				<input type="submit" name="create_item" class="btn btn-lg btn-success" value="Создать элемент">
			</div>
		</div>
	</div>
</form>
