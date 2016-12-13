{* Шаблон отображения элемента ленты *}
<div id="item_{$item['id']}">
	<h1>{$item['title']}</h1>
	<small><i class="fa fa-fw fa-calendar"></i> {$item['datepub']}</small>
	<hr>
	{$item['full_item']}

	{* Шаблон отображения картинок в элементах ленты *}
	{if !empty($images)}
	<div class="text-center">
		{foreach from=$images item=img}
			<a href="/upload/images/{$img['resize']}" rel="img" title="{$item['title']}"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" style="margin: 3px 0px;" alt="{$item['title']}"></a>
		{/foreach}
	</div>
	{/if}
	{if !empty($attachfile)}
		<div class="text-left">
			<strong>Файлы:</strong>
			{foreach from=$attachfile item=file}
				<br /><a href="/upload/files/{$file['file']}" class="btn btn-xs btn-default"><i class="fa fa-fw fa-download"></i> {$file['file']}</a>
			{/foreach}
		</div>
	{/if}
	<div class="text-right">
		<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}" class="btn btn-xs btn-primary"><span class="fa fa-chevron-circle-left fa-fw"></span> Вернуться</a>
	</div>
</div>