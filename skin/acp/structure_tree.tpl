{* Шаблон отображения структуры сайта *}
<div class="panel-heading">
	Структура сайта
</div>

<table class="table table-hover table-condensed hidden-xs">
	<thead>
		<tr class="active">
			<th width="3%">ID</th>
			<th width="56%">Название <small>alias</small></th>
			<th width="11%" class="text-center">Тип</th>
			<th width="30%" class="text-right">Опции</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$tree item=page}
		<tr>
			<td class="text-muted structure-highlight text-right"><nobr>{$page['id']}<i class="fa fa-fw fa-caret-right"></i></nobr></td>
			<td>
				<nobr>
					{section name=foo start=1 loop=$page['level'] step=1}<span class="text-muted structure-highlight">&bull;</span>&emsp;{/section}

					{if $page['page_type'] == "html" or $page['page_type'] == "php"}
						<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}">{$page['title']}</a>
					{else}
						<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}">{$page['title']}</a>
					{/if}
				</nobr>
				<small class="trinfo">
					{if !isset($page['group_access'][0])}<i class="fa fa-fw fa-user-secret" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Есть ограничения доступа"></i>{else}<i class="fa fa-fw"></i>{/if}
					{if $page['noindex']}<i class="fa fa-fw fa-eye-slash" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Неиндексируется поисковыми системами"></i>{else}<i class="fa fa-fw"></i>{/if}
				</small>
				<small class="tralias">{$page['alias']}</small>
			</td>
			<td class="text-left">
				<span class="label label-default">{$content_types[$page['page_type']]}</span>

				{if $page['page_type'] == "feed"}
					<span class="label label-info">{$page['items']} эл.</span>
					{if $page['rss']}<span class="label label-warning"><i class="fa fa-fw fa-rss"></i></span>{/if}
				{/if}
			</td>
			<td class="text-right">
				<div class="btn-group">
					{if $page['page_type'] == "feed"}
						<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$page['id']}" class="btn btn-xs btn-default"><i class="fa fa-fw fa-cog" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Настройки ленты"></i></a>
					{/if}
					<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
					{if $page['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<ul class="list-group visible-xs">
	{foreach from=$tree item=page}
		<li class="list-group-item no-overflow">

			{if $page['page_type'] == "html" or $page['page_type'] == "php"}
				<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}">{$page['title']}</a>
			{else}
				<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}">{$page['title']}</a>
			{/if}

			<div class="pull-right">
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span></a>
					{if $page['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span></a>{/if}
				</div>
			</div>
		</li>
	{/foreach}
</ul>