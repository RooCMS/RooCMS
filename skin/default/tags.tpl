{* Tags View Template *}
<div class="container">
	<div class="row">
		<div class="col-12">
			<h1 class="text-capitalize">
				<i class="fa fa-fw fa-tag fa-va"></i>
				{$tag['title']}
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-9">
			{foreach from=$feeds item=item}
				<div class="card" id="item-id-{$item['id']}">
					<div class="row no-gutters">
						{if isset($item['image'][0])}
							<div class="col-md-5 col-xl-4">
								{foreach from=$item['image'] item=image}
									<a href="{$SCRIPT_NAME}?page={$item['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}" title="{$item['title']}"><img src="upload/images/{$image['thumb']}" class="card-img" alt="{$image['alt']}"></a>
								{/foreach}
							</div>
						{/if}
						<div class="col-md-{if isset($item['image'][0])}7{else}12{/if} col-xl-{if isset($item['image'][0])}8{else}12{/if}{if isset($item['image'][0])} roocms-feedbrief-mh{/if}">
							<div class="card-body d-flex flex-column mh-100 h-100">
								<h5 class="card-title mb-1"><a href="{$SCRIPT_NAME}?page={$item['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}">{$item['title']}</a></h5>
								<div class="card-text mb-1 small">
									<i class="fas fa-fw fa-folder"></i> <a href="{$SCRIPT_NAME}?page={$item['alias']}">{$item['feed_title']}</a>
								</div>
								<div class="card-text flex-shrink-1 position-relative overflow-hidden">
									{$item['brief_item']}
									{if isset($item['image'][0])}<div class="roocms-feedbrief-layer"></div>{/if}
								</div>
								<div class="card-text small mt-auto">
									{if !empty($item['tags'])}
										{foreach from=$item['tags'] item=tag}
											<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="text-extra">#{$tag['title']}</a>
										{/foreach}
										<br /><br />
									{/if}

									<i class="fas fa-calendar" title="Дата публикации"></i> {$item['datepub']}
									{if $item['views'] != 0}<i class="fas fa-fw fa-eye" title="Просмотрено раз"></i> {$item['views']}{/if}
									{if $item['author_id'] != 0} <i class="fas fa-fw fa-user-circle" title="Автор"></i> {$authors[$item['author_id']]['nickname']}{/if}

									<a href="{$SCRIPT_NAME}?page={$item['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['id']}" class="text-secondary text-uppercase float-right">Читать<span class="fas fa-chevron-circle-right fa-fw"></span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			{/foreach}

			{* Pagination *}
			{if isset($pages) && !empty($pages)}
				<ul class="pagination justify-content-center my-3">
					{foreach from=$pages item=page}
						{if isset($smarty.get.pg) && $smarty.get.pg == $page['n']}
							<li class="page-item active"><a href="{$SCRIPT_NAME}?part=tags&pg={$page['n']}{get_params exclude='part,pg'}" class="page-link">{$page['n']}</a></li>
						{else}
							{if !isset($smarty.get.pg) && $page['n'] == "1"}
								<li class="page-item active"><a href="{$SCRIPT_NAME}?part=tags&pg={$page['n']}{get_params exclude='part,pg'}" class="page-link">{$page['n']}</a></li>
							{else}
								<li class="page-item"><a href="{$SCRIPT_NAME}?part=tags&pg={$page['n']}{get_params exclude='part,pg'}" class="page-link">{$page['n']}</a></li>
							{/if}
						{/if}
					{/foreach}
				</ul>
			{/if}
		</div>
		<div class="col-lg-3">
			<div class="card">
				<div class="card-header">
					Метки
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-body">
					{$module->load('tag_cloud')}
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					Последние новости
				</div>
			</div>
			{$module->load('last_feed')}
			<div class="card mt-3">
				<div class="card-header">
					QR
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-body text-center">
					<img src="qrcode.php?url={$smarty.server.REQUEST_URI}" class="img-thumbnail border-0" alt="QR ссылка на эту страницу">
				</div>
			</div>
		</div>
	</div>
</div>
