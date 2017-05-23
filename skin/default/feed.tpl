{* Шаблон ленты *}

{if !empty($rsslink)}
	<a href="{$rsslink}" class="btn btn-warning btn-xs pull-right"><span class="fa fa-rss fa-fw"></span>RSS 2.0</a>
{/if}

<h1>{$page_title}</h1>

{foreach from=$feeds item=item}
	<div class="panel panel-default">
		<div class="panel-body">
			<div id="item-id-{$item['id']}">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="feed-title">
							<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}">{$item['title']}</a>
						</h3>
						<div class="feed-date small">
							<i class="fa fa-calendar" title="Дата публикации"></i> {$item['datepub']}
							{if $item['views'] != 0}<i class="fa fa-fw fa-eye" title="Просмотрено раз"></i> {$item['views']}{/if}
							{if $item['author_id'] != 0} <i class="fa fa-fw fa-user-circle-o" title="Автор"></i> {$authors[$item['author_id']]['nickname']}{/if}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						{if isset($item['image'][0])}
							{foreach from=$item['image'] item=image}
								<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}"><img src="upload/images/{$image['thumb']}" border="0" alt="{$image['alt']}" class="img-thumbnail feed-image-prev"></a>
							{/foreach}
						{/if}
						{$item['brief_item']}
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-sm-6">
						{if !empty($item['tags'])}
							<span class="small">
								{foreach from=$item['tags'] item=tag}
									<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="btn btn-default btn-xs"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}</a>
								{/foreach}
							</span>
						{/if}
					</div>
					<div class="col-sm-6 text-right">
						<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}" class="btn btn-sm btn-primary">Читать полностью <span class="fa fa-chevron-circle-right fa-fw"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
{/foreach}


{if isset($pages) && !empty($pages)}
	<ul class="pagination">
		{foreach from=$pages item=page}
			{if isset($page['prev'])}
				<li><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['prev']}{get_params prefix='&amp;' exclude='page,pg'}">&larr;</a></li>
			{elseif isset($page['next'])}
				<li><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['next']}{get_params prefix='&amp;' exclude='page,pg'}">&rarr;</a></li>
			{else}
				{if isset($smarty.get.pg) && $smarty.get.pg == $page['n']}
					<li class="active"><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}{get_params prefix='&amp;' exclude='page,pg'}">{$page['n']}</a></li>
				{else}
					{if !isset($smarty.get.pg) && $page['n'] == "1"}
						<li class="active"><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}{get_params prefix='&amp;' exclude='page,pg'}">{$page['n']}</a></li>
					{else}
						<li><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}{get_params prefix='&amp;' exclude='page,pg'}">{$page['n']}</a></li>
					{/if}
				{/if}
			{/if}
		{/foreach}
	</ul>
{/if}

{$module->load("tagcloud")}
