{* Files Upload from *}
<p>
	Загрузить файлы: <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Можно загружать файлы форматов: {foreach from=$allow_files_type item=type name=itype}{$type['ext']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}" data-placement="right"></i></small>
	<span class="btn btn-outline-secondary btn-sm" id="addfiles"><i class="fa fa-fw fa-upload"></i> добавить поле для загрузки файлов</span>

	<div class="custom-file mb-3">
		<input type="file" name="files[]" class="custom-file-input" id="attachedFile" multiple accept="{foreach from=$allow_files_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}">
		<label class="custom-file-label" for="attachedFile" data-browse="Выбрать">Выберите файл(ы)</label>
	</div>
	<div id="morefiles">
	</div>
</p>
{literal}
<script>
	$(document).ready(function() {
		var f = 1;
		$('#addfiles').on("click", function() {
			var ifuc = '<div class="custom-file mb-3">\n' +
				'\t\t<input type="file" name="files[]" class="custom-file-input" id="attached_files_'+f+'" multiple accept="{/literal}{foreach from=$allow_files_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}{literal}">\n' +
				'\t\t<label class="custom-file-label" for="attached_files_'+f+'" data-browse="Выбрать">Выберите файл(ы)</label>\n' +
				'\t</div>';
				f++;
			$(ifuc).appendTo('#morefiles');
		});
	});
</script>
{/literal}


