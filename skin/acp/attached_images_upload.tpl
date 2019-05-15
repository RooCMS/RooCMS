{* Images Upload Form *}

Загрузить изображение: <small><span class="fas fa-question-circle fa-fw" rel="tooltip" title="Можно загружать изображения форматов: {foreach from=$allow_images_type item=type name=itype}{$type['ext']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}" data-placement="right"></span></small>


<div class="custom-file mb-3">
	<input type="file" name="images[]" class="custom-file-input" id="attachedImage" multiple accept="{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}">
	<label class="custom-file-label" for="attachedImage" data-browse="Выбрать">Выберите изображения</label>
</div>

<div id="moreimages">
</div>
