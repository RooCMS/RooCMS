{* Шаблон результатов поиска *}

<h1>
	Поиск: {$searchstring}
</h1>

{foreach from=$result item=item}
	<div class="panel panel-default">
		<div class="panel-body">
			<div id="item-id-{$item['id']}">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="feed-title">
							<a href="{$SCRIPT_NAME}?page={$item['alias']}&id={$item['id']}">{$item['title']}</a>
						</h3>
						<div class="feed-date small">
							<a href="{$SCRIPT_NAME}?page={$item['alias']}"><i class="fa fa-folder"></i> {$item['feed_title']}</a>
							<i class="fa fa-calendar"></i> {$item['datepub']}
							{if $item['views'] != 0}<i class="fa fa-fw fa-eye" title="Просмотрено раз"></i> {$item['views']}{/if}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
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
						<a href="{$SCRIPT_NAME}?page={$item['alias']}&id={$item['id']}" class="btn btn-sm btn-primary">Читать полностью <span class="fa fa-chevron-circle-right fa-fw"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
{/foreach}