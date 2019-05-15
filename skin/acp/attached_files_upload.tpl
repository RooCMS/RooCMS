{* Files Upload from *}

Загрузить файлы: <small><i class="fas fa-question-circle fa-fw" rel="tooltip" title="Можно загружать файлы форматов: {foreach from=$allow_files_type item=type name=itype}{$type['ext']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}" data-placement="right"></i></small>

<div class="custom-file mb-3">
	<input type="file" name="files[]" class="custom-file-input" id="attachedFile" multiple accept="{foreach from=$allow_files_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}">
	<label class="custom-file-label" for="attachedFile" data-browse="Выбрать">Выберите файл(ы)</label>
</div>
