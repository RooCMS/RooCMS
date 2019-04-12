{* Шаблон создания новой страницы в разделе помощи *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="card-header">
	Новый раздел помощи
</div>
<form method="post" action="{$SCRIPT_NAME}?act=help&part=create_part">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputUname" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				uname:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="uname" id="inputUname" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required>
			</div>
		</div>

		<div class="form-group row">
			<label for="inputSort" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Порядок расположения страницы в структуре:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="number" name="sort" id="inputSort" class="form-control" value="0">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputStructure" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Расположение раздела в структуре:
			</label>
			<div class="col-md-7 col-lg-8">
				<div class="row">
					<div class="col-12 col-lg-6">
						<select name="parent_id" id="inputStructure" class="selectpicker" required data-header="Структура помощи по сайту" data-size="auto" data-live-search="true" data-width="100%">
							{foreach from=$tree item=p}
								<option value="{$p['id']}" {if $smarty.const.DEBUGMODE}data-subtext="{$p['uname']}"{/if}>{section name=level loop=$p['level']}&middot; {/section} {$p['title']}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-12">
				<label for="content" class="control-label">
					Текст: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="HTML разрешен" data-placement="right"></i></small>
				</label>
				<textarea id="content" class="form-control ckeditor" name="content" spellcheck></textarea>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-12">
				<input type="submit" name="create_part" class="btn btn-lg btn-success" value="Создать раздел">
			</div>
		</div>
	</div>
</form>