{* Шаблон отображения элемента ленты *}
<div id="content" class="container">
	<div id="item_{$item['id']}">
		<h2>{$item['title']}</h2>
		<small>{$item['datep']}</small>
		<div class="row-fluid">{$item['full_item']}</div>
		{* Шаблон отображения картинок в элементах ленты *}
		<div class="center">
			{foreach from=$images item=img}
				<a href="/upload/images/resize/{$img['filename']}" rel="img"><img src="/upload/images/thumb/{$img['filename']}" class="img-polaroid" style="margin: 3px 0px;"></a>
			{/foreach}
		</div>
		<div class="pull-right"><a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}" class="btn btn-link"><span class="icon-arrow-left"></span> Вернуться</a></div>
		<div class="clearfix"></div>
	</div>
</div>
