{* Template Edit Feed Unit *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="card-header">
	Редактируем "{$item['title']}"
</div>

<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_item&item={$item['id']}&page={$item['sid']}" enctype="multipart/form-data" role="form">
	<div class="card-body">
		<div class="btn-group btn-group-toggle" data-toggle="buttons">
			<label class="btn btn-outline-success{if $item['status'] == 1} active{/if}" for="flag_status_true" rel="tooltip" title="Публиковать" data-placement="auto" data-container="body">
				<input type="radio" name="status" value="1" id="flag_status_true"{if $item['status'] == 1} checked{/if}> <i class="fa fa-fw fa-eye"></i>
			</label>
			<label class="btn btn-outline-danger{if $item['status'] == 0} active{/if}" for="flag_status_false" rel="tooltip" title="Скрыть" data-placement="auto" data-container="body">
				<input type="radio" name="status" value="0" id="flag_status_false"{if $item['status'] == 0} checked{/if}> <i class="fa fa-fw fa-eye-slash"></i>
			</label>
		</div>
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" value="{$item['title']}" spellcheck required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputMetaTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Мета Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="meta_title" id="inputMetaTitle" class="form-control" value="{$item['meta_title']}" spellcheck>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputMetaDescription" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Мета описание:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="meta_description" id="inputMetaDescription" class="form-control" value="{$item['meta_description']}" spellcheck>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputMetaKeywords" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Мета ключевые слова:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="meta_keywords" id="inputMetaKeywords" class="form-control" value="{$item['meta_keywords']}" spellcheck>
			</div>
		</div>

		{if $feed['items_sorting'] == "manual_sorting"}
			{* Manual Sorting*}
			<div class="form-group row">
				<label for="inputSort" class="col-md-5 col-lg-4 form-control-plaintext text-right">
					Порядок расположения в ленте:
				</label>
				<div class="col-md-7 col-lg-8">
					<input type="number" name="itemsort" id="inputSort" class="form-control" value="{$item['sort']}" pattern="^[ 0-9]+$">
				</div>
			</div>
		{/if}

		<div class="form-group row">
			<label for="inputDateP" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Дата публикации: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Разрешается указать дату будущим числом. Посетители увидять публикацию только с наступлением указанной даты." data-placement="left"></span></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="input-group">
					<input type="text" name="date_publications" id="inputDateP" class="form-control datepicker w-50" value="{$item['date_publications']}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}" required>
					<input type="text" name="date_end_publications" id="inputDateEP" class="form-control datepicker w-50" value="{if $item['date_end_publications']}{$item['date_end_publications']}{/if}" placeholder="дд.мм.гггг" pattern="{literal}\d{1,2}\.\d{1,2}\.\d{4}{/literal}">
				</div>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputTags" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Метки:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="tags" id="inputTags" class="form-control tagsinput" value="{$item['tags']}">
				{if !empty($poptags)}
					<div class="mt-1">
						{foreach from=$poptags item=tag}
							<a href="#" name="assdag" class="addtag btn btn-sm btn-outline-primary" data-value="{$tag['title']}">{$tag['title']}</a>
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
						<select name="author_id" id="author_id" class="selectpicker" required data-size="auto" data-live-search="true" data-width="100%">
							<option value="0">Без автора</option>
							{foreach from=$userlist item=user}
								<option value="{$user['uid']}" {if $user['uid'] == $item['author_id']} selected{/if}>{$user['nickname']}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>

		{if !empty($groups)}
			<div class="form-group row">
				<label for="inputGroupAccess" class="col-md-5 col-lg-4 form-control-plaintext text-right">
					Доступ для групп:
					<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Укажите какие группы пользователей смогут просматривать эту страницу" data-placement="left"></span></small>
				</label>
				<div class="col-md-7 col-lg-8">
					<div class="btn-group-vertical btn-group-toggle roocms-crui" data-toggle="buttons" id="inputGroupAccess">
						{foreach from=$groups item=group}
							<label class="btn btn-light {if isset($gids[$group['gid']])}active{/if}">
								<input type="checkbox" name="gids[]" value="{$group['gid']}" autocomplete="off"{if isset($gids[$group['gid']])} checked{/if}><i class="far fa-fw fa-square"></i> {$group['title']}
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		{/if}

		<div class="form-group row">
			<div class="col-12">
				<label for="brief_item" class="control-label">
					Аннотация:
				</label>
				<textarea id="brief_item" class="form-control ckeditor" name="brief_item" required>{$item['brief_item']}</textarea>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-12">
				<label for="brief_item" class="control-label">
					Полный текст: <small><span class="fas fa-exclamation-triangle fa-fw text-danger" rel="tooltip" title="Обазательно заполнить это поле" data-placement="right"></span></small>
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
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-12">
				<input type="submit" name="update_item" class="btn btn-lg btn-success" value="Сохранить элемент">
			</div>
		</div>
	</div>
</form>

