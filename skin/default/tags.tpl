{* Шаблон тегов *}

<h1>
	<i class="fa fa-fw fa-flip-horizontal fa-tag"></i>
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
				<div class="row">
					<div class="col-sm-6">
						{if !empty($item['tags'])}
							<span class="small">
								{foreach from=$item['tags'] item=tag}
									<i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}
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


{if isset($pages) && !empty($pages)}
	<ul class="pagination">
		{foreach from=$pages item=page}
			{if isset($page['prev'])}
				<li><a href="{$SCRIPT_NAME}?part=tags{get_params prefix='&amp;' exclude='part,pg'}&pg={$page['prev']}">&larr;</a></li>
			{elseif isset($page['next'])}
				<li><a href="{$SCRIPT_NAME}?part=tags{get_params prefix='&amp;' exclude='part,pg'}&pg={$page['next']}">&rarr;</a></li>
			{else}
				{if isset($smarty.get.pg) && $smarty.get.pg == $page['n']}
					<li class="active"><a href="{$SCRIPT_NAME}?part=tags{get_params prefix='&amp;' exclude='part,pg'}&pg={$page['n']}">{$page['n']}</a></li>
				{else}
					{if !isset($smarty.get.pg) && $page['n'] == "1"}
						<li class="active"><a href="{$SCRIPT_NAME}?part=tags{get_params prefix='&amp;' exclude='part,pg'}&pg={$page['n']}">{$page['n']}</a></li>
					{else}
						<li><a href="{$SCRIPT_NAME}?part=tags{get_params prefix='&amp;' exclude='part,pg'}&pg={$page['n']}">{$page['n']}</a></li>
					{/if}
				{/if}
			{/if}
		{/foreach}
	</ul>
{/if}
