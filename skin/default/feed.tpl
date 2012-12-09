{* Шаблон ленты *}
<div id="content" class="container">
	<div class="row-fluid">
	{if !empty($rsslink)}
		<div class="pull-right">
			<a href="{$rsslink}" class="btn" style="vertical-align: middle;text-decoration: none;"><img src="{$SKIN}/img/misc/rss.gif" border="0" style="vertical-align: middle;"> <u>RSS 2.0</u> </a>
		</div>
	{/if}
    <h2>{$page_title}</h2>
	{foreach from=$feeds item=item}
		<div id="item_{$item['id']}" class="clearfix">
			<h4><a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}">{$item['title']}</a></h4>
			<small>{$item['datep']}</small>
			<div class="row-fluid">
				{if isset($item['image'][0])}
					{foreach from=$item['image'] item=image}
					<div class="span2">
						<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}"><img src="upload/images/thumb/{$image['filename']}" width="140" border="0" alt="{$image['alt']}" class="img-polaroid"></a>
					</div>
					{/foreach}
				{/if}
				<div class="span1{if isset($item['image'][0])}0{else}2{/if} justify">
					{$item['brief_item']}
				</div>
				<div style="clear: both;"></div>
			</div>
			<div class="pull-right"><a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}" class="btn btn-link">Читать полностью <span class="icon-arrow-right"></span></a></div>
		</div>
	{/foreach}

	{if isset($pages) && !empty($pages)}
	<div class="pagination">
		<ul>
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
	</div>
	{/if}

	</div>
</div>
