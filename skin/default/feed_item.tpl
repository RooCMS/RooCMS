{* Шаблон отображения элемента ленты *}
	<div id="item_{$item['id']}">
		<h2>{$item['title']}</h2>
		<small>Опубликовано: {$item['datep']}</small>
		{$item['full_item']}

		{* Шаблон отображения картинок в элементах ленты *}
		<div class="text-center">
			{foreach from=$images item=img}
				<a href="/upload/images/{$img['resize']}" rel="img" title="{$item['title']}"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" style="margin: 3px 0px;" alt="{$item['title']}"></a>
			{/foreach}
		</div>
		<div class="text-right">
			<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}" class="btn btn-xs btn-primary"><span class="icon-chevron-sign-left icon-fixed-width"></span> Вернуться</a>
		</div>
	</div>
