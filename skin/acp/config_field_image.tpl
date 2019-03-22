{if isset($image['src']) && $image['src'] != ""}
	<span id="ci-{$field['name']}" class="d-inline position-relative">
		<img src="/upload/images/{$image['src']}" height="45" alt="" rel="tooltip" title="Размеры: {$image['width']}px на {$image['height']}px" data-placement="top">
		<i id="dci-{$field['name']}" class="fas fa-fw fa-times-circle fa-icon-action del" rel="tooltip" title="Удалить изображение" data-placement="top"></i>
	</span>
{/if}
<div class="custom-file w-75 ml-2 mb-3">
	<input type="file" name="image_{$field['name']}" class="custom-file-input" id="input_{$field['name']}" accept="{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}">
	<label class="custom-file-label" for="input_{$field['name']}" data-browse="Выбрать">Выберите изображение</label>
</div>
<input type="hidden" name="{$field['name']}" value="{$field['value']}" readonly>