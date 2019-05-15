{* Template Structure Tree *}
<div class="card-header">
	Структура сайта
</div>

<table class="table table-hover d-none d-sm-table mb-0">
	<thead class="bg-light">
		<tr class="active">
			<th width="3%">ID</th>
			<th width="55%">Название <small>alias</small></th>
			<th width="10%" class="text-center">Тип</th>
			<th class="text-right">Опции</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$tree item=page}
		<tr class="nav-{if $page['nav']}on{else}off{/if}{if $page['noindex']} noindex{/if}">
			<td class="align-middle text-muted text-right small text-nowrap">{$page['id']}<i class="fa fa-fw fa-caret-right"></i></td>
			<td class="align-middle">
				<span class="text-nowrap">
					{section name=foo start=1 loop=$page['level'] step=1}<span class="text-muted d-none d-md-inline-block">&bull;&emsp;</span>{/section}

					{if $page['page_type'] == "feed"}
						<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}">{$page['title']}</a>
					{else}
						<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}">{$page['title']}</a>
					{/if}
				</span>
				<small class="float-right">
					{if !isset($page['group_access'][0])}<i class="fas fa-fw fa-user-secret" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Есть групповые ограничения"></i>{else}<i class="fa fa-fw"></i>{/if}
					{if $page['noindex']}<i class="far fa-fw fa-eye-slash" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Неиндексируется поисковыми системами"></i>{else}<i class="fa fa-fw"></i>{/if}
					{if $page['nav']}<i class="fas fa-fw fa-globe" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Страница отражается в навигации"></i>{else}<i class="fa fa-fw"></i>{/if}
				</small>
				<small class="tralias float-right">{$page['alias']}</small>
			</td>
			<td class="text-left">
				<span class="badge badge-secondary">{$content_types[$page['page_type']]}{if $page['rss']} <i class="fas fa-fw fa-rss"></i>{/if}</span>

				{if $page['page_type'] == "feed"}
					<span class="badge badge-info">{$page['items']} эл.</span>
				{/if}
			</td>
			<td class="text-right">
				<div class="btn-group">
					{if $page['page_type'] == "feed"}
						<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$page['id']}" class="btn btn-sm btn-outline-primary" rel="tooltip" data-toggle="tooltip" data-placement="left" data-container="body" title="Настройки ленты"><i class="fa fa-fw fa-cog"></i></a>
					{/if}
					<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit fa-fw"></i><span class="d-none d-md-inline-block">Редактировать</span></a>
					{if $page['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt fa-fw"></i><span class="d-none d-md-inline-block">Удалить</span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<table class="table table-hover d-block-table d-sm-none mb-0">
	<tbody>
	{foreach from=$tree item=page}
		<tr class="nav-{if $page['nav']}on{else}off{/if}{if $page['noindex']} noindex{/if}">
			<td class="align-middle">
				{if $page['page_type'] == "html" or $page['page_type'] == "php"}
					<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}">{$page['title']}</a>
				{else}
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}">{$page['title']}</a>
				{/if}
			</td>
			<td class="w-25 align-middle text-right">
				<div class="btn-group btn-group-sm">
					<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit fa-fw"></i></a>
					{if $page['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt fa-fw"></i></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>