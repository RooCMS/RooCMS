{* Шаблон тегов *}
<div class="row">
	<div class="col-sm-9">
		<h1 class="text-capitalize">
			<i class="fa fa-fw fa-tag fa-va"></i>
			{$tag['title']}
		</h1>

		{foreach from=$feeds item=item}
			<div class="panel panel-default">
				<div class="panel-body">
					<div id="item-id-{$item['id']}">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="feed-title">
									<a href="{$SCRIPT_NAME}?page={$item['alias']}&id={$item['id']}">{$item['title']}</a>
								</h3>
								<div class="feed-date small">
									<i class="fa fa-calendar"></i> {$item['datepub']}
									{if $item['views'] != 0}<i class="fa fa-fw fa-eye" title="Просмотрено раз"></i> {$item['views']}{/if}
									{if $item['author_id'] != 0} <i class="fa fa-fw fa-user-circle-o" title="Автор"></i> {$authors[$item['author_id']]['nickname']}{/if}
									<a href="{$SCRIPT_NAME}?page={$item['alias']}"><i class="fa fa-fw fa-folder"></i>{$item['feed_title']}</a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 colheight-md-1 no-overflow">
								{if isset($item['image'][0])}
									{foreach from=$item['image'] item=image}
										<a href="{$SCRIPT_NAME}?page={$item['alias']}&id={$item['id']}"><img src="upload/images/{$image['thumb']}" border="0" alt="{$image['alt']}" class="img-thumbnail feed-image-prev"></a>
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
											<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="btn btn-default btn-xs"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}</a>
										{/foreach}
									</span>
								{/if}
							</div>
							<div class="col-xs-3 text-right">
								<a href="{$SCRIPT_NAME}?page={$item['alias']}&id={$item['id']}" class="btn btn-sm btn-primary">Читать полностью <span class="fa fa-chevron-circle-right fa-fw"></span></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		{/foreach}


		{if isset($pages) && !empty($pages)}
			<ul class="pagination">
				{foreach from=$pages item=page}
					{if isset($smarty.get.pg) && $smarty.get.pg == $page['n']}
						<li class="active"><a href="{$SCRIPT_NAME}?part=tags{get_params exclude='part,pg'}&pg={$page['n']}">{$page['n']}</a></li>
					{else}
						{if !isset($smarty.get.pg) && $page['n'] == "1"}
							<li class="active"><a href="{$SCRIPT_NAME}?part=tags{get_params exclude='part,pg'}&pg={$page['n']}">{$page['n']}</a></li>
						{else}
							<li><a href="{$SCRIPT_NAME}?part=tags{get_params exclude='part,pg'}&pg={$page['n']}">{$page['n']}</a></li>
						{/if}
					{/if}
				{/foreach}
			</ul>
		{/if}
	</div>
	<div class="col-sm-3 text-center" style="padding-top:60px;">
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
</div>
