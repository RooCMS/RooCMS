{* Форма загрузки изображений *}
<p>
	Загрузить изображение: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Можно загружать изображения форматов: {foreach from=$allow_images_type item=type name=itype}{$type['ext']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}" data-placement="right"></span></small>
    <span class="btn btn-info btn-xs" id="addimg"><span class="fa fa-fw fa-upload"></span> добавить ещё изображение</span>
    <div id="moreimages">
		<input type="file" name="images[]" class="btn btn-default" size="50" accept="{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}">
	</div>

	{literal}
	<script>
		$(document).ready(function() {
			$('#addimg').click(function() {
				$('<input type="file" name="images[]" size="50" class="btn btn-default" accept="{/literal}{foreach from=$allow_images_type item=type name=itype}{$type['mime_type']}{if !$smarty.foreach.itype.last},{/if}{/foreach}{literal}">').appendTo('#moreimages');
			});
		});
	</script>
	{/literal}
</p>

