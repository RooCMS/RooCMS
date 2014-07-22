{* Форма загрузки изображений *}
<p>
	Загрузить изображение: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Можно загружать изображения форматов: {foreach from=$allow_images_type item=type name=itype}{$type['ext']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}" data-placement="right"></span></small>
	<span class="btn btn-info btn-xs" id="addimg"><span class="fa fa-fw fa-upload"></span> добавить ещё поле для загрузки изображений</span>
	<div id="moreimages">
		<input type="file" name="images[]" class="btn btn-default" multiple size="50" accept="{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}">
	</div>
	<small>
		Если вы не можете загрузить через поле больше одного файла, попробуйте включить мультизагрузку посредством Ajax в настройках RooCMS.
	</small>
</p>
{literal}
<script>
	$(document).ready(function() {
		$('#addimg').click(function() {
			$('<input type="file" name="images[]" size="50" class="btn btn-default" accept="{/literal}{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}{literal}">').appendTo('#moreimages');
		});
	});
</script>
{/literal}


