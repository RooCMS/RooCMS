{* Шаблон создания новой страницы в разделе помощи *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="panel-heading">
	Редактируем раздел помощи "{$data['title']}"
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=help&part=edit_part&u={$data['uname']}" enctype="multipart/form-data" role="form" class="form-horizontal">
		<div class="form-group">
			<label for="inputUname" class="col-md-3 control-label">
				uname:
			</label>
			<div class="col-md-9">
				<input type="text" name="uname" id="inputUname" class="form-control" required value="{$data['uname']}">
				<input type="hidden" name="old_uname" id="inputOldUname" class="form-control" required value="{$data['uname']}">
			</div>
		</div>
		<div class="form-group">
			<label for="inputTitle" class="col-md-3 control-label">
				Заголовок:
			</label>
			<div class="col-md-9">
				<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required value="{$data['title']}">
			</div>
		</div>

		<div class="form-group">
			<label for="inputSort" class="col-md-3 control-label">
				Порядок расположения страницы в структуре:
			</label>
			<div class="col-md-9">
				<input type="number" name="sort" id="inputSort" class="form-control" value="{$data['sort']}">
			</div>
		</div>

		<div class="form-group">
			<label for="inputStructure" class="col-md-3 control-label">
				Расположение раздела в структуре:
			</label>
			<div class="col-md-9">
				{if $data['id'] != 1}
					<select name="parent_id" id="inputStructure" class="selectpicker show-tick" required data-header="Структура помощи по сайту" data-size="auto" data-live-search="true" data-width="50%">
						{foreach from=$tree item=p}
							<option value="{$p['id']}" {if $smarty.const.DEBUGMODE}data-subtext="{$p['uname']}"{/if} {if $p['id'] == $data['parent_id']}selected{/if}>{section name=level loop=$p['level']}&middot; {/section} {$p['title']}</option>
						{/foreach}
					</select>
				{else}
					<p class="text-primary form-control-static">Это корневая страница раздела!</p>
					<input type="hidden" name="parent_id" value="{$data['parent_id']}" readonly>
				{/if}
				<input type="hidden" name="now_parent_id" value="{$data['parent_id']}" readonly>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12">
				<label for="content" class="control-label">
					Текст: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="HTML разрешен" data-placement="right"></i></small>
				</label>
				<textarea id="content" class="form-control ckeditor" name="content" spellcheck>{$data['content']}</textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-right">
				<input type="submit" name="update_part" class="btn btn-success" value="Обновить раздел">
			</div>
		</div>
	</form>
</div>