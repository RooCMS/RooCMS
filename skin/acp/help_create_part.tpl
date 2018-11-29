{* Шаблон создания новой страницы в разделе помощи *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="panel-heading">
	Новый раздел помощи
</div>
<div class="panel-body">
	<form method="post" action="{$SCRIPT_NAME}?act=help&part=create_part" enctype="multipart/form-data" role="form" class="form-horizontal">
		<div class="form-group">
			<label for="inputUname" class="col-md-3 control-label">
				uname:
			</label>
		    	<div class="col-md-9">
				<input type="text" name="uname" id="inputUname" class="form-control" required>
			</div>
		</div>
		<div class="form-group">
			<label for="inputTitle" class="col-md-3 control-label">
				Заголовок:
			</label>
			<div class="col-md-9">
				<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required>
			</div>
		</div>

		<div class="form-group">
			<label for="inputSort" class="col-md-3 control-label">
				Порядок расположения страницы в структуре:
			</label>
			<div class="col-md-9">
				<input type="text" name="sort" id="inputSort" class="form-control" value="0">
			</div>
		</div>

		<div class="form-group">
			<label for="inputStructure" class="col-md-3 control-label">
				Расположение раздела в структуре:
			</label>
			<div class="col-md-9">
				<select name="parent_id" id="inputStructure" class="selectpicker show-tick" required data-header="Структура помощи по сайту" data-size="auto" data-live-search="true" data-width="50%">
					{foreach from=$tree item=p}
						<option value="{$p['id']}" {if $smarty.const.DEBUGMODE}data-subtext="{$p['uname']}"{/if}>{section name=level loop=$p['level']}&middot; {/section} {$p['title']}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12">
				<label for="content" class="control-label">
					Текст: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="HTML разрешен" data-placement="right"></i></small>
				</label>
				<textarea id="content" class="form-control ckeditor" name="content" spellcheck></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-right">
				<input type="submit" name="create_part" class="btn btn-success" value="Создать раздел">
			</div>
		</div>
	</form>
</div>