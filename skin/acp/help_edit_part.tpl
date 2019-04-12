{* Шаблон создания новой страницы в разделе помощи *}
<script type="text/javascript" src="plugin/ckeditor.php"></script>

<div class="card-header">
	Редактируем раздел помощи "{$data['title']}"
</div>
<form method="post" action="{$SCRIPT_NAME}?act=help&part=edit_part&u={$data['uname']}">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputUname" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				uname:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="uname" id="inputUname" class="form-control" required value="{$data['uname']}">
				<input type="hidden" name="old_uname" id="inputOldUname" class="form-control" required value="{$data['uname']}" readonly>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" spellcheck required value="{$data['title']}">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputSort" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Порядок расположения страницы в структуре:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="number" name="sort" id="inputSort" class="form-control" value="{$data['sort']}">
			</div>
		</div>

		<div class="form-group row">
			<label for="inputStructure" class="col-md-5 col-lg-4 form-control-plaintext text-right">
				Расположение раздела в структуре:
			</label>
			<div class="col-md-7 col-lg-8">
				{if $data['id'] != 1}
				<div class="row">
					<div class="col-12 col-lg-6">
						<select name="parent_id" id="inputStructure" class="selectpicker" required data-header="Структура помощи по сайту" data-size="auto" data-live-search="true" data-width="100%">
							{foreach from=$tree item=p}
								<option value="{$p['id']}" {if $smarty.const.DEBUGMODE}data-subtext="{$p['uname']}"{/if} {if $p['id'] == $data['parent_id']}selected{/if}>{section name=level loop=$p['level']}&middot; {/section} {$p['title']}</option>
							{/foreach}
						</select>
					</div>
				</div>
				{else}
					<p class="text-primary form-control-plaintext">Это корневая страница раздела!</p>
					<input type="hidden" name="parent_id" value="{$data['parent_id']}" readonly>
				{/if}
				<input type="hidden" name="now_parent_id" value="{$data['parent_id']}" readonly>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-12">
				<label for="content" class="control-label">
					Текст: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="HTML разрешен" data-placement="right"></i></small>
				</label>
				<textarea id="content" class="form-control ckeditor" name="content" spellcheck>{$data['content']}</textarea>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-12">
				<input type="submit" name="update_part" class="btn btn-lg btn-success" value="Обновить раздел">
			</div>
		</div>
	</div>
</form>