{* Images Upload Form *}
<p>
	Загрузить изображение: <small><span class="fas fa-question-circle fa-fw" rel="tooltip" title="Можно загружать изображения форматов: {foreach from=$allow_images_type item=type name=itype}{$type['ext']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}" data-placement="right"></span></small>
	<span class="btn btn-outline-secondary btn-sm" id="addimg"><span class="fas fa-fw fa-upload"></span> добавить поле для загрузки изображений</span>
	<div class="custom-file mb-3">
		<input type="file" name="images[]" class="custom-file-input" id="attached_image" multiple size="50" accept="{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}">
		<label class="custom-file-label" for="attached_image">Выберите изображения</label>
	</div>
	<div id="moreimages">
	</div>
</p>
{literal}
<script>
	$(document).ready(function() {
		var i = 1;
		$('#addimg').on("click", function() {
			var ifuc = '<div class="custom-file mb-3">\n' +
				'\t\t<input type="file" name="images[]" class="custom-file-input" id="attached_image_'+i+'" multiple size="50" accept="{/literal}{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}{literal}">\n' +
				'\t\t<label class="custom-file-label" for="attached_image_'+i+'">Выберите изображения</label>\n' +
				'\t</div>';
				i++;
			$(ifuc).appendTo('#moreimages');
		});
	});
</script>
{/literal}


