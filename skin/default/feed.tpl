{* Feed template *}
<div class="row">
	<div class="col-sm-12">
		<h1>
			{$page_title}
			{if !empty($rsslink)}
				<a href="{$rsslink}" class="btn btn-default btn-sm"><span class="fa fa-rss fa-fw"></span></a>
			{/if}
		</h1>
		{if trim($feed['append_info_before']) != ""}
		{$feed['append_info_before']}
		{/if}
	</div>
	<div class="col-sm-9">
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
							<div class="col-sm-12 colheight-md-1 text-truncate overflow-hidden">
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
							<div class="col-xs-9">
								{if !empty($item['tags'])}
									<span class="small">
										{foreach from=$item['tags'] item=tag}
											<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="btn btn-default btn-sm"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}</a>
										{/foreach}
									</span>
								{/if}
							</div>
							<div class="col-xs-3 text-right">
								<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}" class="btn btn-sm btn-primary">Читать <span class="fa fa-chevron-circle-right fa-fw"></span></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		{/foreach}

		{* Pagination *}
		{if isset($pages) && !empty($pages)}
			<ul class="pagination">
				{foreach from=$pages item=page}
					{if isset($smarty.get.pg) && $smarty.get.pg == $page['n']}
						<li class="active"><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}{get_params exclude='page,pg'}">{$page['n']}</a></li>
					{else}
						{if !isset($smarty.get.pg) && $page['n'] == "1"}
							<li class="active"><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}{get_params exclude='page,pg'}">{$page['n']}</a></li>
						{else}
							<li><a href="{$SCRIPT_NAME}?page={$feed['alias']}&pg={$page['n']}{get_params exclude='page,pg'}">{$page['n']}</a></li>
						{/if}
					{/if}
				{/foreach}
			</ul>
		{/if}
	</div>
	<div class="col-sm-3 text-center">
		<div class="panel panel-default">
			<div class="panel-heading">
				Свежие публикации
			</div>
			<div class="panel-body" style="padding: 0;">
				{$module->load('last_feed')}
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				Самое популярное
			</div>
			<div class="panel-body" style="padding: 0;">
				{$module->load('popular_feed')}
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				Метки
			</div>
			<div class="panel-body">
				{$module->load('tag_cloud')}
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				QR Code
			</div>
			<div class="panel-body">
				<img src="qrcode.php?url={$smarty.server.REQUEST_URI}" class="img-thumbnail" alt="QR ссылка на эту страницу">
			</div>
		</div>
	</div>
	{if trim($feed['append_info_after']) != ""}
	<div class="col-sm-12">
		{$feed['append_info_after']}
	</div>
	{/if}
</div>
