{* Шаблон ленты *}
<div class="row">
	<div class="col-md-{if !empty($rsslink)}11{else}12{/if}">
		<h2>{$page_title}</h2>
	</div>
	{if !empty($rsslink)}
	<div class="col-md-1 text-right">
		<a href="{$rsslink}" class="btn btn-warning btn-xs"><span class="icon-rss icon-fixed-width"></span>RSS 2.0</a>
	</div>
	{/if}
</div>


	{foreach from=$feeds item=item}
	<div id="item-id-{$item['id']}">
	<div class="row">
		<div class="col-sm-12">
			<h3><a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}">{$item['title']}</a></h3>
			<small>Опубликовано: {$item['datep']}</small>
		</div>
	</div>
	<div class="row">
		{if isset($item['image'][0])}
			{foreach from=$item['image'] item=image}
			<div class="col-sm-2 text-center">
				<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}"><img src="upload/images/{$image['thumb']}" border="0" alt="{$image['alt']}" class="img-thumbnail"></a>
			</div>
			{/foreach}
		{/if}
		<div class="col-sm-1{if isset($item['image'][0])}0{else}2{/if}">
			{$item['brief_item']}
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 text-right">
			<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}" class="btn btn-xs btn-primary">Читать полностью <span class="icon-chevron-sign-right icon-fixed-width"></span></a>
		</div>
	</div>
	</div>
	{/foreach}

{if isset($pages) && !empty($pages)}
<ul class="pagination">
{foreach from=$pages item=page}
	{if isset($page['prev'])}
		<li><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['prev']}">&larr;</a></li>
	{elseif isset($page['next'])}
		<li><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['next']}">&rarr;</a></li>
	{else}
		{if isset($smarty.get.pg) && $smarty.get.pg == $page['n']}
			<li class="active"><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}">{$page['n']}</a></li>
		{else}
			{if !isset($smarty.get.pg) && $page['n'] == "1"}
				<li class="active"><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}">{$page['n']}</a></li>
			{else}
				<li><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}">{$page['n']}</a></li>
			{/if}
		{/if}
	{/if}
{/foreach}
</ul>
{/if}
